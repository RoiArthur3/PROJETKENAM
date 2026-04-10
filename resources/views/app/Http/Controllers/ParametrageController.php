<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\EntrepriseSettings;
use App\Models\OperationalService;

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
            'services' => $this->safeCount('App\Models\ServiceOperationnel'),
            'types_operations' => $this->safeCount('App\Models\TypeOperation'),
            'operations_total' => $this->safeCount('App\Models\Operation'),
            'operations_pending' => $this->safeCountWhere('App\Models\Operation', 'statut_courant', 'pending_validation'),
            'operations_approved' => $this->safeCountWhere('App\Models\Operation', 'statut_courant', 'terminee'),
        ];

        // Récupérer les informations de l'entreprise
        try {
            $entreprise = EntrepriseSettings::getActive();
        } catch (\Exception $e) {
            $entreprise = null;
        }

        return view('parametrage.index', compact('stats', 'entreprise'));
    }

    private function safeCount($model)
    {
        try {
            return $model::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function safeCountWhere($model, $column, $value)
    {
        try {
            return $model::where($column, $value)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Afficher une section spécifique de paramétrage
     */
    public function show($section)
    {
        switch($section) {
            case 'entreprise':
                return $this->entreprise();
            case 'email':
                $config = \App\Models\Setting::get('email_config', []);
                return view('parametrage.email', compact('config'));
            case 'systeme':
                $config = \App\Models\Setting::get('system_config', []);
                return view('parametrage.systeme', compact('config'));
            case 'services':
                $services = \App\Models\ServiceOperationnel::all(); // Ou Service::all() selon votre modèle préféré
                return view('parametrage.services', compact('services'));
            default:
                abort(404);
        }
    }

    // ... (update method remains largely the same, dispatching to save methods)

    public function saveEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mail_mailer' => 'required|string|in:smtp,mail,sendmail',
            'mail_host' => 'required|string|max:255',
            'mail_port' => 'required|integer|min:1|max:65535',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'mail_username' => 'required|string|max:255', // email validation removed for username flexibility
            'mail_password' => 'required|string|max:255',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $config = [
                'mailer' => $request->mail_mailer,
                'host' => $request->mail_host,
                'port' => $request->mail_port,
                'encryption' => $request->mail_encryption,
                'username' => $request->mail_username,
                'password' => $request->mail_password,
                'from_address' => $request->mail_from_address,
                'from_name' => $request->mail_from_name,
            ];

            \App\Models\Setting::set('email_config', $config);

            // Update runtime config for immediate testing if needed (optional)
            // config(['mail.mailers.smtp.host' => ...]);

            return response()->json(['success' => true, 'message' => 'Configuration email enregistrée en base de données!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    public function saveSysteme(Request $request)
    {
        // Validation...
        $validator = Validator::make($request->all(), [
            'app_timezone' => 'required|string|max:100',
            'app_locale' => 'required|string|in:fr,en',
            'session_lifetime' => 'required|integer',
            // ... other fields
        ]);

        // Simpler validation for demo purposes or exact same as before

        try {
            $config = $request->except(['_token']);
            \App\Models\Setting::set('system_config', $config);

            return response()->json(['success' => true, 'message' => 'Paramètres système enregistrés en base de données!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Tester la connexion email
     */
    public function testEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_email' => 'required|email',
            'test_subject' => 'required|string|max:255',
            'test_message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Configuration temporaire pour le test
            config([
                'mail.mailer' => $request->mail_mailer ?? 'smtp',
                'mail.host' => $request->mail_host ?? 'smtp.gmail.com',
                'mail.port' => $request->mail_port ?? 587,
                'mail.encryption' => $request->mail_encryption ?? 'tls',
                'mail.username' => $request->mail_username,
                'mail.password' => $request->mail_password,
                'mail.from.address' => $request->mail_from_address ?? 'test@example.com',
                'mail.from.name' => $request->mail_from_name ?? 'Test Email'
            ]);

            // Envoi de l'email de test
            \Mail::raw($request->test_message, function ($message) use ($request) {
                $message->to($request->test_email)
                       ->subject($request->test_subject);
            });

            return response()->json([
                'success' => true,
                'message' => 'Email de test envoyé avec succès!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Échec de l\'envoi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sauvegarder un fichier de configuration
     */
    private function saveConfigFile($type, $config)
    {
        $configPath = config_path('custom/' . $type . '.json');
        $configDir = dirname($configPath);

        if (!File::exists($configDir)) {
            File::makeDirectory($configDir, 0755, true);
        }

        File::put($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Charger un fichier de configuration
     */
    private function loadConfigFile($type)
    {
        $configPath = config_path('custom/' . $type . '.json');

        if (File::exists($configPath)) {
            $content = File::get($configPath);
            return json_decode($content, true);
        }

        return [];
    }

    /**
     * Afficher la page de paramétrage de l'entreprise
     */
    public function entreprise()
    {
        try {
            $entreprise = EntrepriseSettings::getActive();
            // S'assurer qu'on a au moins un objet vide pour éviter les erreurs dans la vue
            if (!$entreprise) {
                $entreprise = new EntrepriseSettings();
            }
        } catch (\Exception $e) {
            // Si le modèle n'existe pas, créer un objet stdClass avec des propriétés par défaut
            $entreprise = (object) [
                'nom_entreprise' => 'KENAM Services',
                'sigle' => 'KENAM',
                'adresse' => '',
                'telephone' => '',
                'email_contact' => '',
                'site_web' => '',
                'rccm' => '',
                'ifu' => '',
                'cnss' => '',
                'compte_bancaire' => '',
                'logo_path' => null,
                'id' => null
            ];
        }

        return view('parametrage.entreprise', compact('entreprise'));
    }

    /**
     * Sauvegarder les paramètres de l'entreprise
     */
    public function saveEntreprise(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_entreprise' => 'required|string|max:255',
            'sigle' => 'required|string|max:50',
            'adresse' => 'required|string',
            'telephone' => 'required|string|max:20',
            'email_contact' => 'required|email|max:255',
            'site_web' => 'required|string|max:255',
            'rccm' => 'required|string|max:50',
            'ifu' => 'nullable|string|max:50',
            'cnss' => 'nullable|string|max:50',
            'compte_bancaire' => 'required|string|max:100',
            'logo_entreprise' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $entreprise = EntrepriseSettings::getActive();
            if (!$entreprise) {
                $entreprise = new EntrepriseSettings();
                $entreprise->actif = true;
            }

            $data = $request->except(['logo_entreprise', '_token']);

            // Gestion de l'upload du logo
            if ($request->hasFile('logo_entreprise')) {
                // Supprimer l'ancien logo si nécessaire
                if ($entreprise->logo_path && File::exists(public_path('storage/' . $entreprise->logo_path))) {
                    try {
                        File::delete(public_path('storage/' . $entreprise->logo_path));
                    } catch (\Exception $e) {
                        // Ignorer si échec de suppression
                    }
                }

                $file = $request->file('logo_entreprise');
                $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('logos', $filename, 'public');
                $data['logo_path'] = 'logos/' . $filename;
            }

            $entreprise->fill($data);
            $entreprise->save();

            return response()->json([
                'success' => true,
                'message' => 'Informations de l\'entreprise mises à jour avec succès.',
                'logo_url' => $entreprise->logo_path ? asset('storage/' . $entreprise->logo_path) : null
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }
}
