<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FacialDevice;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HikvisionSyncController extends Controller
{
    /**
     * Synchroniser un ou plusieurs employés vers les terminaux Hikvision
     * Endpoint: POST /api/hikvision/sync-employees
     */
    public function syncEmployees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employees' => 'required|array|min:1',
            'employees.*.employee_no' => 'required|string|max:100',
            'employees.*.name' => 'nullable|string|max:255',
            'employees.*.photo_base64' => 'required|string',
            'device_id' => 'nullable|integer|exists:facial_devices,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $employees = $validated['employees'];
        $deviceId = $validated['device_id'] ?? null;

        // Récupérer les terminaux cibles
        $devices = $deviceId 
            ? [FacialDevice::findOrFail($deviceId)]
            : FacialDevice::where('is_active', true)->get();

        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($devices as $device) {
            $deviceResults = [];
            
            foreach ($employees as $employee) {
                try {
                    $result = $this->syncEmployeeToDevice($device, $employee);
                    $deviceResults[] = $result;
                    
                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                } catch (\Exception $e) {
                    $deviceResults[] = [
                        'employee_no' => $employee['employee_no'],
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                    $errorCount++;
                }
            }
            
            $results[$device->name] = $deviceResults;
        }

        return response()->json([
            'success' => true,
            'message' => 'Synchronisation terminée',
            'device_name' => $devices->first()->name ?? 'Multiple terminaux',
            'total_processed' => count($employees) * count($devices),
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'results' => $results
        ]);
    }

    /**
     * Synchroniser tous les employés depuis la base de données
     * Endpoint: POST /api/hikvision/sync-all-employees
     */
    public function syncAllEmployees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'nullable|integer|exists:facial_devices,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Device ID invalide',
                'errors' => $validator->errors()
            ], 422);
        }

        $deviceId = $request->device_id;
        $devices = $deviceId 
            ? [FacialDevice::findOrFail($deviceId)]
            : FacialDevice::where('is_active', true)->get();

        // Récupérer tous les employés avec photo
        $personnel = Personnel::with('user')
            ->whereNotNull('photo_base64')
            ->orWhereNotNull('photo_path')
            ->get();

        $employees = $personnel->map(function ($person) {
            $photoBase64 = $person->photo_base64;
            
            // Si pas de base64, essayer de convertir le chemin
            if (!$photoBase64 && $person->photo_path) {
                $photoPath = public_path($person->photo_path);
                if (file_exists($photoPath)) {
                    $photoData = file_get_contents($photoPath);
                    $photoBase64 = 'data:image/jpeg;base64,' . base64_encode($photoData);
                }
            }

            return [
                'employee_no' => $person->matricule,
                'name' => ($person->prenom ?? '') . ' ' . ($person->nom ?? ''),
                'photo_base64' => $photoBase64,
            ];
        })->filter(fn($emp) => !empty($emp['photo_base64']))->toArray();

        if (empty($employees)) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun employé avec photo trouvé'
            ]);
        }

        return $this->syncEmployees(new Request([
            'employees' => $employees,
            'device_id' => $deviceId
        ]));
    }

    /**
     * Tester une photo en base64
     * Endpoint: POST /api/hikvision/test-photo
     */
    public function testPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo_base64' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Photo base64 requise'
            ], 422);
        }

        $photoBase64 = $request->photo_base64;

        try {
            // Valider le format base64
            if (!str_starts_with($photoBase64, 'data:image/')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format d\'image invalide. Attendu: data:image/...'
                ]);
            }

            // Extraire les données de l'image
            $imageInfo = $this->extractImageInfo($photoBase64);

            if (!$imageInfo['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $imageInfo['error'] ?? 'Image invalide'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Photo valide',
                'format' => $imageInfo['format'],
                'width' => $imageInfo['width'],
                'height' => $imageInfo['height'],
                'size' => $imageInfo['size'],
                'preview' => $this->generatePreview($photoBase64, 150)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Synchroniser un employé vers un terminal Hikvision spécifique
     */
    private function syncEmployeeToDevice(FacialDevice $device, array $employee): array
    {
        $baseUrl = "{$device->protocol}://{$device->ip_address}:{$device->port}";
        
        try {
            // Préparer le payload pour Hikvision ISAPI
            $payload = [
                'FaceInfo' => [
                    'FaceID' => uniqid('face_', true),
                    'employeeNo' => $employee['employee_no'],
                    'name' => $employee['name'] ?? $employee['employee_no'],
                    'facePic' => $employee['photo_base64'],
                    'Valid' => 'true',
                    'enableCardReader' => 'true',
                    'cardNo' => ''
                ]
            ];

            $url = "{$baseUrl}/ISAPI/Intelligent/FDLib/FaceDataRecord";
            
            $response = Http::withBasicAuth($device->username, $device->password)
                ->timeout(30)
                ->withHeaders(['Content-Type' => 'application/xml'])
                ->post($url, $this->arrayToXml($payload));

            if ($response->successful()) {
                // Vérifier la réponse Hikvision
                $xmlResponse = simplexml_load_string($response->body());
                $statusCode = (string) ($xmlResponse->statusCode ?? '200');

                if ($statusCode === '200' || $statusCode === '201') {
                    return [
                        'employee_no' => $employee['employee_no'],
                        'success' => true,
                        'face_id' => (string) ($xmlResponse->FaceID ?? ''),
                        'message' => 'Employé synchronisé avec succès'
                    ];
                } else {
                    return [
                        'employee_no' => $employee['employee_no'],
                        'success' => false,
                        'error' => "Erreur Hikvision: {$statusCode}",
                        'message' => (string) ($xmlResponse->statusString ?? 'Erreur inconnue')
                    ];
                }
            } else {
                return [
                    'employee_no' => $employee['employee_no'],
                    'success' => false,
                    'error' => "HTTP {$response->status()}",
                    'message' => 'Erreur de communication avec le terminal'
                ];
            }

        } catch (\Exception $e) {
            return [
                'employee_no' => $employee['employee_no'],
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erreur de synchronisation'
            ];
        }
    }

    /**
     * Extraire les informations d'une image base64
     */
    private function extractImageInfo(string $base64): array
    {
        try {
            // Enlever le préfixe data:image/
            if (preg_match('/^data:image\/(\w+);base64,(.*)$/', $base64, $matches)) {
                $format = $matches[1];
                $imageData = base64_decode($matches[2]);
                
                if ($imageData === false) {
                    return ['valid' => false, 'error' => 'Base64 invalide'];
                }

                // Obtenir les dimensions
                $imageInfo = getimagesizefromstring($imageData);
                
                if ($imageInfo === false) {
                    return ['valid' => false, 'error' => 'Image non valide'];
                }

                return [
                    'valid' => true,
                    'format' => strtoupper($format),
                    'width' => $imageInfo[0] ?? 0,
                    'height' => $imageInfo[1] ?? 0,
                    'size' => strlen($imageData)
                ];
            }

            return ['valid' => false, 'error' => 'Format base64 invalide'];

        } catch (\Exception $e) {
            return ['valid' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Générer un aperçu miniature
     */
    private function generatePreview(string $base64, int $maxSize = 150): string
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,(.*)$/', $base64, $matches)) {
                $imageData = base64_decode($matches[2]);
                $image = imagecreatefromstring($imageData);
                
                if ($image === false) {
                    return '';
                }

                $width = imagesx($image);
                $height = imagesy($image);
                
                // Calculer les nouvelles dimensions
                if ($width > $height) {
                    $newWidth = $maxSize;
                    $newHeight = (int) (($height / $width) * $maxSize);
                } else {
                    $newHeight = $maxSize;
                    $newWidth = (int) (($width / $height) * $maxSize);
                }

                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                ob_start();
                imagejpeg($resized, null, 85);
                $resizedData = ob_get_clean();
                
                imagedestroy($image);
                imagedestroy($resized);

                return 'data:image/jpeg;base64,' . base64_encode($resizedData);
            }
        } catch (\Exception $e) {
            Log::error('Preview generation error: ' . $e->getMessage());
        }

        return '';
    }

    /**
     * Convertir un tableau en XML pour Hikvision ISAPI
     */
    private function arrayToXml(array $array): string
    {
        $xml = new \SimpleXMLElement('<root/>');
        array_walk_recursive($array, function ($value, $key) use ($xml) {
            if (is_numeric($key)) {
                $child = $xml->addChild('item');
                $child->addAttribute('key', $key);
                $child->addChild('value', htmlspecialchars($value));
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        });
        return $xml->asXML();
    }
}
