<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\EntrepriseSettings;
use App\Models\OperationalService;
use App\Services\HikvisionFaceSyncService;

class ParametrageController extends Controller
{
    /**
     * Afficher la page principale de paramétrage avec toutes les cartes
     */
    public function index()
    {
        // Récupérer les statistiques et informations de la base de données avec gestion d'erreur
        $stats = [
            'users' => $this->safeCount('App\Models\User'),
            'active_users' => $this->safeCountWhere('App\Models\User', 'is_active', true),
            'services' => $this->safeCount('App\Models\ServiceOperationnel'),
            'types_operations' => $this->safeCount('App\Models\TypeOperation'),
            'operations_total' => $this->safeCount('App\Models\Operation'),
            'operations_pending' => $this->safeCountWhere('App\Models\Operation', 'statut_courant', 'pending_validation'),
            'operations_approved' => $this->safeCountWhere('App\Models\Operation', 'statut_courant', 'terminee'),
            'operations_rejected' => $this->safeCountWhere('App\Models\Operation', 'statut_courant', 'rejected'),
        ];

        // Récupérer les informations de l'entreprise pour l'affichage
        $entreprise = EntrepriseSettings::first() ?: new \stdClass();
        $entreprise->nom_entreprise = 'Non configuré';
        $entreprise->logo_path = null;

        return view('parametrage.index', compact('stats', 'entreprise'));
    }

