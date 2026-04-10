<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Models\Role;
use App\Models\TypeOperation;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user && in_array($user->role, ['admin', 'superadmin'])) {
                return $next($request);
            }
            abort(403, 'Accès non autorisé.');
        });
    }

    /**
     * Affiche le tableau de bord des paramètres
     */
    public function index()
    {
        $services = Service::orderBy('nom')->get();
        $defaultChain = Setting::get('operations.default_chain', []);

        return view('settings.index', compact('services', 'defaultChain'));
    }

    // ==================== Gestion des utilisateurs ====================

    public function usersIndex()
    {
        $users = User::orderBy('name')->paginate(15);
        return view('settings.users.index', compact('users'));
    }

    public function createUser()
    {
        $roles = [
            'superadmin' => 'Super Administrateur',
            'admin' => 'Administrateur',
            'moderator' => 'Modérateur',
            'user' => 'Agent (Utilisateur Simple)',  // Clarifié
        ];
        $allModules = config('submodules', []);
        return view('settings.users.create', compact('roles', 'allModules'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:10|regex:/^[0-9]+$/|unique:users,telephone',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:superadmin,admin,moderator,user',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
            'submodules' => 'nullable|array',
            'submodules.*' => 'string',
            'admin_full_access' => 'nullable|boolean',
            'admin_modules' => 'nullable|array',
            'admin_modules.*' => 'string',
            'moderator_additional_access' => 'nullable|boolean',
            'moderator_modules' => 'nullable|array',
            'moderator_modules.*' => 'string',
        ]);

        // Créer un email basé sur le numéro de téléphone si aucun email n'est fourni
        $email = $validated['email'] ?? ($validated['phone'] . '@kenamservices.net');

        $user = User::create([
            'name' => $validated['name'],
            'telephone' => $validated['phone'],
            'email' => $email,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        [$selectedModules, $selectedSubmodules] = $this->sanitizeModulesAndSubmodules(
            $validated['role'],
            $request->input('modules', []),
            $request->input('submodules', [])
        );

        \App\Services\UserPermissionService::updateUserPermissions($user, $selectedModules, $selectedSubmodules);

        return redirect()->route('settings.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Mettre à jour la configuration des modules pour un admin
     */
    private function updateAdminModulesConfig($userId, $config)
    {
        $adminConfig = config('admin_modules', []);
        $adminConfig['user_' . $userId] = $config;

        // Sauvegarder dans un fichier de configuration
        $configPath = config_path('admin_modules.php');
        $content = "<?php\n\nreturn " . var_export($adminConfig, true) . ";\n";
        file_put_contents($configPath, $content);
    }

    /**
     * Synchroniser les permissions de modules pour un utilisateur (modérateur)
     * Sauvegarde dans model_has_permissions (table Spatie) pour que User::hasModulePermission() fonctionne
     */
    private function syncModulePermissions(User $user, array $selectedModules)
    {
        // Supprimer toutes les permissions actuelles de cet utilisateur
        \DB::table('model_has_permissions')
            ->where('model_id', $user->id)
            ->where('model_type', 'App\\Models\\User')
            ->delete();

        // Insérer les nouvelles permissions
        foreach ($selectedModules as $moduleCode) {
            $permissionName = $moduleCode . '.access';
            $permission = \DB::table('permissions')->where('name', $permissionName)->first();

            if ($permission) {
                \DB::table('model_has_permissions')->insert([
                    'permission_id' => $permission->id,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $user->id,
                ]);
            }
        }

        Log::info("Permissions synchronisées pour user {$user->id}: " . implode(', ', $selectedModules));
    }

    public function editUser(User $user)
    {
        $roles = [
            'superadmin' => 'Super Administrateur',
            'admin' => 'Administrateur',
            'moderator' => 'Modérateur',
            'user' => 'Agent (Utilisateur Simple)',  // Clarifié
        ];
        $allModules = config('submodules', []);
        return view('settings.users.edit', compact('user', 'roles', 'allModules'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:10',
                'regex:/^[0-9]+$/',
                Rule::unique('users', 'telephone')->ignore($user->id),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,user,superadmin,moderator',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
            'submodules' => 'nullable|array',
            'submodules.*' => 'string',
            'moderator_modules' => 'nullable|array',
            'moderator_modules.*' => 'string',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'telephone' => $validated['phone'],
            'role' => $validated['role'] ?? null,
        ];

        // Mettre à jour l'email seulement s'il est fourni
        if (!empty($validated['email'])) {
            $updateData['email'] = $validated['email'];
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        [$selectedModules, $selectedSubmodules] = $this->sanitizeModulesAndSubmodules(
            $validated['role'],
            $request->input('modules', []),
            $request->input('submodules', [])
        );

        \App\Services\UserPermissionService::updateUserPermissions($user, $selectedModules, $selectedSubmodules);

        return redirect()->route('settings.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroyUser(User $user)
    {
        // Vérifier si l'utilisateur essaie de se supprimer lui-même
        $currentUserId = Auth::id();
        if ($user->getKey() == $currentUserId) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Supprimer l'utilisateur
        $user->delete();

        return redirect()->route('settings.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    // ==================== Rôles et permissions ====================

    public function rolesIndex()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();

        // Debug pour vérifier les données
        \Log::info('Roles count: ' . $roles->count());
        \Log::info('Roles data: ' . $roles->toJson());

        return view('settings.roles.index', compact('roles'));
    }

    // ==================== Paramètres généraux ====================

    public function generalSettings()
    {
        $settings = [
            'app_name' => config('app.name'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            // Ajoutez d'autres paramètres généraux ici
        ];

        return view('settings.general.index', compact('settings'));
    }

    public function updateGeneralSettings(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'timezone' => 'required|timezone',
            'locale' => 'required|in:fr,en,es',
        ]);

        // Mise à jour des paramètres dans le fichier .env ou dans la table settings
        // Cette partie dépend de votre implémentation

        return redirect()->route('settings.general.index')
            ->with('success', 'Paramètres généraux mis à jour avec succès.');
    }

    // ==================== Paramètres de l'entreprise ====================

    public function companySettings()
    {
        $company = [
            'name' => config('company.name', ''),
            'address' => config('company.address', ''),
            'phone' => config('company.phone', ''),
            'email' => config('company.email', ''),
            'siret' => config('company.siret', ''),
        ];

        return view('settings.company.index', compact('company'));
    }

    public function updateCompanySettings(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'siret' => 'required|string|max:14',
        ]);

        // Mise à jour des paramètres de l'entreprise
        // Cette partie dépend de votre implémentation

        return redirect()->route('settings.company.index')
            ->with('success', 'Paramètres de l\'entreprise mis à jour avec succès.');
    }

    // ==================== Paramètres de notification ====================

    public function notificationSettings()
    {
        $notifications = [
            'email_enabled' => config('notifications.email.enabled', true),
            'sms_enabled' => config('notifications.sms.enabled', false),
            'push_enabled' => config('notifications.push.enabled', true),
        ];

        return view('settings.notifications.index', compact('notifications'));
    }

    public function updateNotificationSettings(Request $request)
    {
        $validated = $request->validate([
            'email_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'push_enabled' => 'boolean',
        ]);

        // Mise à jour des paramètres de notification
        // Cette partie dépend de votre implémentation

        return redirect()->route('settings.notifications.index')
            ->with('success', 'Paramètres de notification mis à jour avec succès.');
    }

    // ==================== Types d'opérations ====================

    public function typesOperationsIndex()
    {
        $types = TypeOperation::orderBy('libelle')->get();
        return view('parametrage.types-operations.index', compact('types'));
    }

    public function createTypeOperation()
    {
        return view('parametrage.types-operations.create');
    }

    public function storeTypeOperation(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:types_operations,code',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'actif' => 'nullable|boolean',
        ]);

        $data['actif'] = $request->boolean('actif', true);
        TypeOperation::create($data);

        return redirect()->route('settings.types-operations.index')
            ->with('success', 'Type d\'opération créé avec succès.');
    }

    public function editTypeOperation(TypeOperation $typeOperation)
    {
        return view('parametrage.types-operations.edit', compact('typeOperation'));
    }

    public function updateTypeOperation(Request $request, TypeOperation $typeOperation)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:types_operations,code,' . $typeOperation->id,
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'actif' => 'nullable|boolean',
        ]);

        $data['actif'] = $request->boolean('actif', true);
        $typeOperation->update($data);

        return redirect()->route('settings.types-operations.index')
            ->with('success', 'Type d\'opération mis à jour avec succès.');
    }

    public function destroyTypeOperation(TypeOperation $typeOperation)
    {
        $typeOperation->delete();

        return redirect()->route('settings.types-operations.index')
            ->with('success', 'Type d\'opération supprimé avec succès.');
    }

    // ==================== Gestionnaire de fichiers ====================

    public function fileManager()
    {
        return view('settings.file-manager');
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            // Créer un enregistrement en base de données pour suivre le fichier
            $fileRecord = new File();
            $fileRecord->id = uniqid('file_');
            $fileRecord->name = $file->getClientOriginalName();
            $fileRecord->path = $filePath;
            $fileRecord->size = $file->getSize();
            $fileRecord->mime_type = $file->getMimeType();
            $fileRecord->user_id = Auth::id();
            $fileRecord->save();

            $fileData = [
                'id' => $fileRecord->id,
                'name' => $file->getClientOriginalName(),
                'path' => $filePath,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'created_at' => $fileRecord->created_at,
            ];

            return response()->json([
                'success' => true,
                'file' => $fileData,
                'message' => 'Fichier uploadé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadFile($fileId)
    {
        try {
            // Chercher le fichier dans la base de données
            $fileRecord = File::findOrFail($fileId);

            $filePath = storage_path('app/public/' . $fileRecord->path);

            if (!file_exists($filePath)) {
                return response()->json(['message' => 'Fichier non trouvé'], 404);
            }

            return response()->download($filePath, $fileRecord->name);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    public function deleteFile($fileId)
    {
        try {
            // Chercher le fichier dans la base de données
            $fileRecord = File::findOrFail($fileId);

            // Supprimer le fichier physique
            $filePath = storage_path('app/public/' . $fileRecord->path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Supprimer l'enregistrement en base de données
            $fileRecord->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fichier supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeService(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'actif' => 'nullable|boolean',
        ]);

        $data['actif'] = $request->boolean('actif', true);
        Service::create($data);

        return redirect()->route('settings.index')
            ->with('success', 'Service créé avec succès.');
    }

    public function updateChain(Request $request)
    {
        $data = $request->validate([
            'chain' => 'nullable|array',
            'chain.*' => 'string|max:255',
        ]);

        $chain = $data['chain'] ?? [];
        Setting::set('operations.default_chain', array_values($chain));

        return redirect()->route('settings.index')
            ->with('success', 'Chaîne de validation mise à jour avec succès.');
    }

    public function editRole($id)
    {
        return redirect()->back()->with('success', 'Cette fonctionnalité est en cours de développement, aucune donnée n\'a été altérée.');
    }


    public function updateRole(\Illuminate\Http\Request $request, $id)
    {
        return redirect()->back()->with('success', 'Cette fonctionnalité est en cours de développement, aucune donnée n\'a été altérée.');
    }


    public function destroyRole($id)
    {
        return redirect()->back()->with('success', 'Cette fonctionnalité est en cours de développement, aucune donnée n\'a été altérée.');
    }

    // ==================== GESTION DES LOGS ====================

    public function logsIndex()
    {
        $logFiles = [];
        $logPath = storage_path('logs');

        if (is_dir($logPath)) {
            $files = glob($logPath . '/laravel*.log');
            foreach ($files as $file) {
                $filename = basename($file);
                $size = filesize($file);
                $modified = filemtime($file);

                $logFiles[] = [
                    'filename' => $filename,
                    'size' => $this->formatBytes($size),
                    'modified' => date('Y-m-d H:i:s', $modified),
                    'path' => $file
                ];
            }
        }

        // Lire les dernières lignes du log principal
        $recentLogs = [];
        $mainLogFile = storage_path('logs/laravel.log');
        if (file_exists($mainLogFile)) {
            $lines = file($mainLogFile);
            $recentLogs = array_slice($lines, -50); // 50 dernières lignes
        }

        return view('settings.logs.index', compact('logFiles', 'recentLogs'));
    }

    public function downloadLogs()
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return back()->with('error', 'Fichier de log non trouvé.');
        }

        return response()->download($logFile, 'laravel-' . date('Y-m-d') . '.log');
    }

    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');

        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            return back()->with('success', 'Fichier de log vidé avec succès.');
        }

        return back()->with('error', 'Fichier de log non trouvé.');
    }

    public function cleanupLogs(Request $request)
    {
        $days = $request->input('days', 7);
        $logPath = storage_path('logs');
        $deletedCount = 0;

        if (is_dir($logPath)) {
            $files = glob($logPath . '/laravel*.log');
            $cutoffTime = time() - ($days * 24 * 60 * 60);

            foreach ($files as $file) {
                if (filemtime($file) < $cutoffTime) {
                    if (unlink($file)) {
                        $deletedCount++;
                    }
                }
            }
        }

        return back()->with('success', "{$deletedCount} fichiers de log supprimés (plus anciens que {$days} jours).");
    }

    // ==================== NETTOYAGE DES DONNÉES ====================

    public function cleanupIndex()
    {
        // Lister les tables principales avec leurs counts
        $tables = [
            'operations' => \DB::table('operations')->count(),
            'clients' => \DB::table('clients')->count(),
            'factures' => \DB::table('factures')->count(),
            'depenses' => \DB::table('depenses')->count(),
            'audit_logs' => \DB::table('audit_logs')->count(),
            'notifications' => \DB::table('notifications')->count(),
            'sessions' => \DB::table('sessions')->count(),
        ];

        return view('settings.cleanup.index', compact('tables'));
    }

    public function previewCleanup(Request $request)
    {
        $request->validate([
            'table' => 'required|string',
            'date_field' => 'required|string',
            'before_date' => 'required|date',
        ]);

        $table = $request->table;
        $dateField = $request->date_field;
        $beforeDate = $request->before_date;

        try {
            $count = \DB::table($table)
                ->where($dateField, '<', $beforeDate)
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => "{$count} enregistrements seront supprimés de la table {$table}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function executeCleanup(Request $request)
    {
        $request->validate([
            'table' => 'required|string',
            'date_field' => 'required|string',
            'before_date' => 'required|date',
            'confirm' => 'required|accepted',
        ]);

        $table = $request->table;
        $dateField = $request->date_field;
        $beforeDate = $request->before_date;

        try {
            $deleted = \DB::table($table)
                ->where($dateField, '<', $beforeDate)
                ->delete();

            return back()->with('success', "{$deleted} enregistrements supprimés de la table {$table}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du nettoyage: ' . $e->getMessage());
        }
    }

    // ==================== UTILITAIRES ====================

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Règles modules/sous-modules:
     * - superadmin: accès total
     * - admin: modules cochés + tous leurs sous-modules automatiquement
     * - moderator: sous-modules explicitement choisis (au moins un par module)
     * - user: aucun module administrateur
     */
    private function sanitizeModulesAndSubmodules(string $role, array $modulesInput, array $submodulesInput): array
    {
        $config = config('submodules', []);
        $availableModules = array_keys($config);

        $allSubmoduleToParent = [];
        $allSubmodules = [];
        foreach ($config as $moduleKey => $moduleData) {
            foreach (array_keys($moduleData['submodules'] ?? []) as $subKey) {
                $allSubmoduleToParent[$subKey] = $moduleKey;
                $allSubmodules[] = $subKey;
            }
        }

        if ($role === 'superadmin') {
            return [$availableModules, $allSubmodules];
        }

        if ($role === 'user') {
            return [[], []];
        }

        $selectedModules = array_values(array_unique(array_intersect($modulesInput, $availableModules)));
        $selectedSubmodules = [];

        foreach (array_unique($submodulesInput) as $subKey) {
            if (!isset($allSubmoduleToParent[$subKey])) {
                continue;
            }

            $selectedSubmodules[] = $subKey;
            $parent = $allSubmoduleToParent[$subKey];
            if (!in_array($parent, $selectedModules, true)) {
                $selectedModules[] = $parent;
            }
        }

        if ($role === 'admin') {
            $selectedSubmodules = [];
            foreach ($selectedModules as $moduleKey) {
                $selectedSubmodules = array_merge(
                    $selectedSubmodules,
                    array_keys($config[$moduleKey]['submodules'] ?? [])
                );
            }
            return [array_values(array_unique($selectedModules)), array_values(array_unique($selectedSubmodules))];
        }

        if ($role === 'moderator') {
            foreach ($selectedModules as $moduleKey) {
                $hasSubmodule = false;
                foreach ($selectedSubmodules as $subKey) {
                    if (($allSubmoduleToParent[$subKey] ?? null) === $moduleKey) {
                        $hasSubmodule = true;
                        break;
                    }
                }

                if (!$hasSubmodule) {
                    throw ValidationException::withMessages([
                        'submodules' => "Le module '{$moduleKey}' doit contenir au moins un sous-module autorisé.",
                    ]);
                }
            }
        }

        return [array_values(array_unique($selectedModules)), array_values(array_unique($selectedSubmodules))];
    }

}
