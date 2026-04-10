<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FacialDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HikvisionEventController extends Controller
{
    /**
     * Récupérer les événements depuis l'API ISAPI Hikvision (pull mode)
     * Endpoint: GET /api/hikvision/events/{deviceId}
     */
    public function getEvents(Request $request, $deviceId)
    {
        $device = FacialDevice::findOrFail($deviceId);

        if (!$device->is_active) {
            return response()->json(['error' => 'Device not active'], 403);
        }

        try {
            // Construire l'URL ISAPI pour les événements de présence
            $baseUrl = "{$device->protocol}://{$device->ip_address}:{$device->port}";

            // URL principale pour les événements d'accès
            $eventsUrl = "{$baseUrl}/ISAPI/AccessControl/EventLog/Info?searchID=0&maxResults=100";

            // Alternative: URL pour les événements ACS plus détaillés
            $acsEventsUrl = "{$baseUrl}/ISAPI/AccessControl/AcsEvent?searchID=0&maxResults=100";

            // Essayer d'abord l'URL ACS si spécifiée dans la requête
            $useAcsEvents = $request->boolean('use_acs', false);
            $targetUrl = $useAcsEvents ? $acsEventsUrl : $eventsUrl;

            Log::info("Fetching Hikvision events", [
                'device' => $device->name,
                'url' => $targetUrl,
                'use_acs' => $useAcsEvents
            ]);

            $response = Http::withBasicAuth($device->username, $device->password)
                ->timeout(15)
                ->get($targetUrl);

            if (!$response->successful()) {
                Log::error('Hikvision API error', [
                    'device' => $device->name,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $targetUrl
                ]);

                // Si l'URL ACS échoue, essayer l'URL standard
                if ($useAcsEvents) {
                    Log::info("Falling back to standard events URL", [
                        'device' => $device->name,
                        'fallback_url' => $eventsUrl
                    ]);

                    $response = Http::withBasicAuth($device->username, $device->password)
                        ->timeout(15)
                        ->get($eventsUrl);

                    if (!$response->successful()) {
                        return response()->json(['error' => 'Failed to fetch events from both endpoints'], 500);
                    }
                } else {
                    return response()->json(['error' => 'Failed to fetch events'], 500);
                }
            }

            $xml = simplexml_load_string($response->body());
            $events = [];

            // Traitement différent selon le type de réponse
            if ($useAcsEvents && isset($xml->AcsEventInfo)) {
                // Traitement des événements ACS
                foreach ($xml->AcsEventInfo ?? [] as $eventInfo) {
                    $events[] = [
                        'device_serial' => $device->serial_number,
                        'employee_code' => (string) $eventInfo->employeeNoString ?? '',
                        'employee_name' => (string) $eventInfo->name ?? '',
                        'event_time' => (string) $eventInfo->time ?? '',
                        'event_type' => (string) $eventInfo->eventType ?? '',
                        'direction' => $this->mapDirection((string) $eventInfo->door ?? ''),
                        'status' => (string) $eventInfo->status ?? '',
                        'confidence' => (float) ($eventInfo->faceScore ?? null),
                        'door_name' => (string) $eventInfo->doorName ?? '',
                        'card_no' => (string) $eventInfo->cardNo ?? '',
                    ];
                }
            } else {
                // Traitement des événements standard
                foreach ($xml->EventLogInfo ?? [] as $eventInfo) {
                    $events[] = [
                        'device_serial' => $device->serial_number,
                        'employee_code' => (string) $eventInfo->employeeNoString ?? '',
                        'employee_name' => (string) $eventInfo->name ?? '',
                        'event_time' => (string) $eventInfo->time ?? '',
                        'event_type' => (string) $eventInfo->majorEventType ?? '',
                        'direction' => $this->mapDirection((string) $eventInfo->inAndOutFlag ?? ''),
                        'status' => (string) $eventInfo->eventState ?? '',
                        'confidence' => (float) ($eventInfo->faceScore ?? null),
                    ];
                }
            }

            // Mettre à jour le statut du device
            $device->update([
                'last_seen_at' => now(),
                'last_status' => 'online',
                'last_error' => null
            ]);

            return response()->json([
                'success' => true,
                'events' => $events,
                'total' => count($events),
                'device' => $device->name,
                'url_used' => $targetUrl,
                'response_time' => $response->handlerStats()['total_time'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Hikvision fetch error', [
                'device' => $device->name,
                'error' => $e->getMessage()
            ]);

            $device->update([
                'last_status' => 'offline',
                'last_error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * S'abonner aux événements en temps réel (Event Push)
     * Endpoint: POST /api/hikvision/subscribe/{deviceId}
     */
    public function subscribe(Request $request, $deviceId)
    {
        $device = FacialDevice::findOrFail($deviceId);
        $callbackUrl = $request->input('callback_url', route('api.facial.events'));

        try {
            $baseUrl = "{$device->protocol}://{$device->ip_address}:{$device->port}";
            $subscribeUrl = "{$baseUrl}/ISAPI/Event/notification/subscribe";

            $payload = [
                'EventNotification' => [
                    'Subscribe' => [
                        'ID' => uniqid('sub_', true),
                        'URL' => $callbackUrl,
                        'ProtocolType' => 'HTTP',
                        'OperationMode' => 'isapi',
                        'EventNotificationType' => 'All',
                    ]
                ]
            ];

            $response = Http::withBasicAuth($device->username, $device->password)
                ->withHeaders(['Content-Type' => 'application/xml'])
                ->timeout(10)
                ->post($subscribeUrl, $this->arrayToXml($payload));

            if (!$response->successful()) {
                return response()->json(['error' => 'Failed to subscribe'], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscribed successfully',
                'callback_url' => $callbackUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Hikvision subscribe error', [
                'device' => $device->name,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Tester la connexion avec un terminal Hikvision
     */
    public function testConnection($deviceId)
    {
        $device = FacialDevice::findOrFail($deviceId);

        try {
            $baseUrl = "{$device->protocol}://{$device->ip_address}:{$device->port}";
            $testUrl = "{$baseUrl}/ISAPI/System/deviceInfo";

            $response = Http::withBasicAuth($device->username, $device->password)
                ->timeout(5)
                ->get($testUrl);

            if ($response->successful()) {
                $device->update([
                    'last_seen_at' => now(),
                    'last_status' => 'online',
                    'last_error' => null
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Connection successful',
                    'device_info' => $response->json()
                ]);
            } else {
                throw new \Exception("HTTP {$response->status()}");
            }

        } catch (\Exception $e) {
            $device->update([
                'last_status' => 'offline',
                'last_error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mapper le flag inAndOutFlag vers entry/exit
     */
    private function mapDirection($flag)
    {
        return match($flag) {
            '1', 'in', 'entry' => 'entry',
            '2', 'out', 'exit' => 'exit',
            default => 'unknown'
        };
    }

    /**
     * Recevoir les événements depuis la caméra Hikvision (webhook)
     * Endpoint: POST /api/hikvision/events
     */
    public function receiveWebhook(Request $request)
    {
        try {
            Log::info('Hikvision webhook received', [
                'content_type' => $request->header('Content-Type'),
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            // Récupérer le contenu XML/JSON
            $content = $request->getContent();

            if (empty($content)) {
                Log::warning('Empty webhook content received');
                return response()->json(['status' => 'error', 'message' => 'Empty content'], 400);
            }

            // Parser le contenu (XML ou JSON)
            $data = null;
            if (strpos($request->header('Content-Type'), 'xml') !== false) {
                $data = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
                $data = json_decode(json_encode($data), true);
            } else {
                $data = json_decode($content, true);
            }

            if (!$data) {
                Log::error('Failed to parse webhook content', ['content' => $content]);
                return response()->json(['status' => 'error', 'message' => 'Invalid content'], 400);
            }

            // Traiter les événements
            $events = $this->extractEvents($data);

            foreach ($events as $event) {
                $this->storeEvent($event);
            }

            Log::info('Hikvision webhook processed successfully', [
                'events_count' => count($events),
                'device_ip' => $request->ip()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Events processed successfully',
                'events_count' => count($events)
            ]);

        } catch (\Exception $e) {
            Log::error('Hikvision webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Extraire les événements des données reçues
     */
    private function extractEvents($data)
    {
        $events = [];

        // Adapter selon le format reçu par la caméra DS-K1T342MFWX-E1
        if (isset($data['EventNotification'])) {
            $eventData = $data['EventNotification'];

            if (isset($eventData['Event'])) {
                $eventList = is_array($eventData['Event']) ? $eventData['Event'] : [$eventData['Event']];

                foreach ($eventList as $event) {
                    $events[] = [
                        'device_id' => $event['deviceID'] ?? 'DS-K1T342MFWX-E1',
                        'event_type' => $event['eventType'] ?? 'unknown',
                        'event_type_id' => $event['eventTypeID'] ?? 0,
                        'event_state' => $event['eventState'] ?? 'active',
                        'event_time' => $event['eventTime'] ?? now(),
                        'employee_id' => $event['employeeID'] ?? null,
                        'card_number' => $event['cardNo'] ?? null,
                        'device_name' => $event['deviceName'] ?? 'Hikvision DS-K1T342MFWX-E1',
                        'direction' => $this->mapDirection($event['direction'] ?? null),
                        'raw_data' => $event
                    ];
                }
            }
        }

        return $events;
    }

    /**
     * Stocker un événement en base de données
     */
    private function storeEvent($eventData)
    {
        try {
            // Créer ou mettre à jour le pointage
            \App\Models\Pointage::updateOrCreate(
                [
                    'employee_id' => $eventData['employee_id'],
                    'pointage_time' => $eventData['event_time'],
                    'type' => 'hikvision'
                ],
                [
                    'device_id' => $eventData['device_id'],
                    'device_name' => $eventData['device_name'],
                    'direction' => $eventData['direction'],
                    'card_number' => $eventData['card_number'],
                    'event_type' => $eventData['event_type'],
                    'raw_data' => json_encode($eventData['raw_data']),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            Log::info('Event stored successfully', [
                'employee_id' => $eventData['employee_id'],
                'event_time' => $eventData['event_time'],
                'direction' => $eventData['direction']
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to store event', [
                'error' => $e->getMessage(),
                'event_data' => $eventData
            ]);
        }
    }

    /**
     * Convertir un tableau PHP en XML pour les requêtes ISAPI
     */
    private function arrayToXml($array)
    {
        $xml = new \SimpleXMLElement('<root/>');
        array_walk_recursive($array, function ($value, $key) use ($xml) {
            $xml->addChild($key, htmlspecialchars($value));
        });
        return $xml->asXML();
    }
}