    /**
     * Compter en toute sécurité les enregistrements d'un modèle
     */
    private function safeCount($model)
    {
        try {
            return $model::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Compter en toute sécurité avec une condition where
     */
    private function safeCountWhere($model, $column, $value)
    {
        try {
            return $model::where($column, $value)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Afficher la page de paramétrage de l'entreprise
     */
    public function entreprise()
    {
        $entreprise = EntrepriseSettings::first();
        return view('parametrage.entreprise', compact('entreprise'));
    }

    /**
     * Sauvegarder les paramètres de l'entreprise
     */
    public function saveEntreprise(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'rc' => 'nullable|string|max:255',
            'cc' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $entreprise = EntrepriseSettings::first();
            if (!$entreprise) {
                $entreprise = new EntrepriseSettings();
            }

            // Traiter l'upload du logo
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/uploads/logo', $filename);
                $validated['logo_path'] = $path;
            }

            // Retirer le champ 'logo' du validated car on utilise 'logo_path'
            unset($validated['logo']);

            $entreprise->fill($validated);
            $entreprise->save();

            $response = [
                'success' => true,
                'message' => 'Informations de l\'entreprise sauvegardées avec succès'
            ];

            if ($request->hasFile('logo')) {
                $response['logo_url'] = Storage::url('uploads/logo/' . $filename ?? '');
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher la page de configuration email
     */
    public function email()
    {
        return view('parametrage.email');
    }

    /**
     * Sauvegarder la configuration email
     */
    public function saveEmail(Request $request)
    {
        $validated = $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string'
        ]);

        try {
            $this->updateEnvFile([
                'MAIL_HOST' => $validated['mail_host'],
                'MAIL_PORT' => $validated['mail_port'],
                'MAIL_USERNAME' => $validated['mail_username'],
                'MAIL_PASSWORD' => $validated['mail_password'],
                'MAIL_ENCRYPTION' => $validated['mail_encryption'] ?? 'tls',
                'MAIL_FROM_ADDRESS' => $validated['mail_from_address'],
                'MAIL_FROM_NAME' => '"' . $validated['mail_from_name'] . '"'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Configuration email sauvegardée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sauvegarder la configuration SMS
     */
    public function saveSms(Request $request)
    {
        $validated = $request->validate([
            'sms_provider' => 'nullable|string|in:,smseco_revendeur,netsmspro,smseco,orange,mtn,twilio,infobip,custom',
            'sms_sender_id' => 'nullable|string|max:20',
            'sms_is_active' => 'boolean',
            'sms_api_key' => 'nullable|string',
            'sms_api_secret' => 'nullable|string',
            'sms_reseller_id' => 'nullable|string',
            'sms_api_url' => 'nullable|url'
        ]);

        try {
            $entreprise = EntrepriseSettings::first();
            if (!$entreprise) {
                $entreprise = new EntrepriseSettings();
            }

            $entreprise->fill($validated);
            $entreprise->save();

            return response()->json([
                'success' => true,
                'message' => 'Configuration SMS sauvegardée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tester la configuration email
     */
    public function testEmail(Request $request)
    {
        try {
            $to = $request->input('test_email');
            if (!$to) {
                return back()->with('error', 'Veuillez fournir une adresse email de test');
            }

            Mail::raw('Ceci est un email de test depuis KENAM Services', function ($message) use ($to) {
                $message->to($to)
                    ->subject('Email de Test - KENAM Services');
            });

            return back()->with('success', 'Email de test envoyé avec succès à ' . $to);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
        }
    }

    /**
     * Afficher la page de paramètres généraux
     */
    public function general()
    {
        return view('parametrage.general');
    }

    /**
     * Sauvegarder les paramètres généraux
     */
    public function saveGeneral(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'app_timezone' => 'required|string',
            'app_locale' => 'required|string|max:5'
        ]);

        try {
            $this->updateEnvFile([
                'APP_NAME' => '"' . $validated['app_name'] . '"',
                'APP_URL' => $validated['app_url'],
                'APP_TIMEZONE' => $validated['app_timezone'],
                'APP_LOCALE' => $validated['app_locale']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Paramètres généraux sauvegardés avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher la page de paramètres système
     */
    public function systeme()
    {
        return view('parametrage.systeme');
    }

    /**
     * Sauvegarder les paramètres système
     */
    public function saveSysteme(Request $request)
    {
        $validated = $request->validate([
            'debug_mode' => 'boolean',
            'maintenance_mode' => 'boolean',
            'log_level' => 'required|string|in:debug,info,warning,error,critical'
        ]);

        try {
            $this->updateEnvFile([
                'APP_DEBUG' => $validated['debug_mode'] ? 'true' : 'false',
                'APP_MAINTENANCE_DRIVER' => $validated['maintenance_mode'] ? 'file' : 'array',
                'LOG_LEVEL' => $validated['log_level']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Paramètres système sauvegardés avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher la page de configuration des services
     */
    public function services()
    {
        try {
            $services = \App\Models\ServiceOperationnel::all();
            return view('parametrage.services', compact('services'));
        } catch (\Exception $e) {
            return redirect()->route('parametrage.index')->with('error', 'Erreur lors du chargement des services: ' . $e->getMessage());
        }
    }

    /**
     * Sauvegarder la configuration des services
     */
    public function saveServices(Request $request)
    {
        $validated = $request->validate([
            'services' => 'required|array',
            'services.*.id' => 'required|integer|exists:service_operationnels,id',
            'services.*.nom' => 'required|string|max:255',
            'services.*.description' => 'nullable|string',
            'services.*.est_actif' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['services'] as $serviceData) {
                $service = \App\Models\ServiceOperationnel::find($serviceData['id']);
                if ($service) {
                    $service->update([
                        'nom' => $serviceData['nom'],
                        'description' => $serviceData['description'] ?? null,
                        'est_actif' => $serviceData['est_actif'] ?? true
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Configuration des services sauvegardée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la sauvegarde: ' . $e->getMessage());
        }
    }

    /**
     * Afficher la page de configuration Hikvision
     */
    public function hikvisionConfig()
    {
        return view('parametrage.hikvision.config');
    }

    /**
     * Sauvegarder la configuration Hikvision
     */
    public function saveHikvisionConfig(Request $request)
    {
        $data = $request->validate([
            'hikvision_ip' => 'required|ip',
            'hikvision_port' => 'required|integer',
            'hikvision_username' => 'required|string',
            'hikvision_password' => 'required|string',
            'hikvision_enabled' => 'boolean'
        ]);

        $this->updateEnvFile([
            'HIKVISION_IP' => $data['hikvision_ip'],
            'HIKVISION_PORT' => $data['hikvision_port'],
            'HIKVISION_USERNAME' => $data['hikvision_username'],
            'HIKVISION_PASSWORD' => $data['hikvision_password'],
            'HIKVISION_ENABLED' => $data['hikvision_enabled'] ?? false
        ]);

        return back()->with('success', 'Configuration Hikvision sauvegardée avec succès');
    }

    /**
     * Tester la connexion Hikvision
     */
    public function testHikvision(Request $request, HikvisionFaceSyncService $syncService)
    {
        try {
            // Utiliser soit les données du formulaire, soit la config actuelle
            $deviceId = $request->input('device_id');
            $result = $syncService->testConnection($deviceId);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'http_code' => $result['http_code'] ?? ($result['success'] ? 200 : 0),
                'device_info' => $result['device_info'] ?? null,
                'step' => $result['step'] ?? 'unknown'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de connexion: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher la page de diagnostic Hikvision
     */
    public function hikvisionDiagnostic()
    {
        $config = [
            'ip' => env('HIKVISION_IP', '192.168.1.70'),
            'port' => env('HIKVISION_PORT', 80),
            'username' => env('HIKVISION_USERNAME', 'admin'),
            'password' => env('HIKVISION_PASSWORD', ''),
            'timeout' => 5,
            'endpoint' => '/ISAPI/Intelligent/FacialRecognition/faceInfo',
        ];
        return view('parametrage.hikvision.diagnostic', compact('config'));
    }


    /**
     * Exécuter le diagnostic Hikvision
     */
    public function runHikvisionDiagnostic(Request $request, HikvisionFaceSyncService $syncService)
    {
        try {
            $results = [
                'config' => [
                    'timeout' => 5
                ],
                'environment' => [
                    'php_version' => PHP_VERSION,
                    'curl_enabled' => function_exists('curl_version'),
                    'allow_url_fopen' => ini_get('allow_url_fopen') ? 'Activé' : 'Désactivé'
                ],
                'connectivity' => [
                    'success' => false,
                    'message' => 'En attente...'
                ]
            ];

            // Test réel
            $connectionTest = $syncService->testConnection();
            $results['connectivity'] = [
                'success' => $connectionTest['success'],
                'message' => $connectionTest['message'],
                'step' => $connectionTest['step'] ?? 'unknown'
            ];

            if ($connectionTest['success']) {
                $results['device_info'] = $connectionTest['device_info'];
            }

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du diagnostic: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lancer la synchronisation des employés
     */
    public function hikvisionSync(HikvisionFaceSyncService $syncService)
    {
        try {
            $results = $syncService->syncAllPendingEmployees();
            
            return response()->json([
                'success' => true,
                'message' => 'Synchronisation terminée',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la synchronisation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Proxy pour récupérer l'image de la caméra (Snapshot)
     */
    public function proxySnapshot(Request $request, HikvisionFaceSyncService $syncService)
    {
        $deviceId = $request->get('device_id');
        $result = $syncService->getSnapshot($deviceId);

        if ($result['success']) {
            return response($result['image'])
                ->header('Content-Type', $result['content_type']);
        }

        // Image d'erreur par défaut (gris clair avec texte)
        return response()->file(public_path('images/camera-error.png'), [
            'Content-Type' => 'image/png',
        ])->setStatusCode(404);
    }

    /**
     * Vérifier le statut de connexion pour le dashboard
     */
    public function getDeviceStatus(HikvisionFaceSyncService $syncService)
    {
        $device = \App\Models\FacialDevice::where('is_active', true)->first();
        if (!$device) {
            return response()->json(['status' => 'offline', 'message' => 'Aucun dispositif']);
        }

        $result = $syncService->testConnection($device->id);
        
        return response()->json([
            'status' => $result['success'] ? 'online' : 'offline',
            'last_seen' => $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Jamais'
        ]);
    }

    /**
     * Mettre à jour le fichier .env
     */
    private function updateEnvFile($data)
    {
        $envFile = file_get_contents(base_path('.env'));

        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envFile)) {
                $envFile = preg_replace($pattern, $replacement, $envFile);
            } else {
                $envFile .= "\n{$key}={$value}";
            }
        }

        file_put_contents(base_path('.env'), $envFile);
    }
}
