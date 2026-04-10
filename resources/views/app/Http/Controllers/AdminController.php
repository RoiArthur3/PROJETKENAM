<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\Service;
use App\Models\User;
use App\Models\Agent;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\VehicleAssignment;
use App\Models\OperationStaff;
use App\Models\StockMovement;
use App\Models\Setting;
use App\Models\SmsSetting;

class AdminController extends Controller
{
    /* ========================= DASHBOARD ========================= */

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
            'total_operations' => class_exists('\App\Models\Operation') ? \App\Models\Operation::count() : 0,
            'system_modules' => 12,
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function utilisateurs()
    {
        return app(UserController::class)->index();
    }

    public function system()
    {
        return redirect()->route('admin.settings');
    }

    /* ========================= SETTINGS ========================= */

    public function settings()
    {
        $settings = [
            'company_name' => 'KENAM Services',
            'company_address' => 'Adresse de l\'entreprise',
            'company_phone' => '+225 XX XX XX XX XX',
            'company_email' => 'contact@kenam.ci',
            'tax_rate' => 18.0,
            'currency' => 'FCFA',
            'working_hours_start' => '08:00',
            'working_hours_end' => '17:00',
        ];

        $services = Service::orderBy('nom')->get();
        $defaultChain = ['Service A', 'Service B', 'Service C', 'Service D', 'Service E'];
        $types = collect([]);

        $notificationTemplates = Setting::get('notifications.email_templates', []);
        $notificationChannels = Setting::get('notifications.channels', []);
        $notificationRolePrefs = Setting::get('notifications.roles_prefs', []);

        $smsSettings = SmsSetting::current();

        return view('admin.settings', compact(
            'settings',
            'services',
            'defaultChain',
            'types',
            'notificationTemplates',
            'notificationChannels',
            'notificationRolePrefs',
            'smsSettings'
        ));
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:services,nom',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['actif'] = $request->has('actif');

        Service::create($validated);

        return redirect()->route('admin.settings')->with('success', 'Service créé avec succès');
    }

    public function updateService(Request $request, Service $service)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:services,nom,' . $service->id,
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['actif'] = $request->has('actif');

        $service->update($validated);

