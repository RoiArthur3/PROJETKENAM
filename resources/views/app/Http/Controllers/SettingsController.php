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
        return view('settings.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:10|regex:/^[0-9]+$/|unique:users,telephone',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:superadmin,admin,moderator,user',
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

        // Gérer les permissions selon le rôle
        if ($validated['role'] === 'admin') {
            // Mettre à jour la configuration des modules admin
            $adminConfig = [
                'admin_has_full_access' => $validated['admin_full_access'] ?? false,
                'restricted_modules' => $validated['admin_modules'] ?? [],
            ];

            // Sauvegarder dans la configuration
            $this->updateAdminModulesConfig($user->id, $adminConfig);

        } elseif ($validated['role'] === 'moderator') {
            // Sauvegarder les modules cochés dans model_has_permissions
            $selectedModules = $validated['moderator_modules'] ?? [];
            $this->syncModulePermissions($user, $selectedModules);
        }

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
        return view('settings.users.edit', compact('user', 'roles'));
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

        // Synchroniser les modules du modérateur
        if ($validated['role'] === 'moderator') {
            $selectedModules = $validated['moderator_modules'] ?? [];
            $this->syncModulePermissions($user, $selectedModules);
        }

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
}
