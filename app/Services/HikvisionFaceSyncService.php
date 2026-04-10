<?php

namespace App\Services;

use App\Models\Personnel;
use App\Models\FacialDevice;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HikvisionFaceSyncService
{
    private $baseUrl;
    private $username;
    private $password;

    private function envFirst(string ...$keys): mixed
    {
        foreach ($keys as $key) {
            $value = env($key);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    public function __construct()
    {
        $this->baseUrl = env('HIKVISION_BASE_URL', 'http://' . env('HIKVISION_IP', '192.168.1.70'));
        $this->username = $this->envFirst('HIKVISION_USER', 'HIKVISION_USERNAME') ?? 'admin';
        $this->password = $this->envFirst('HIKVISION_PASS', 'HIKVISION_PASSWORD') ?? 'Arthur@752';
    }

    private function getDeviceConfig($deviceId = null): array
    {
        if ($deviceId) {
            $device = FacialDevice::findOrFail($deviceId);
            return [
                'ip' => $device->ip_address,
                'port' => $device->port,
                'user' => $device->username,
                'pass' => $device->password,
            ];
        }

        return [
            'ip' => env('HIKVISION_IP', '192.168.1.70'),
            'port' => env('HIKVISION_PORT', 80),
            'user' => $this->username,
            'pass' => $this->password,
        ];
    }

    /**
     * Tester la connexion globale à la caméra
     */
    public function testConnection($deviceId = null): array
    {
        $config = $this->getDeviceConfig($deviceId);
        $ip = $config['ip'];
        $port = $config['port'];
        $user = $config['user'];
        $pass = $config['pass'];
        
        $baseUrl = "http://{$ip}:{$port}";
        $url = "{$baseUrl}/ISAPI/System/deviceInfo";

        try {
            // 1. Test de socket rapide
            $errno = 0;
            $errstr = '';
            $socket = @fsockopen($ip, $port, $errno, $errstr, 3);
            if (!$socket) {
                return [
                    'success' => false,
                    'message' => "Impossible d'ouvrir une connexion sur {$ip}:{$port}. " . ($errstr ?: "Délai d'attente dépassé."),
                    'step' => 'network'
                ];
            }
            fclose($socket);

            // 2. Test d'authentification ISAPI
            $response = Http::withBasicAuth($user, $pass)
                ->timeout(5)
                ->get($url);

            if ($response->successful()) {
                // Mettre à jour last_seen_at si on a un device_id ou qu'il y a un appareil actif
                if ($deviceId) {
                    FacialDevice::where('id', $deviceId)->update([
                        'last_seen_at' => now(),
                        'last_status' => 'online'
                    ]);
                } else {
                    FacialDevice::where('is_active', true)->update([
                        'last_seen_at' => now(),
                        'last_status' => 'online'
                    ]);
                }

                return [
                    'success' => true,
                    'message' => 'Connexion réussie au dispositif Hikvision.',
                    'device_info' => $response->json() ?: $response->body(),
                    'step' => 'auth'
                ];
            }

            if ($deviceId) {
                FacialDevice::where('id', $deviceId)->update(['last_status' => 'offline']);
            } else {
                FacialDevice::where('is_active', true)->update(['last_status' => 'offline']);
            }

            return [
                'success' => false,
                'message' => 'Échec de l\'authentification. Vérifiez l\'utilisateur et le mot de passe.',
                'http_code' => $response->status(),
                'step' => 'auth'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors du test: ' . $e->getMessage(),
                'step' => 'exception'
            ];
        }
    }

    /**
     * Récupérer une capture d'image (snapshot) de la caméra
     */
    public function getSnapshot($deviceId = null)
    {
        try {
            $config = $this->getDeviceConfig($deviceId);
            $url = "http://{$config['ip']}:{$config['port']}/ISAPI/Streaming/channels/101/picture";

            $response = Http::withBasicAuth($config['user'], $config['pass'])
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                // Mettre à jour last_seen_at
                if ($deviceId) {
                    FacialDevice::where('id', $deviceId)->update(['last_seen_at' => now()]);
                } else {
                    FacialDevice::where('is_active', true)->update(['last_seen_at' => now()]);
                }
                
                return [
                    'success' => true,
                    'image' => $response->body(),
                    'content_type' => $response->header('Content-Type') ?: 'image/jpeg'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur caméra: ' . $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Synchroniser un employé vers la caméra Hikvision
     */
    public function syncEmployeeToCamera(Personnel $personnel, FacialDevice $device = null): bool
    {
        try {
            if (!$personnel->photo_profil) {
                Log::warning("Aucune photo pour synchroniser: {$personnel->matricule}");
                return false;
            }

            // 1. Préparer la photo pour la caméra
            $photoData = $this->preparePhotoForCamera($personnel->photo_profil);
            if (!$photoData) {
                Log::error("Impossible de préparer la photo pour: {$personnel->matricule}");
                return false;
            }

            $cameraProfile = $this->createCameraProfile([
                'matricule' => $personnel->matricule,
                'nom_complet' => $personnel->nom_complet,
                'sexe' => $personnel->sexe,
                'date_naissance' => $personnel->date_naissance?->format('Y-m-d'),
                'camera_person_id' => $personnel->camera_person_id,
            ], $photoData, $device);

            if (!($cameraProfile['success'] ?? false)) {
                Log::error("Impossible de créer le profil facial pour: {$personnel->matricule}");
                return false;
            }

            $personnel->update([
                'camera_person_id' => $cameraProfile['camera_person_id'] ?? $personnel->camera_person_id ?? $personnel->matricule,
                'facial_sync_status' => 'synced',
                'facial_sync_at' => now(),
                'facial_device_id' => $device?->id
            ]);

            Log::info("Employé synchronisé avec succès: {$personnel->matricule}");
            return true;

        } catch (\Exception $e) {
            Log::error("Erreur synchronisation employé {$personnel->matricule}: " . $e->getMessage());
            return false;
        }
    }

    public function createEmployeeFromForm(array $payload, UploadedFile $photo, FacialDevice $device = null): array
    {
        try {
            $photoData = base64_encode((string) file_get_contents($photo->getRealPath()));

            if ($photoData === '') {
                return [
                    'success' => false,
                    'message' => 'Impossible de lire la photo de profil.',
                ];
            }

            return $this->createCameraProfile($payload, $photoData, $device);
        } catch (\Exception $e) {
            Log::error('Erreur création employé caméra depuis formulaire: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Préparer la photo pour la caméra (redimensionner, convertir)
     */
    private function preparePhotoForCamera(string $photoPath): ?string
    {
        try {
            $fullPath = Storage::disk('public')->path($photoPath);

            if (!file_exists($fullPath)) {
                Log::error("Photo non trouvée: {$fullPath}");
                return null;
            }

            // Vérifier et redimensionner si nécessaire
            $imageInfo = getimagesize($fullPath);
            if (!$imageInfo) {
                Log::error("Format d'image invalide: {$fullPath}");
                return null;
            }

            // Convertir en JPEG base64 (format requis par Hikvision)
            $imageData = file_get_contents($fullPath);
            return base64_encode($imageData);

        } catch (\Exception $e) {
            Log::error("Erreur préparation photo: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Créer le profil facial sur la caméra Hikvision
     */
    public function createCameraProfile(array $payload, string $photoData, FacialDevice $device = null): array
    {
        $deviceIp = $device?->ip_address ?? env('HIKVISION_IP', '192.168.1.70');
        $protocol = $device?->protocol ?? parse_url($this->baseUrl, PHP_URL_SCHEME) ?? 'http';
        $port = $device?->port;
        $baseUrl = $port ? "{$protocol}://{$deviceIp}:{$port}" : "{$protocol}://{$deviceIp}";
        $url = "{$baseUrl}/ISAPI/Intelligent/FacialRecognition/faceInfo";

        $cameraPersonId = (string) ($payload['camera_person_id'] ?? $payload['matricule'] ?? '');
        $requestPayload = [
            'FaceInfo' => [
                'faceID' => $cameraPersonId,
                'faceName' => (string) ($payload['nom_complet'] ?? $payload['matricule'] ?? ''),
                'facePic' => $photoData,
                'gender' => $this->mapGender($payload['sexe'] ?? null),
                'birthday' => $payload['date_naissance'] ?? null,
                'employeeID' => (string) ($payload['matricule'] ?? $cameraPersonId),
                'cardNo' => (string) ($payload['matricule'] ?? $cameraPersonId),
            ]
        ];

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $requestPayload);

            if ($response->successful()) {
                $responseData = $response->json();
                $resolvedCameraPersonId = $responseData['FaceInfo']['faceID']
                    ?? $responseData['FaceInfo']['FaceID']
                    ?? $responseData['faceID']
                    ?? $responseData['FaceID']
                    ?? $cameraPersonId;

                Log::info("Profil facial créé sur {$deviceIp}", [
                    'matricule' => $payload['matricule'] ?? null,
                    'camera_person_id' => $resolvedCameraPersonId,
                ]);

                return [
                    'success' => true,
                    'camera_person_id' => (string) $resolvedCameraPersonId,
                    'message' => 'Profil caméra créé avec succès',
                    'response' => $responseData,
                ];
            } else {
                Log::error("Erreur création profil facial: " . $response->body());
                return [
                    'success' => false,
                    'camera_person_id' => $cameraPersonId,
                    'message' => 'La caméra a refusé la création du profil',
                    'response' => $response->json() ?: $response->body(),
                ];
            }

        } catch (\Exception $e) {
            Log::error("Erreur API Hikvision: " . $e->getMessage());
            return [
                'success' => false,
                'camera_person_id' => $cameraPersonId,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Supprimer un profil facial de la caméra
     */
    public function removeEmployeeFromCamera(Personnel $personnel, FacialDevice $device = null): bool
    {
        try {
            $deviceIp = $device?->ip_address ?? env('HIKVISION_IP', '192.168.1.70');
            $url = "http://{$deviceIp}/ISAPI/Intelligent/FacialRecognition/faceInfo/{$personnel->matricule}";

            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout(30)
                ->delete($url);

            if ($response->successful()) {
                $personnel->update([
                    'facial_sync_status' => 'removed',
                    'facial_sync_at' => null,
                    'facial_device_id' => null
                ]);

                Log::info("Profil facial supprimé pour {$personnel->matricule}");
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Erreur suppression profil facial: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier le statut de synchronisation
     */
    public function checkSyncStatus(Personnel $personnel, FacialDevice $device = null): ?array
    {
        try {
            $deviceIp = $device?->ip_address ?? env('HIKVISION_IP', '192.168.1.70');
            $url = "http://{$deviceIp}/ISAPI/Intelligent/FacialRecognition/faceInfo/{$personnel->matricule}";

            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout(30)
                ->get($url);

            if ($response->successful()) {
                return [
                    'status' => 'found',
                    'data' => $response->json()
                ];
            }

            return ['status' => 'not_found'];

        } catch (\Exception $e) {
            Log::error("Erreur vérification statut: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Synchroniser tous les employés non synchronisés
     */
    public function syncAllPendingEmployees(): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0
        ];

        // Récupérer tous les employés avec photo mais non synchronisés
        $employees = Personnel::whereNotNull('photo_profil')
            ->where(function($query) {
                $query->whereNull('facial_sync_status')
                      ->orWhere('facial_sync_status', '!=', 'synced');
            })
            ->get();

        $device = FacialDevice::where('is_active', true)->first();

        foreach ($employees as $employee) {
            if ($this->syncEmployeeToCamera($employee, $device)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Mapper le genre pour Hikvision
     */
    private function mapGender(?string $sexe): string
    {
        return match(strtolower($sexe ?? '')) {
            'm', 'masculin', 'male' => 'male',
            'f', 'féminin', 'female' => 'female',
            default => 'unknown'
        };
    }
}