        return redirect()->route('admin.settings')->with('success', 'Service mis à jour avec succès');
    }

    public function deleteService(Service $service)
    {
        try {
            if (Schema::hasTable('controles') && Schema::hasTable('controle_service')) {
                if (method_exists($service, 'controles') && $service->controles()->exists()) {
                    return redirect()->route('admin.settings')->with('error', 'Service utilisé');
                }
            }
        } catch (\Exception $e) {}

        $service->delete();

        return redirect()->route('admin.settings')->with('success', 'Service supprimé avec succès');
    }

    public function updateOperationChain(Request $request)
    {
        $request->validate([
            'chain' => 'array|max:5',
            'chain.*' => 'nullable|string|max:255'
        ]);

        return redirect()->route('admin.settings')->with('success', 'Chaîne mise à jour');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'currency' => 'required|string|max:10',
        ]);

        return redirect()->route('admin.settings')->with('success', 'Paramètres mis à jour');
    }

    /* ========================= USERS & PERMISSIONS ========================= */

    public function permissions(Request $request)
    {
        $users = User::orderBy('name')->get();
        $roles = ['admin','superadmin','moderator','agent','tresorerie','commercial','rh','comptabilite'];
        $modules = \App\Services\UserPermissionService::getAllModules();

        $selectedUser = null;
        $userModules = [];

        if ($request->filled('user_id')) {
            $selectedUser = User::find($request->user_id);

            if ($selectedUser) {
                $permissions = \App\Services\UserPermissionService::getUserPermissions($selectedUser);
                $userModules = array_keys(array_filter($permissions));
            }
        }

        return view('admin.comptes.permissions', compact(
            'users', 'roles', 'modules', 'selectedUser', 'userModules'
        ));
    }

    public function comptes(Request $request)
    {
        $query = User::query()->with('service');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        if ($request->filled('service')) {
            $query->where('service_id', $request->get('service'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', (bool) $request->get('status'));
        }

        $users = $query->orderBy('name')->get();

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'admins' => User::where('role', 'admin')->count(),
            'superadmins' => User::where('role', 'superadmin')->count(),
            'moderators' => User::where('role', 'moderator')->count(),
        ];

        return view('admin.comptes.index', compact('users', 'stats'));
    }

    public function createAgent()
    {
        $services = Service::orderBy('nom')->get();
        return view('admin.comptes.create-agent', compact('services'));
    }

    public function createModerateur()
    {
        $modules = \App\Services\UserPermissionService::getModulesForRole('moderator');
        return view('admin.comptes.create-moderateur', compact('modules'));
    }

    public function createAdmin()
    {
        $services = Service::orderBy('nom')->get();
        $modules = \App\Services\UserPermissionService::getAllModules();
        return view('admin.comptes.create-admin', compact('services', 'modules'));
    }

    public function usersIndex()
    {
        $users = User::orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $services = Service::orderBy('nom')->get();
        $modules = array_keys(\App\Services\UserPermissionService::getAllModules());
        return view('admin.comptes.create-user', compact('services', 'modules'));
    }

    public function storeAgent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:30',
            'password' => 'required|string|min:8|confirmed',
            'service_id' => 'required|exists:services,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'agent',
            'service_id' => $validated['service_id'],
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        \App\Services\UserPermissionService::updateUserPermissions($user, ['dashboard', 'requetes']);

        return redirect()->route('admin.comptes.index')->with('success', 'Agent créé avec succès.');
    }

    public function storeModerateur(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:30',
            'password' => 'required|string|min:8|confirmed',
            'modules' => 'required|array|min:1',
            'modules.*' => 'string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'moderator',
            'service_id' => null, // Les modérateurs ne sont pas liés à un service spécifique
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Ajouter les modules par défaut + ceux sélectionnés
        $selectedModules = array_unique(array_merge(['dashboard', 'operations'], $request->input('modules', [])));
        \App\Services\UserPermissionService::updateUserPermissions($user, $selectedModules);

        return redirect()->route('admin.comptes.index')->with('success', 'Modérateur créé avec succès.');
    }

    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:30',
            'password' => 'required|string|min:8|confirmed',
            'service_id' => 'nullable|exists:services,id',
            'modules' => 'sometimes|array',
            'modules.*' => 'string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'service_id' => $validated['service_id'] ?? null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $modules = (array) $request->input('modules', []);
        if (empty($modules)) {
            $modules = ['dashboard'];
        } else {
            $modules = array_values(array_unique(array_merge(['dashboard'], $modules)));
        }
        \App\Services\UserPermissionService::updateUserPermissions($user, $modules);

        return redirect()->route('admin.comptes.index')->with('success', 'Administrateur créé avec succès.');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:30',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:agent,moderator,admin,superadmin',
            'service_id' => 'required_if:role,agent|nullable|exists:services,id',
            'modules' => 'sometimes|array',
            'modules.*' => 'string',
        ]);

        // Séparer le nom et prénom pour la table agents
        $nameParts = explode(' ', $validated['name'], 2);
        $prenom = $nameParts[0] ?? '';
        $nom = $nameParts[1] ?? '';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'service_id' => $validated['service_id'] ?? null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Si c'est un agent, créer aussi l'enregistrement dans la table RH
        if ($validated['role'] === 'agent') {
            // Générer un matricule automatiquement
            $matricule = 'AG' . date('Y') . str_pad(Agent::count() + 1, 4, '0', STR_PAD_LEFT);

            Agent::create([
                'matricule' => $matricule,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'telephone' => $validated['phone'],
                'service_id' => $validated['service_id'] ?? null,
                'is_active' => true,
            ]);
        }

        // Attribution des modules selon le rôle
        $modules = [];
        switch ($validated['role']) {
            case 'agent':
                $modules = ['operations', 'validations'];
                break;
            case 'moderator':
                $modules = (array) $request->input('modules', []);
                if (empty($modules)) {
                    $modules = ['dashboard', 'operations', 'validations'];
                }
                break;
            case 'admin':
                $modules = (array) $request->input('modules', []);
                if (empty($modules)) {
                    $modules = ['operations', 'validations'];
                } else {
                    $modules = array_values(array_unique(array_merge(['operations', 'validations'], $modules)));
                    $modules = array_diff($modules, ['dashboard']); // Admin n'a pas le dashboard général
                }
                break;
            case 'superadmin':
                $modules = ['dashboard', 'operations', 'validations', 'admin', 'comptabilite', 'stock', 'commercial', 'rh', 'parc', 'tresorerie'];
                break;
        }

        \App\Services\UserPermissionService::updateUserPermissions($user, $modules);

        $roleNames = [
            'agent' => 'Agent',
            'moderator' => 'Modérateur',
            'admin' => 'Admin',
            'superadmin' => 'Super Admin'
        ];

        $message = $roleNames[$validated['role']] . ' créé avec succès';

        // Ajouter une info spécifique pour les agents
        if ($validated['role'] === 'agent') {
            $message .= '. Les informations RH ont été enregistrées pour la gestion de la paie.';
        }

        return redirect()->route('admin.comptes.index')->with('success', $message);
    }

    public function resetAgentPassword($id)
    {
        $agent = \App\Models\Agent::findOrFail($id);
        $newPassword = 'Agent' . rand(1000, 9999);

        $agent->update(['password' => Hash::make($newPassword)]);

        return back()->with('success', "Mot de passe réinitialisé : {$newPassword}");
    }

    /* ========================= SYSTEM ========================= */

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return response()->json(['success' => true]);
    }

    public function services()
    {
        $services = Service::with('responsable')->orderBy('nom')->get();
        return view('admin.settings.services', compact('services'));
    }

    public static function checkSystemLock(?string $module = null, ?int $userId = null): bool
    {
        if (\App\Models\SystemLock::isLocked(\App\Models\SystemLock::TYPE_FULL_ACCESS)) {
            return true;
        }

        return false;
    }

    /**
     * Mettre à jour les permissions d'un utilisateur
     */
    public function updateUserPermissions(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $request->validate([
            'modules' => 'required|array',
            'modules.*' => 'string|exists:modules,name'
        ]);

        \App\Services\UserPermissionService::updateUserPermissions($user, $request->input('modules'));

        return redirect()->route('admin.comptes.permissions', ['user_id' => $userId])->with('success', 'Permissions mises à jour avec succès.');
    }

    /**
     * Afficher le formulaire d'édition d'un utilisateur
     */
    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        $services = Service::orderBy('nom')->get();
        $modules = array_keys(\App\Services\UserPermissionService::getAllModules());
        $userPermissions = \App\Services\UserPermissionService::getUserPermissions($user);
        $userModules = is_array($userPermissions) ? array_keys(array_filter($userPermissions)) : [];

        return view('admin.comptes.edit-user', compact('user', 'services', 'modules', 'userModules'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'phone' => 'required|string|max:30',
            'role' => 'required|in:agent,moderator,admin,superadmin',
            'service_id' => 'nullable|exists:services,id',
            'is_active' => 'required|boolean',
            'modules' => 'sometimes|array',
            'modules.*' => 'string',
        ]);

        $user->update($validated);

        if (!empty($validated['modules'])) {
            \App\Services\UserPermissionService::updateUserPermissions($user, $validated['modules']);
        }

        return redirect()->route('admin.comptes.permissions', ['user_id' => $userId])->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);

        // Empêcher la suppression du superadmin connecté
        if (auth()->user()->id == $userId) {
            return redirect()->route('admin.comptes.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.comptes.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur
     */
    public function resetUserPassword(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // Générer un nouveau mot de passe temporaire
        $newPassword = 'Kenam@' . date('Y') . rand(100, 999);

        $user->update([
            'password' => Hash::make($newPassword),
            'email_verified_at' => null, // Forcer la vérification
        ]);

        // Envoyer l'email (à implémenter)
        // Mail::to($user->email)->send(new PasswordResetMail($newPassword));

        return redirect()->route('admin.comptes.permissions', ['user_id' => $userId])
            ->with('success', "Mot de passe réinitialisé. Nouveau mot de passe: {$newPassword}");
    }

    /**
     * Afficher le formulaire de modification des informations de connexion
     */
    public function editCredentials($userId)
    {
        $user = User::findOrFail($userId);
        return view('admin.comptes.edit-credentials', compact('user'));
    }

    /**
     * Mettre à jour les informations de connexion d'un utilisateur (email, téléphone, mot de passe)
     */
    public function updateUserCredentials(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // Validation des données
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,' . $userId,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Mise à jour de l'email et du téléphone
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];

        // Mise à jour du mot de passe si fourni
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);

            // Si l'utilisateur modifie son propre mot de passe, on le déconnecte
            if (auth()->id() == $userId) {
                $user->setRememberToken(null);

                // On déconnecte l'utilisateur après la mise à jour
                $this->guard()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('status', 'Vos informations de connexion ont été mises à jour. Veuillez vous reconnecter avec votre nouveau mot de passe.');
            }
        }

        $user->save();

        // Redirection avec message de succès
        return redirect()->route('admin.comptes.permissions', ['user_id' => $userId])
            ->with('success', 'Les informations de connexion ont été mises à jour avec succès.');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
