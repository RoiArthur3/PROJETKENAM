<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class HikvisionConfigController extends Controller
{
    /**
     * Page de configuration Hikvision
     */
    public function config()
    {
        $currentConfig = [
            'enabled' => config('hikvision.enabled', false),
            'default_ip' => config('hikvision.default_ip', '192.168.1.70'),
            'default_port' => config('hikvision.default_port', 80),
            'default_protocol' => config('hikvision.default_protocol', 'http'),
            'default_username' => config('hikvision.default_username', 'admin'),
            'default_password' => config('hikvision.default_password', ''),
            'endpoint' => config('hikvision.endpoint', '/ISAPI/Event/notification/alertStreams'),
            'timeout' => config('hikvision.timeout', 30),
        ];

        return view('hikvision.config', compact('currentConfig'));
    }

    /**
     * Page de diagnostic Hikvision
     */
    public function diagnostic()
    {
        return view('hikvision.diagnostic');
    }

    /**
     * Tester la connexion avec les paramètres du formulaire
     */
    public function testConnection(Request $request)
    {
        $data = $request->validate([
            'ip' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'protocol' => 'required|in:http,https',
            'username' => 'required|string',
            'password' => 'required|string',
            'endpoint' => 'required|string',
            'timeout' => 'integer|min:5|max:120'
        ]);

        try {
            $baseUrl = "{$data['protocol']}://{$data['ip']}:{$data['port']}";
            $testUrl = $baseUrl . '/ISAPI/System/deviceInfo';

            $ch = curl_init($testUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "{$data['username']}:{$data['password']}");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_TIMEOUT, $data['timeout']);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'KENAM-Hikvision-Client/1.0');

            $startTime = microtime(true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            $endTime = microtime(true);
            curl_close($ch);

            if ($error) {
                return response()->json([
                    'success' => false,
                    'error' => $error
                ], 500);
            }

            if ($httpCode === 200) {
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'device_info' => $this->parseDeviceXml($response),
                    'response_time' => round($endTime - $startTime, 3)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => "HTTP {$httpCode}",
                    'message' => 'Échec de connexion'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tester la connexion avec les variables .env
     */
    public function testEnv(Request $request)
    {
        try {
            $ip = config('hikvision.default_ip');
            $username = config('hikvision.default_user');
            $password = config('hikvision.default_password');
            $port = config('hikvision.default_port');
            $protocol = config('hikvision.default_protocol');
            $timeout = config('hikvision.default_timeout');

            $baseUrl = "{$protocol}://{$ip}:{$port}";
            $testUrl = $baseUrl . '/ISAPI/System/deviceInfo';

            $ch = curl_init($testUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'KENAM-Hikvision-Client/1.0');

            $startTime = microtime(true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            $endTime = microtime(true);
            curl_close($ch);

            if ($error) {
                return response()->json([
                    'success' => false,
                    'error' => $error
                ], 500);
            }

            if ($httpCode === 200) {
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie avec variables .env',
                    'device_info' => $this->parseDeviceXml($response),
                    'response_time' => round($endTime - $startTime, 3)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => "HTTP {$httpCode}",
                    'message' => 'Échec de connexion avec variables .env'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les événements avec paramètres du formulaire
     */
    public function fetchEvents(Request $request)
    {
        $data = $request->validate([
            'ip' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'protocol' => 'required|in:http,https',
            'username' => 'required|string',
            'password' => 'required|string',
            'endpoint' => 'required|string',
            'timeout' => 'integer|min:5|max:120',
            'max_results' => 'integer|min:1|max:1000'
        ]);

        try {
            $baseUrl = "{$data['protocol']}://{$data['ip']}:{$data['port']}";
            $url = $baseUrl . $data['endpoint'];

            if (strpos($data['endpoint'], '?') === false) {
                $url .= "?searchID=0&maxResults={$data['max_results']}";
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "{$data['username']}:{$data['password']}");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_TIMEOUT, $data['timeout']);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'KENAM-Hikvision-Client/1.0');

            $startTime = microtime(true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            $endTime = microtime(true);
            curl_close($ch);

            if ($error) {
                return response()->json([
                    'success' => false,
                    'error' => $error
                ], 500);
            }

            if ($httpCode === 200) {
                $events = $this->parseEventsXml($response, $data['endpoint']);

                return response()->json([
                    'success' => true,
                    'events' => $events,
                    'total' => count($events),
                    'url_used' => $url,
                    'response_time' => round($endTime - $startTime, 3)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => "HTTP {$httpCode}",
                    'message' => 'Échec de récupération des événements'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les événements avec variables .env
     */
    public function fetchEventsEnv(Request $request)
    {
        $data = $request->validate([
            'max_results' => 'integer|min:1|max:1000'
        ]);

        try {
            $ip = config('hikvision.default_ip');
            $username = config('hikvision.default_user');
            $password = config('hikvision.default_password');
            $port = config('hikvision.default_port');
            $protocol = config('hikvision.default_protocol');
            $endpoint = config('hikvision.default_endpoint');
            $timeout = config('hikvision.default_timeout');
            $maxResults = $data['max_results'] ?? config('hikvision.default_max_results');

            $baseUrl = "{$protocol}://{$ip}:{$port}";
            $url = $baseUrl . $endpoint;

            if (strpos($endpoint, '?') === false) {
                $url .= "?searchID=0&maxResults={$maxResults}";
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'KENAM-Hikvision-Client/1.0');

            $startTime = microtime(true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            $endTime = microtime(true);
            curl_close($ch);

            if ($error) {
                return response()->json([
                    'success' => false,
                    'error' => $error
                ], 500);
            }

            if ($httpCode === 200) {
                $events = $this->parseEventsXml($response, $endpoint);

                return response()->json([
                    'success' => true,
                    'events' => $events,
                    'total' => count($events),
                    'url_used' => $url,
                    'response_time' => round($endTime - $startTime, 3)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => "HTTP {$httpCode}",
                    'message' => 'Échec de récupération des événements avec variables .env'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sauvegarder la configuration
     */
    public function saveConfig(Request $request)
    {
        $data = $request->validate([
            'enabled' => 'boolean',
            'ip' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'protocol' => 'required|in:http,https',
            'username' => 'required|string',
            'password' => 'required|string',
            'endpoint' => 'required|string',
            'timeout' => 'integer|min:5|max:120',
            'max_results' => 'integer|min:1|max:1000'
        ]);

        try {
            $configPath = config('hikvision.storage.path');

            // Créer le répertoire si nécessaire
            $dir = dirname($configPath);
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            // Sauvegarder la configuration
            $configData = [
                'enabled' => $data['enabled'] ?? false,
                'default_ip' => $data['ip'],
                'default_port' => $data['port'],
                'default_protocol' => $data['protocol'],
                'default_user' => $data['username'],
                'default_password' => $data['password'],
                'default_endpoint' => $data['endpoint'],
                'default_timeout' => $data['timeout'],
                'default_max_results' => $data['max_results'],
                'updated_at' => now()->toISOString()
            ];

            File::put($configPath, json_encode($configData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return response()->json([
                'success' => true,
                'message' => 'Configuration sauvegardée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parser le XML des informations device
     */
    private function parseDeviceXml($xmlString)
    {
        try {
            $xml = simplexml_load_string($xmlString);
            if (!$xml) {
                return ['error' => 'Invalid XML response'];
            }

            return [
                'device_name' => (string) $xml->deviceName ?? 'Unknown',
                'device_id' => (string) $xml->deviceID ?? 'Unknown',
                'mac_address' => (string) $xml->macAddress ?? 'Unknown',
                'firmware_version' => (string) $xml->firmwareVersion ?? 'Unknown',
                'serial_number' => (string) $xml->serialNumber ?? 'Unknown'
            ];
        } catch (\Exception $e) {
            return ['error' => 'Failed to parse device info: ' . $e->getMessage()];
        }
    }

    /**
     * Parser le XML des événements
     */
    private function parseEventsXml($xmlString, $endpoint)
    {
        try {
            $xml = simplexml_load_string($xmlString);
            if (!$xml) {
                return [];
            }

            $events = [];

            // Déterminer le type de réponse
            if (strpos($endpoint, 'AcsEvent') !== false && isset($xml->AcsEventInfo)) {
                foreach ($xml->AcsEventInfo as $eventInfo) {
                    $events[] = [
                        'employee_code' => (string) $eventInfo->employeeNoString ?? '',
                        'employee_name' => (string) $eventInfo->name ?? '',
                        'event_time' => (string) $eventInfo->time ?? '',
                        'event_type' => (string) $eventInfo->eventType ?? '',
                        'direction' => (string) $eventInfo->door ?? '',
                        'status' => (string) $eventInfo->status ?? '',
                        'confidence' => (float) ($eventInfo->faceScore ?? null),
                        'door_name' => (string) $eventInfo->doorName ?? '',
                        'card_no' => (string) $eventInfo->cardNo ?? ''
                    ];
                }
            } elseif (isset($xml->EventLogInfo)) {
                foreach ($xml->EventLogInfo as $eventInfo) {
                    $events[] = [
                        'employee_code' => (string) $eventInfo->employeeNoString ?? '',
                        'employee_name' => (string) $eventInfo->name ?? '',
                        'event_time' => (string) $eventInfo->time ?? '',
                        'event_type' => (string) $eventInfo->majorEventType ?? '',
                        'direction' => $this->mapDirection((string) $eventInfo->inAndOutFlag ?? ''),
                        'status' => (string) $eventInfo->eventState ?? '',
                        'confidence' => (float) ($eventInfo->faceScore ?? null)
                    ];
                }
            }

            return $events;
        } catch (\Exception $e) {
            Log::error('Failed to parse events XML', [
                'error' => $e->getMessage(),
                'xml_preview' => substr($xmlString, 0, 500)
            ]);
            return [];
        }
    }

    /**
     * Mapper le flag direction
     */
    private function mapDirection($flag)
    {
        return match($flag) {
            '1', 'in', 'entry' => 'entry',
            '2', 'out', 'exit' => 'exit',
            default => 'unknown'
        };
    }
}
