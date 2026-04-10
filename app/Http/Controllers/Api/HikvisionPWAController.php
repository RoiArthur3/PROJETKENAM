<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HikvisionPWAController extends Controller
{
    /**
     * Récupérer la liste des employés pour le PWA
     * Endpoint: GET /api/hikvision/employees
     */
    public function getEmployees(Request $request)
    {
        try {
            $employees = User::where('role', 'employee')
                ->where('account_status', 'active')
                ->select([
                    'id',
                    'name',
                    'email',
                    'employee_id',
                    'phone',
                    'department',
                    'position'
                ])
                ->orderBy('name')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $employees,
                'count' => $employees->count()
            ]);

        } catch (\Exception $e) {
            Log::error('PWA: Failed to fetch employees', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch employees'
            ], 500);
        }
    }

    /**
     * Upload d'une photo vers la caméra Hikvision
     * Endpoint: POST /api/hikvision/upload-photo
     */
    public function uploadPhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,jpg,png|max:2048',
                'employeeId' => 'nullable|exists:users,id',
                'timestamp' => 'required|date',
                'tempName' => 'nullable|string|max:255',
                'tempNotes' => 'nullable|string|max:500'
            ]);

            $photo = $request->file('photo');
            $employeeId = $request->input('employeeId');
            $timestamp = $request->input('timestamp');
            $tempName = $request->input('tempName');
            $tempNotes = $request->input('tempNotes');
            $isPreRegistration = !$employeeId;

            // Récupérer les informations de l'employé si spécifié
            $employee = null;
            $employeeName = $tempName ?? 'Pré-enregistrement';

            if ($employeeId) {
                $employee = User::findOrFail($employeeId);
                $employeeName = $employee->name;
            }

            // Générer un nom de fichier unique
            $prefix = $isPreRegistration ? 'prereg_' : 'employee_';
            $filename = $prefix . ($employeeId ?? 'temp') . '_' . Str::uuid() . '.jpg';

            // Stocker la photo temporairement
            $tempPath = $photo->storeAs('temp/photos', $filename, 'local');
            $fullPath = storage_path('app/' . $tempPath);

            // Récupérer la configuration Hikvision
            $configPath = storage_path('app/hikvision_config.json');
            $hikvisionConfig = [];

            if (file_exists($configPath)) {
                $hikvisionConfig = json_decode(file_get_contents($configPath), true);
            }

            // Configuration par défaut si non trouvée
            $ip = $hikvisionConfig['ip'] ?? config('hikvision.default_ip', '192.168.1.70');
            $port = $hikvisionConfig['port'] ?? config('hikvision.default_port', '80');
            $username = $hikvisionConfig['username'] ?? config('hikvision.default_user', 'admin');
            $password = $hikvisionConfig['password'] ?? config('hikvision.default_password', '');
            $protocol = $hikvisionConfig['protocol'] ?? config('hikvision.default_protocol', 'http');

            // Envoyer la photo vers la caméra Hikvision
            $result = $this->uploadToHikvision($fullPath, $filename, $employeeName, $hikvisionConfig, $isPreRegistration);

            // Créer un enregistrement de pointage
            $pointageData = [
                'user_id' => $employeeId,
                'date_pointage' => now()->toDateString(),
                'heure_pointage' => now(),
                'statut' => 'present',
                'notes' => 'Photo PWA Hikvision - ' . $employeeName,
                'created_at' => $timestamp,
                'updated_at' => now()
            ];

            // Ajouter les informations de pré-enregistrement si applicable
            if ($isPreRegistration) {
                $pointageData['notes'] .= ' [Pré-enregistrement]';
                if ($tempNotes) {
                    $pointageData['notes'] .= ' - ' . $tempNotes;
                }
            }

            $pointage = \App\Models\Pointage::create($pointageData);

            // Nettoyer le fichier temporaire
            Storage::disk('local')->delete($tempPath);

            Log::info('PWA: Photo uploaded to Hikvision', [
                'employee_id' => $employeeId,
                'employee_name' => $employeeName,
                'is_pre_registration' => $isPreRegistration,
                'temp_name' => $tempName,
                'temp_notes' => $tempNotes,
                'filename' => $filename,
                'hikvision_result' => $result
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $isPreRegistration ? 'Pré-enregistrement synchronisé avec succès' : 'Photo uploaded successfully',
                'data' => [
                    'pointage_id' => $pointage->id,
                    'employee_name' => $employeeName,
                    'is_pre_registration' => $isPreRegistration,
                    'temp_name' => $tempName,
                    'temp_notes' => $tempNotes,
                    'filename' => $filename,
                    'hikvision_result' => $result
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('PWA: Failed to upload photo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload photo'
            ], 500);
        }
    }

    /**
     * Envoyer la photo vers la caméra Hikvision
     */
    private function uploadToHikvision($filePath, $filename, $employeeName, $config, $isPreRegistration = false)
    {
        try {
            $ip = $config['ip'] ?? '192.168.1.70';
            $port = $config['port'] ?? '80';
            $username = $config['username'] ?? 'admin';
            $password = $config['password'] ?? '';
            $protocol = $config['protocol'] ?? 'http';

            // URL pour l'upload de photo sur la caméra Hikvision
            $uploadUrl = "{$protocol}://{$ip}:{$port}/ISAPI/Intelligent/FaceDataRecord";

            // Préparer les données pour l'API Hikvision
            $xmlData = $this->generateFaceDataXML($employeeName, $filename, $isPreRegistration);

            // Utiliser cURL pour envoyer la photo
            $ch = curl_init();

            // Créer un fichier temporaire pour l'upload multipart
            $postFields = [
                'FaceDataRecord' => $xmlData,
                'FacePicture' => new \CURLFile($filePath, 'image/jpeg', $filename)
            ];

            curl_setopt_array($ch, [
                CURLOPT_URL => $uploadUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postFields,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: multipart/form-data',
                    'User-Agent: KENAM-Hikvision-PWA/1.0'
                ],
                CURLOPT_USERPWD => "{$username}:{$password}",
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CONNECTTIMEOUT => 10
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                Log::error('PWA: cURL error uploading to Hikvision', [
                    'error' => $error,
                    'http_code' => $httpCode,
                    'is_pre_registration' => $isPreRegistration
                ]);
                return ['success' => false, 'error' => $error];
            }

            // Parser la réponse XML
            $result = $this->parseHikvisionResponse($response);

            Log::info('PWA: Hikvision upload response', [
                'http_code' => $httpCode,
                'response' => $response,
                'parsed_result' => $result,
                'is_pre_registration' => $isPreRegistration
            ]);

            return [
                'success' => $httpCode === 200,
                'http_code' => $httpCode,
                'response' => $response,
                'parsed' => $result,
                'is_pre_registration' => $isPreRegistration
            ];

        } catch (\Exception $e) {
            Log::error('PWA: Exception uploading to Hikvision', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'is_pre_registration' => $isPreRegistration
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Générer le XML pour les données faciales Hikvision
     */
    private function generateFaceDataXML($employeeName, $filename, $isPreRegistration = false)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<FaceDataRecord version="2.0" xmlns="http://www.hikvision.com/ver20/XMLSchema">';
        $xml .= '<FaceInfo>';
        $xml .= '<faceID>' . ($isPreRegistration ? 'pre_' . Str::random(8) : Str::random(8)) . '</faceID>';
        $xml .= '<personName><![CDATA[' . $employeeName . ']]></personName>';
        $xml .= '<gender>unknown</gender>';
        $xml .= '<personID>' . ($isPreRegistration ? 'pre_' . Str::random(8) : Str::random(8)) . '</personID>';
        $xml .= '<picturePath>' . $filename . '</picturePath>';
        if ($isPreRegistration) {
            $xml .= '<personType>visitor</personType>';
        }
        $xml .= '</FaceInfo>';
        $xml .= '<FacePicture>';
        $xml .= '<pictureURL>' . $filename . '</pictureURL>';
        $xml .= '<pictureType>face</pictureType>';
        $xml .= '</FacePicture>';
        $xml .= '</FaceDataRecord>';

        return $xml;
    }

    /**
     * Parser la réponse XML de Hikvision
     */
    private function parseHikvisionResponse($response)
    {
        try {
            $xml = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);

            if ($xml === false) {
                return ['error' => 'Invalid XML response'];
            }

            $result = json_decode(json_encode($xml), true);

            return [
                'status_code' => $result['statusCode'] ?? 'unknown',
                'status_string' => $result['statusString'] ?? 'unknown',
                'error_code' => $result['errorCode'] ?? null,
                'error_message' => $result['errorMsg'] ?? null
            ];

        } catch (\Exception $e) {
            return ['error' => 'Failed to parse XML: ' . $e->getMessage()];
        }
    }

    /**
     * Récupérer la liste des photos récentes
     * Endpoint: GET /api/hikvision/photos
     */
    public function getPhotos(Request $request)
    {
        try {
            $limit = min($request->input('limit', 20), 100);
            $offset = $request->input('offset', 0);

            $photos = \App\Models\Pointage::where('notes', 'like', '%Photo PWA Hikvision%')
                ->with(['user:id,name,employee_id'])
                ->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get([
                    'id',
                    'user_id',
                    'date_pointage',
                    'heure_pointage',
                    'notes',
                    'created_at'
                ]);

            $total = \App\Models\Pointage::where('notes', 'like', '%Photo PWA Hikvision%')->count();

            return response()->json([
                'status' => 'success',
                'data' => $photos,
                'pagination' => [
                    'total' => $total,
                    'limit' => $limit,
                    'offset' => $offset,
                    'has_more' => ($offset + $limit) < $total
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('PWA: Failed to fetch photos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch photos'
            ], 500);
        }
    }
}
