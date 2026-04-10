<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FacialDevice;
use App\Models\FacialRecognitionRecord;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FacialRecognitionController extends Controller
{
    /**
     * Enregistrer un événement de reconnaissance faciale
     * Endpoint: POST /api/facial-recognition/record
     */
    public function record(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_id' => 'required|exists:facial_devices,id',
                'device_serial' => 'nullable|string|max:100',
                'employee_code' => 'nullable|string|max:50',
                'employee_name' => 'nullable|string|max:100',
                'photo_base64' => 'nullable|string',
                'face_id' => 'nullable|string|max:100',
                'confidence_score' => 'nullable|numeric|min:0|max:1',
                'direction' => 'nullable|in:entry,exit,unknown',
                'door_name' => 'nullable|string|max:100',
                'event_type' => 'nullable|string|max:50',
                'recognition_time' => 'nullable|date',
                'camera_metadata' => 'nullable|array'
            ]);

            // Trouver le device
            $device = FacialDevice::findOrFail($validated['device_id']);

            // Trouver le personnel correspondant
            $personnel = null;
            if (!empty($validated['employee_code'])) {
                $personnel = Personnel::where('matricule', $validated['employee_code'])
                                   ->orWhere('email', $validated['employee_code'])
                                   ->first();
            }

            // Créer l'enregistrement
            $record = FacialRecognitionRecord::create([
                'device_id' => $device->id,
                'device_serial' => $validated['device_serial'] ?? $device->serial_number,
                'personnel_id' => $personnel?->id,
                'employee_code' => $validated['employee_code'],
                'employee_name' => $validated['employee_name'],
                'photo_base64' => $validated['photo_base64'],
                'face_id' => $validated['face_id'],
                'confidence_score' => $validated['confidence_score'],
                'direction' => $validated['direction'],
                'door_name' => $validated['door_name'],
                'event_type' => $validated['event_type'] ?? 'facial_recognition',
                'status' => $this->determineStatus($validated),
                'recognition_time' => $validated['recognition_time'] ?? now(),
                'camera_metadata' => $validated['camera_metadata'],
                'sync_status' => 'pending'
            ]);

            // Si personnel trouvé et reconnaissance valide, synchroniser avec la caméra
            if ($personnel && $record->isValid()) {
                $this->performCameraSync($record, $device, $personnel);
            }

            // Mettre à jour le statut du device
            $device->update([
                'last_seen_at' => now(),
                'last_status' => 'online'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Enregistrement créé avec succès',
                'record_id' => $record->id,
                'personnel_found' => !is_null($personnel),
                'sync_initiated' => !is_null($personnel) && $record->isValid()
            ]);

        } catch (\Exception $e) {
            Log::error('Facial recognition record error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Synchroniser un employé avec la caméra
     * Endpoint: POST /api/facial-recognition/sync-to-camera
     */
    public function syncToCamera(Request $request)
    {
        try {
            $validated = $request->validate([
                'personnel_id' => 'required|exists:personnel,id',
                'device_id' => 'required|exists:facial_devices,id',
                'photo_base64' => 'nullable|string'
            ]);

            $personnel = Personnel::findOrFail($validated['personnel_id']);
            $device = FacialDevice::findOrFail($validated['device_id']);

            // Utiliser la photo fournie ou celle du personnel
            $photoBase64 = $validated['photo_base64'] ?? $personnel->photo_base64;

            if (empty($photoBase64)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucune photo disponible pour cet employé'
                ], 400);
            }

            // Appeler le contrôleur de synchronisation Hikvision
            $hikvisionController = new \App\Http\Controllers\Api\HikvisionSyncController();
            $syncResult = $hikvisionController->syncEmployees(
                new Request([
                    'employees' => [[
                        'employee_no' => $personnel->matricule,
                        'name' => $personnel->nom . ' ' . $personnel->prenoms,
                        'photo_base64' => $photoBase64
                    ]],
                    'device_id' => $device->id
                ])
            );

            if ($syncResult->getStatusCode() === 200) {
                $syncData = json_decode($syncResult->getContent(), true);

                // Créer un enregistrement de suivi
                FacialRecognitionRecord::create([
                    'device_id' => $device->id,
                    'personnel_id' => $personnel->id,
                    'employee_code' => $personnel->matricule,
                    'employee_name' => $personnel->nom . ' ' . $personnel->prenoms,
                    'photo_base64' => $photoBase64,
                    'event_type' => 'manual_sync',
                    'status' => 'synced',
                    'sync_status' => 'synced',
                    'synced_at' => now(),
                    'recognition_time' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Employé synchronisé avec succès',
                    'sync_result' => $syncData
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Échec de synchronisation avec la caméra',
                    'sync_result' => json_decode($syncResult->getContent(), true)
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Sync to camera error', [
                'error' => $e->getMessage(),
                'personnel_id' => $request->input('personnel_id'),
                'device_id' => $request->input('device_id')
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les enregistrements de reconnaissance
     * Endpoint: GET /api/facial-recognition/records
     */
    public function getRecords(Request $request)
    {
        try {
            $query = FacialRecognitionRecord::with(['device', 'personnel']);

            // Filtres
            if ($request->has('device_id')) {
                $query->where('device_id', $request->input('device_id'));
            }

            if ($request->has('personnel_id')) {
                $query->where('personnel_id', $request->input('personnel_id'));
            }

            if ($request->has('employee_code')) {
                $query->where('employee_code', 'like', '%' . $request->input('employee_code') . '%');
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->has('sync_status')) {
                $query->where('sync_status', $request->input('sync_status'));
            }

            if ($request->has('date_from')) {
                $query->whereDate('recognition_time', '>=', $request->input('date_from'));
            }

            if ($request->has('date_to')) {
                $query->whereDate('recognition_time', '<=', $request->input('date_to'));
            }

            // Tri
            $sortBy = $request->input('sort_by', 'recognition_time');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->input('per_page', 20);
            $records = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $records->items(),
                'pagination' => [
                    'current_page' => $records->currentPage(),
                    'per_page' => $records->perPage(),
                    'total' => $records->total(),
                    'last_page' => $records->lastPage()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get facial recognition records error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Synchroniser les enregistrements en attente
     * Endpoint: POST /api/facial-recognition/sync-pending
     */
    public function syncPendingRecords(Request $request)
    {
        try {
            $pendingRecords = FacialRecognitionRecord::with(['personnel', 'device'])
                ->where('sync_status', 'pending')
                ->whereHas('personnel')
                ->whereNotNull('photo_base64')
                ->limit(50) // Limiter pour éviter la surcharge
                ->get();

            $syncedCount = 0;
            $failedCount = 0;

            foreach ($pendingRecords as $record) {
                try {
                    $this->syncToCamera($record, $record->device, $record->personnel);
                    $syncedCount++;
                } catch (\Exception $e) {
                    $record->update([
                        'sync_status' => 'failed',
                        'sync_error' => $e->getMessage()
                    ]);
                    $failedCount++;
                    Log::error('Failed to sync record', [
                        'record_id' => $record->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Synchronisation terminée',
                'synced_count' => $syncedCount,
                'failed_count' => $failedCount,
                'total_processed' => $syncedCount + $failedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Sync pending records error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques de reconnaissance
     * Endpoint: GET /api/facial-recognition/stats
     */
    public function getStats(Request $request)
    {
        try {
            $stats = [
                'total_records' => FacialRecognitionRecord::count(),
                'today_records' => FacialRecognitionRecord::whereDate('recognition_time', today())->count(),
                'successful_recognitions' => FacialRecognitionRecord::successful()->count(),
                'pending_sync' => FacialRecognitionRecord::pending()->count(),
                'synced_records' => FacialRecognitionRecord::synced()->count(),
                'unique_employees' => FacialRecognitionRecord::whereNotNull('personnel_id')
                    ->distinct('personnel_id')
                    ->count(),
                'devices_active' => FacialDevice::where('is_active', true)->count(),
            ];

            // Statistiques par device
            $deviceStats = FacialRecognitionRecord::join('facial_devices', 'facial_recognition_records.device_id', '=', 'facial_devices.id')
                ->selectRaw('
                    facial_devices.name as device_name,
                    COUNT(*) as record_count,
                    AVG(confidence_score) as avg_confidence,
                    COUNT(CASE WHEN sync_status = "synced" THEN 1 END) as synced_count
                ')
                ->groupBy('facial_devices.id', 'facial_devices.name')
                ->get();

            $stats['by_device'] = $deviceStats;

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Get facial recognition stats error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Déterminer le statut de reconnaissance
     */
    private function determineStatus(array $data): string
    {
        $confidence = $data['confidence_score'] ?? 0;
        $hasEmployee = !empty($data['employee_code']) || !empty($data['employee_name']);

        if ($confidence >= 0.8 && $hasEmployee) {
            return 'recognized';
        } elseif ($confidence >= 0.5) {
            return 'uncertain';
        } else {
            return 'unknown';
        }
    }

    /**
     * Synchroniser un enregistrement avec la caméra
     */
    private function performCameraSync(FacialRecognitionRecord $record, FacialDevice $device, Personnel $personnel): void
    {
        $record->update(['sync_status' => 'syncing']);

        try {
            $hikvisionController = new \App\Http\Controllers\Api\HikvisionSyncController();
            $syncResult = $hikvisionController->syncEmployees(
                new Request([
                    'employees' => [[
                        'employee_no' => $personnel->matricule,
                        'name' => $personnel->nom . ' ' . $personnel->prenoms,
                        'photo_base64' => $record->photo_base64
                    ]],
                    'device_id' => $device->id
                ])
            );

            if ($syncResult->getStatusCode() === 200) {
                $record->update([
                    'sync_status' => 'synced',
                    'synced_at' => now(),
                    'sync_error' => null
                ]);
            } else {
                $syncData = json_decode($syncResult->getContent(), true);
                $record->update([
                    'sync_status' => 'failed',
                    'sync_error' => $syncData['message'] ?? 'Erreur inconnue'
                ]);
            }
        } catch (\Exception $e) {
            $record->update([
                'sync_status' => 'failed',
                'sync_error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
