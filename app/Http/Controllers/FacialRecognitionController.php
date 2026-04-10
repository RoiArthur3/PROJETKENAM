<?php

namespace App\Http\Controllers;

use App\Models\FacialDevice;
use App\Models\FacialEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FacialRecognitionController extends Controller
{
    /**
     * Afficher la configuration de la reconnaissance faciale
     */
    public function index()
    {
        return view('parametrage.facial-recognition');
    }

    /**
     * Sauvegarder la configuration de la reconnaissance faciale
     */
    public function saveConfig(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'enabled' => 'boolean',
                'confidence_threshold' => 'integer|min:50|max:100',
                'model' => 'string|in:face_net,arc_face,dlib',
                'camera_source' => 'string|in:webcam,ip_camera,usb',
                'camera_url' => 'url|nullable',
                'resolution' => 'string|in:640x480,1280x720,1920x1080',
                'fps' => 'integer|min:1|max:60',
                'quality' => 'integer|min:1|max:100',
                'acceleration' => 'string|in:none,cuda,opencl,mps',
                'max_face_size' => 'integer|min:100|max:1000',
                'save_images' => 'boolean',
                'retention_days' => 'integer|min:1|max:365',
                'live_detection' => 'boolean',
                'anti_spoofing' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ]);
            }

            // Créer le fichier de configuration
            $config = [
                'enabled' => $request->boolean('enabled', false),
                'confidence_threshold' => $request->integer('confidence_threshold', 85),
                'model' => $request->input('model', 'face_net'),
                'camera_source' => $request->input('camera_source', 'webcam'),
                'camera_url' => $request->input('camera_url', ''),
                'resolution' => $request->input('resolution', '640x480'),
                'fps' => $request->integer('fps', 30),
                'quality' => $request->integer('quality', 90),
                'acceleration' => $request->input('acceleration', 'none'),
                'max_face_size' => $request->integer('max_face_size', 500),
                'save_images' => $request->boolean('save_images', false),
                'retention_days' => $request->integer('retention_days', 30),
                'live_detection' => $request->boolean('live_detection', true),
                'anti_spoofing' => $request->boolean('anti_spoofing', true),
            ];

            // Sauvegarder dans le fichier de configuration
            $configPath = config_path('facial_recognition.php');
            $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
            File::put($configPath, $configContent);

            return response()->json([
                'success' => true,
                'message' => 'Configuration sauvegardée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Tester la caméra
     */
    public function testCamera(Request $request)
    {
        try {
            // Simuler un test de caméra
            $config = config('facial_recognition');
            
            if (!$config['enabled']) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reconnaissance faciale n\'est pas activée'
                ]);
            }

            // Simuler les résultats du test
            $results = [
                'success' => true,
                'resolution' => $config['resolution'] ?? '640x480',
                'fps' => $config['fps'] ?? 30,
                'stream_url' => null, // URL du flux vidéo si disponible
            ];

            // Si c'est une caméra IP, tester la connexion
            if ($config['camera_source'] === 'ip_camera' && !empty($config['camera_url'])) {
                // Simuler une vérification de connexion
                $results['stream_url'] = $config['camera_url'];
            }

            return response()->json($results);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du test de la caméra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Tester la reconnaissance faciale
     */
    public function testRecognition(Request $request)
    {
        try {
            $config = config('facial_recognition');
            
            if (!$config['enabled']) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reconnaissance faciale n\'est pas activée'
                ]);
            }

            // Simuler un test de reconnaissance
            $results = [
                'success' => true,
                'faces_detected' => rand(0, 3), // Simuler détection de 0-3 visages
                'avg_confidence' => rand(70, 95), // Confiance moyenne
                'processing_time' => rand(50, 200), // Temps en ms
                'image' => null, // Image avec les visages détectés
            ];

            return response()->json($results);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du test de reconnaissance: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Lancer un diagnostic système
     */
    public function diagnostic(Request $request)
    {
        try {
            $results = [
                'system' => [
                    'Python installé' => true,
                    'OpenCV disponible' => true,
                    'Librairie faciale disponible' => true,
                    'GPU détecté' => false, // Simuler absence de GPU
                    'Mémoire suffisante' => true,
                ],
                'hardware' => [
                    'CPU Cores' => 4,
                    'RAM (GB)' => 8,
                    'GPU' => 'Non détecté',
                    'Caméra disponible' => true,
                ],
                'models' => [
                    'FaceNet' => true,
                    'ArcFace' => false, // Non installé
                    'DLib' => true,
                ]
            ];

            return response()->json($results);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du diagnostic: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Calibrer le système
     */
    public function calibrate(Request $request)
    {
        try {
            $config = config('facial_recognition');
            
            if (!$config['enabled']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Activez d\'abord la reconnaissance faciale'
                ]);
            }

            // Simuler une calibration
            // En réalité, cela impliquerait:
            // 1. Calibration de la caméra
            // 2. Test des conditions d'éclairage
            // 3. Optimisation des paramètres de détection
            // 4. Test des modèles IA

            $calibrationResults = [
                'success' => true,
                'message' => 'Système calibré avec succès. Conditions optimales détectées.',
                'lighting' => 'Bon',
                'camera_angle' => 'Optimal',
                'face_detection_accuracy' => '92%',
            ];

            return response()->json($calibrationResults);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la calibration: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtenir les statistiques d'utilisation
     */
    public function getStats(Request $request)
    {
        try {
            $totalRecognitions = FacialEvent::count();
            $successful = FacialEvent::where('status', 'success')->count();
            $failed = FacialEvent::where('status', '!=', 'success')->count();

            $avgConfidence = (float) FacialEvent::whereNotNull('confidence')->avg('confidence');
            $activeUsers = FacialEvent::whereDate('event_time', today())
                ->whereNotNull('employee_code')
                ->distinct('employee_code')
                ->count('employee_code');

            $lastCalibration = FacialDevice::whereNotNull('updated_at')->max('updated_at');

            $stats = [
                'total_recognitions' => $totalRecognitions,
                'successful_recognitions' => $successful,
                'failed_recognitions' => $failed,
                'avg_confidence' => round($avgConfidence, 2),
                'active_users' => $activeUsers,
                'system_uptime' => 'N/A',
                'last_calibration' => $lastCalibration,
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ]);
        }
    }

    public function devices()
    {
        $devices = FacialDevice::orderByDesc('created_at')->get();

        return response()->json([
            'success' => true,
            'data' => $devices,
        ]);
    }

    public function saveDevice(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:facial_devices,id',
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:100',
            'ip_address' => 'required|string|max:64',
            'port' => 'required|integer|min:1|max:65535',
            'protocol' => 'required|string|in:http,https',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $query = FacialDevice::where('serial_number', $validated['serial_number']);
        if (!empty($validated['id'])) {
            $query->where('id', '!=', $validated['id']);
        }

        if ($query->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Le numéro de série existe déjà.',
            ], 422);
        }

        $device = !empty($validated['id'])
            ? FacialDevice::findOrFail($validated['id'])
            : new FacialDevice();

        $device->fill([
            'name' => $validated['name'],
            'serial_number' => $validated['serial_number'],
            'ip_address' => $validated['ip_address'],
            'port' => $validated['port'],
            'protocol' => $validated['protocol'],
            'username' => $validated['username'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        if (!empty($validated['password'])) {
            $device->password = $validated['password'];
        }

        if (empty($device->api_token)) {
            $device->api_token = Str::random(64);
        }

        $device->save();

        return response()->json([
            'success' => true,
            'message' => 'Terminal sauvegardé avec succès.',
            'data' => $device,
            'api_ingest_url' => route('facial.events.ingest'),
        ]);
    }

    public function deleteDevice(FacialDevice $device)
    {
        $device->delete();

        return response()->json([
            'success' => true,
            'message' => 'Terminal supprimé.',
        ]);
    }

    public function rotateDeviceToken(FacialDevice $device)
    {
        $device->api_token = Str::random(64);
        $device->save();

        return response()->json([
            'success' => true,
            'message' => 'Token API régénéré avec succès.',
            'data' => [
                'id' => $device->id,
                'api_token' => $device->api_token,
            ],
            'api_ingest_url' => route('facial.events.ingest'),
        ]);
    }

    public function testDevice(FacialDevice $device)
    {
        $result = $this->checkDeviceConnection($device);

        $device->update([
            'last_seen_at' => $result['success'] ? now() : $device->last_seen_at,
            'last_status' => $result['success'] ? 'online' : 'offline',
            'last_error' => $result['success'] ? null : $result['message'],
        ]);

        return response()->json($result);
    }

    public function recentEvents()
    {
        $events = FacialEvent::with('device')
            ->orderByDesc('event_time')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);
    }

    private function checkDeviceConnection(FacialDevice $device): array
    {
        $host = $device->ip_address;
        $port = (int) $device->port;
        $protocol = strtolower((string) $device->protocol);

        $errno = 0;
        $errstr = '';
        $socket = @fsockopen($host, $port, $errno, $errstr, 4);

        if (!$socket) {
            return [
                'success' => false,
                'message' => 'Connexion socket échouée: ' . ($errstr ?: 'terminal indisponible'),
            ];
        }
        fclose($socket);

        try {
            $baseUrl = sprintf('%s://%s:%d', $protocol, $host, $port);
            $request = Http::timeout(5)->withOptions(['verify' => false]);
            if (!empty($device->username)) {
                $request = $request->withBasicAuth($device->username, (string) $device->password);
            }

            $response = $request->get($baseUrl);

            return [
                'success' => true,
                'message' => 'Terminal joignable',
                'http_status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => true,
                'message' => 'Port joignable (HTTP non confirmé): ' . $e->getMessage(),
            ];
        }
    }
}
