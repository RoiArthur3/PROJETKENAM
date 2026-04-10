<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FacialDevice;
use App\Models\FacialEvent;
use App\Models\Personnel;
use App\Models\Pointage;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class FacialAttendanceIngestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $normalized = $this->normalizeIncomingPayload($request);

        $validator = Validator::make($normalized, [
            'device_serial' => 'nullable|string|max:100',
            'employee_code' => 'required|string|max:100',
            'employee_name' => 'nullable|string|max:255',
            'event_time' => 'nullable|date',
            'event_type' => 'nullable|string|max:50',
            'direction' => 'nullable|string|in:entry,exit,unknown',
            'status' => 'nullable|string|max:30',
            'confidence' => 'nullable|numeric|min:0|max:100',
            'payload' => 'nullable|array',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Payload invalide',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $token = (string) $request->header('X-Device-Token', '');

        $device = null;
        if (!empty($validated['device_serial'])) {
            $device = FacialDevice::where('serial_number', $validated['device_serial'])
                ->where('is_active', true)
                ->first();
        }

        if (!$device && $token !== '') {
            // Some terminals do not include serial in webhook payloads.
            $device = FacialDevice::where('api_token', $token)
                ->where('is_active', true)
                ->first();
        }

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Terminal inconnu ou inactif',
            ], 401);
        }

        if ($token === '' || !hash_equals((string) $device->api_token, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token terminal invalide',
            ], 401);
        }

        $eventTime = $this->parseEventTime($validated['event_time'] ?? null);
        $direction = $this->normalizeDirection($validated['direction'] ?? null, $validated['event_type'] ?? null);

        if ($this->isDuplicateEvent($device->id, $validated['employee_code'], $eventTime, $direction)) {
            return response()->json([
                'success' => true,
                'message' => 'Événement déjà reçu (doublon ignoré)',
            ]);
        }

        DB::beginTransaction();
        try {
            $event = FacialEvent::create([
                'facial_device_id' => $device->id,
                'serial_number' => $validated['device_serial'] ?? $device->serial_number,
                'employee_code' => $validated['employee_code'],
                'employee_name' => $validated['employee_name'] ?? null,
                'event_time' => $eventTime,
                'event_type' => $validated['event_type'] ?? 'authentication',
                'direction' => $direction,
                'status' => $validated['status'] ?? 'success',
                'confidence' => $validated['confidence'] ?? null,
                'raw_payload' => $validated['payload'] ?? $request->all(),
                'processing_status' => 'pending',
            ]);

            $syncResult = $this->upsertPointageFromEvent($validated['employee_code'], $eventTime, $direction);
            $pointage = $syncResult['pointage'];

            $event->update([
                'pointage_id' => $pointage?->id,
                'processed_at' => now(),
                'processing_status' => $syncResult['processing_status'],
                'processing_message' => $syncResult['processing_message'],
            ]);

            $device->update([
                'last_seen_at' => now(),
                'last_status' => 'online',
                'last_error' => null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Événement reçu',
                'event_id' => $event->id,
                'pointage_id' => $pointage?->id,
                'sync_action' => $syncResult['sync_action'],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur ingestion événement facial: ' . $e->getMessage(), [
                'payload' => $request->all(),
            ]);

            $device->update([
                'last_status' => 'error',
                'last_error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur pendant le traitement',
            ], 500);
        }
    }

    private function upsertPointageFromEvent(string $employeeCode, Carbon $eventTime, string $direction): array
    {
        if (!Schema::hasTable('personnel') || !Schema::hasTable('pointages')) {
            return [
                'pointage' => null,
                'sync_action' => 'ignored',
                'processing_status' => 'ignored',
                'processing_message' => 'Tables personnel/pointages indisponibles',
            ];
        }

        $personnel = Personnel::where('matricule', $employeeCode)->first();
        if (!$personnel) {
            return [
                'pointage' => null,
                'sync_action' => 'ignored',
                'processing_status' => 'ignored',
                'processing_message' => 'Aucun personnel trouvé pour ce code',
            ];
        }

        $datePointage = $eventTime->toDateString();
        $heure = $eventTime->format('H:i:s');

        $pointage = Pointage::where('personnel_id', $personnel->id)
            ->whereDate('date_pointage', $datePointage)
            ->first();

        if (!$pointage) {
            $basePayload = [
                'personnel_id' => $personnel->id,
                'user_id' => $personnel->user_id,
                'date_pointage' => $datePointage,
                'statut' => 'present',
                'validated_at' => now(),
            ];

            if ($direction === 'exit') {
                $basePayload['heure_depart'] = $heure;
            } else {
                $basePayload['heure_arrivee'] = $heure;
                if (Schema::hasColumn('pointages', 'heure_pointage')) {
                    $basePayload['heure_pointage'] = $eventTime;
                }
            }

            return [
                'pointage' => Pointage::create($this->filterPointagePayload($basePayload)),
                'sync_action' => 'created',
                'processing_status' => 'processed',
                'processing_message' => 'Pointage créé depuis événement facial',
            ];
        }

        if ($direction === 'exit') {
            if (!empty($pointage->heure_depart)) {
                return [
                    'pointage' => $pointage,
                    'sync_action' => 'duplicate_exit',
                    'processing_status' => 'ignored',
                    'processing_message' => 'Sortie déjà enregistrée: doublon ignoré',
                ];
            }

            $updatePayload = [
                'user_id' => $personnel->user_id,
                'statut' => 'present',
                'validated_at' => now(),
            ];
            $updatePayload['heure_depart'] = $heure;
        } else {
            if (!empty($pointage->heure_arrivee)) {
                return [
                    'pointage' => $pointage,
                    'sync_action' => 'duplicate_entry',
                    'processing_status' => 'ignored',
                    'processing_message' => 'Entrée déjà enregistrée: doublon ignoré',
                ];
            }

            $updatePayload = [
                'user_id' => $personnel->user_id,
                'statut' => 'present',
                'validated_at' => now(),
                'heure_arrivee' => $heure,
            ];

            if (Schema::hasColumn('pointages', 'heure_pointage')) {
                $updatePayload['heure_pointage'] = $eventTime;
            }
        }

        $pointage->update($this->filterPointagePayload($updatePayload));

        return [
            'pointage' => $pointage->refresh(),
            'sync_action' => 'updated',
            'processing_status' => 'processed',
            'processing_message' => 'Pointage mis à jour depuis événement facial',
        ];
    }

    private function normalizeIncomingPayload(Request $request): array
    {
        $all = (array) $request->all();
        $payload = (array) ($all['payload'] ?? []);

        $deviceSerial = $this->pickValue([$all, $payload], [
            'device_serial',
            'serial_number',
            'serialNo',
            'deviceSerialNo',
            'X-Device-Serial',
            'device.serial_number',
            'device.serialNo',
            'device.id',
            'terminal.serial',
            'event.device_serial',
            'EventNotificationAlert.deviceID',
            'EventNotificationAlert.serialNo',
            'EventNotificationAlert.ipAddress',
            'EventNotificationAlert.AccessControllerEvent.deviceName',
            'AccessControllerEvent.deviceName',
        ]);

        $employeeCode = $this->pickValue([$all, $payload], [
            'employee_code',
            'employee_id',
            'employeeId',
            'employeeNoString',
            'personnel_code',
            'personnel.matricule',
            'personnel.code',
            'user_code',
            'event.employee_code',
            'event.employeeNoString',
            'AccessControllerEvent.employeeNoString',
            'EventNotificationAlert.AccessControllerEvent.employeeNoString',
            'AccessControllerEvent.cardNo',
            'EventNotificationAlert.AccessControllerEvent.cardNo',
        ]);

        $employeeName = $this->pickValue([$all, $payload], [
            'employee_name',
            'name',
            'person_name',
            'personnel.name',
            'personnel.full_name',
            'event.employee_name',
            'AccessControllerEvent.name',
            'EventNotificationAlert.AccessControllerEvent.name',
        ]);

        $eventTime = $this->pickValue([$all, $payload], [
            'event_time',
            'timestamp',
            'eventTime',
            'time',
            'dateTime',
            'event_time_iso',
            'event.timestamp',
            'event.time',
            'EventNotificationAlert.dateTime',
            'EventNotificationAlert.AccessControllerEvent.dateTime',
            'AccessControllerEvent.dateTime',
        ]);

        $eventType = $this->pickValue([$all, $payload], [
            'event_type',
            'eventType',
            'minor',
            'major',
            'event.type',
            'event.name',
            'EventNotificationAlert.eventType',
            'EventNotificationAlert.eventDescription',
            'EventNotificationAlert.AccessControllerEvent.majorEventType',
            'EventNotificationAlert.AccessControllerEvent.minorEventType',
            'AccessControllerEvent.majorEventType',
            'AccessControllerEvent.minorEventType',
        ], 'authentication');

        $rawDirection = $this->pickValue([$all, $payload], [
            'direction',
            'in_out',
            'inOut',
            'doorDirection',
            'event.direction',
            'event.in_out',
            'EventNotificationAlert.AccessControllerEvent.inAndOutFlag',
            'EventNotificationAlert.AccessControllerEvent.attendanceStatus',
            'AccessControllerEvent.inAndOutFlag',
            'AccessControllerEvent.attendanceStatus',
        ]);

        $status = $this->pickValue([$all, $payload], [
            'status',
            'result',
            'verify_result',
            'event.status',
            'eventState',
            'EventNotificationAlert.eventState',
            'EventNotificationAlert.AccessControllerEvent.status',
            'AccessControllerEvent.status',
        ], 'success');

        $confidence = $this->pickValue([$all, $payload], [
            'confidence',
            'score',
            'similarity',
            'event.confidence',
            'EventNotificationAlert.AccessControllerEvent.maskConfidence',
            'EventNotificationAlert.AccessControllerEvent.faceScore',
            'AccessControllerEvent.faceScore',
        ]);

        if (is_numeric($confidence)) {
            $confidence = (float) $confidence;
            if ($confidence > 0 && $confidence <= 1) {
                $confidence = $confidence * 100;
            }
            $confidence = min(100, max(0, $confidence));
        } else {
            $confidence = null;
        }

        return [
            'device_serial' => $deviceSerial,
            'employee_code' => $employeeCode,
            'employee_name' => $employeeName,
            'event_time' => $eventTime,
            'event_type' => is_scalar($eventType) ? (string) $eventType : 'authentication',
            'direction' => $this->normalizeDirection($rawDirection, $eventType),
            'status' => is_scalar($status) ? strtolower((string) $status) : 'success',
            'confidence' => $confidence,
            'payload' => $payload ?: $all,
        ];
    }

    private function pickValue(array $sources, array $keys, mixed $default = null): mixed
    {
        foreach ($sources as $source) {
            foreach ($keys as $key) {
                $value = data_get($source, $key);
                if ($value !== null && $value !== '') {
                    return $value;
                }
            }
        }

        return $default;
    }

    private function parseEventTime(mixed $eventTime): Carbon
    {
        if (!$eventTime) {
            return now();
        }

        try {
            return Carbon::parse((string) $eventTime);
        } catch (\Throwable) {
            return now();
        }
    }

    private function normalizeDirection(mixed $direction, mixed $eventType = null): string
    {
        $value = strtolower(trim((string) ($direction ?? '')));
        $type = strtolower(trim((string) ($eventType ?? '')));

        $entryAliases = ['entry', 'in', 'checkin', 'check_in', 'clockin', 'clock_in', 'arrivee', 'arrive', 'entrance', '1'];
        $exitAliases = ['exit', 'out', 'checkout', 'check_out', 'clockout', 'clock_out', 'depart', 'sortie', 'leave', '2'];

        $value = str_replace([' ', '-'], '_', $value);
        $type = str_replace([' ', '-'], '_', $type);

        if (in_array($value, $entryAliases, true)) {
            return 'entry';
        }
        if (in_array($value, $exitAliases, true)) {
            return 'exit';
        }

        if (str_contains($type, 'in') || str_contains($type, 'entry') || str_contains($type, 'checkin')) {
            return 'entry';
        }
        if (str_contains($type, 'out') || str_contains($type, 'exit') || str_contains($type, 'checkout')) {
            return 'exit';
        }

        return 'unknown';
    }

    private function isDuplicateEvent(int $deviceId, string $employeeCode, Carbon $eventTime, string $direction): bool
    {
        $windowStart = (clone $eventTime)->subSeconds(5);
        $windowEnd = (clone $eventTime)->addSeconds(5);

        return FacialEvent::where('facial_device_id', $deviceId)
            ->where('employee_code', $employeeCode)
            ->where('direction', $direction)
            ->whereBetween('event_time', [$windowStart, $windowEnd])
            ->exists();
    }

    private function filterPointagePayload(array $payload): array
    {
        $columns = Schema::getColumnListing('pointages');
        return array_intersect_key($payload, array_flip($columns));
    }
}
