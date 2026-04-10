<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CompteController extends Controller
{
    public function agentsIndex()
    {
        $agents = User::where('role', 'agent')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.comptes.agents.index', compact('agents'));
    }

    public function agentsCreate()
    {
        return view('admin.comptes.agents.create');
    }

    public function agentsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,manager,agent,user',
            'service_id' => 'nullable|exists:services,id',
            'is_active' => 'required|boolean'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['email_verified_at'] = now();

        User::create($validated);

        return redirect()->route('admin.comptes.agents.index')
            ->with('success', 'Agent créé avec succès');
    }

    public function agentsShow(User $agent)
    {
        return view('admin.comptes.agents.show', compact('agent'));
    }

    public function agentsEdit(User $agent)
    {
        return view('admin.comptes.agents.edit', compact('agent'));
    }

    public function agentsUpdate(Request $request, User $agent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$agent->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|in:admin,manager,agent,user',
            'service_id' => 'nullable|exists:services,id',
            'is_active' => 'required|boolean'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $agent->update($validated);

        return redirect()->route('admin.comptes.agents.index')
            ->with('success', 'Agent mis à jour avec succès');
    }

    public function agentsDestroy(User $agent)
    {
        $agent->delete();

        return redirect()->route('admin.comptes.agents.index')
            ->with('success', 'Agent supprimé avec succès');
    }

    // Méthodes pour la gestion des utilisateurs
    // Méthodes pour la gestion des utilisateurs
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $services = \App\Models\Service::all();

        $allModules = [
            'rh' => 'Ressources Humaines',
            'projects' => 'Projets',
            'commercial' => 'Commercial',
            'comptabilite' => 'Comptabilité',
            'tresorerie' => 'Trésorerie',
            'fournisseurs' => 'Fournisseurs',
            'magasin' => 'Magasin',
            'entrepots' => 'Entrepôts',
            'materiel' => 'Matériel',
            'materiel_roulant' => 'Matériel Roulant',
            'checking' => 'Checking',
            'audit' => 'Audit',
            'reporting' => 'Reporting',
            'settings' => 'Paramètres'
        ];

        return view('admin.users.create', compact('services', 'allModules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:superadmin,admin,moderator,moderateur,agent,user',
            'telephone' => 'nullable|string|min:8|max:20|unique:users,telephone',
            'phone' => 'nullable|string|min:8|max:20|unique:users,telephone',
            'is_active' => 'nullable|boolean',
            'modules' => 'nullable|array',
            'modules.*' => 'string'
        ]);

        // Mapping phone -> telephone
        $phoneValue = $validated['phone'] ?? $validated['telephone'] ?? null;
        if (!$phoneValue) {
            return back()->withErrors(['phone' => 'Le numéro de téléphone est obligatoire.'])->withInput();
        }

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'telephone' => $phoneValue,
            'phone' => $phoneValue,
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ];

        /* Modules logic ... */
        $modules = $request->input('modules', []);
        $user = User::create($userData);

        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles($user->role);
        }

        // Appliquer les permissions de base via le service
        if (class_exists('App\Services\UserRolePermissionService')) {
            \App\Services\UserRolePermissionService::applyRolePermissions($user);
        }

        // Gestion spécifique pour les modérateurs (permissions Spatie)
        if ($user->role === 'moderator' || $user->role === 'moderateur') {
            // Mapping noms français (formulaire) → noms anglais (permissions en BDD)
            $moduleMap = [
                'tresorerie' => 'treasury',
                'comptabilite' => 'accounting',
                'rh' => 'hr',
                'fournisseurs' => 'suppliers',
                'materiel' => 'fleet',
                'magasin' => 'warehouse',
                'entrepots' => 'warehouse',
            ];

            foreach ($modules as $module) {
                $mappedModule = $moduleMap[$module] ?? $module;
                $permissionName = $mappedModule . '.access';
                $permission = DB::table('permissions')->where('name', $permissionName)->first();
                if ($permission) {
                    DB::table('model_has_permissions')->insertOrIgnore([
                        'permission_id' => $permission->id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $user->id,
                    ]);
                }
            }
        }

        return redirect()->route('admin.comptes.users.index')
            ->with('success', 'Utilisateur créé avec succès');
    }

    public function show($user)
    {
        $user = User::findOrFail($user);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Charger les rôles pour la sélection
        $roles = DB::table('roles')->get();

        // Récupérer tous les modules définis dans le système
        // (On peut les définir ici ou utiliser une table si elle existe)
        $allModules = [
            'rh' => 'Ressources Humaines',
            'projects' => 'Projets',
            'commercial' => 'Commercial',
            'comptabilite' => 'Comptabilité',
            'tresorerie' => 'Trésorerie',
            'fournisseurs' => 'Fournisseurs',
            'magasin' => 'Magasin',
            'entrepots' => 'Entrepôts',
            'materiel' => 'Matériel',
            'materiel_roulant' => 'Matériel Roulant',
            'checking' => 'Checking',
            'audit' => 'Audit',
            'reporting' => 'Reporting',
            'settings' => 'Paramètres'
        ];

        // Récupérer les modules disponibles (pour compatibilité avec la vue)
        $availableModules = [];
        foreach($allModules as $key => $label) {
             $availableModules[$key] = [
                'label' => $label,
                'icon' => 'fas fa-cube',
                'route' => '#',
                'mandatory' => false,
                'default_checked' => false
            ];
        }

        // Récupérer les permissions actuelles de l'utilisateur
        $currentPermissions = [];
        $permissionNames = DB::table('model_has_permissions')
            ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->where('model_has_permissions.model_id', $user->id)
            ->where('model_has_permissions.model_type', 'App\Models\User')
            ->pluck('permissions.name')
            ->toArray();

        foreach ($allModules as $key => $label) {
            $currentPermissions[$key] = in_array($key . '.access', $permissionNames);
        }

        return view('admin.users.edit', compact('user', 'roles', 'allModules', 'currentPermissions', 'availableModules'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|string|in:superadmin,admin,moderator,moderateur,agent,user',
            'telephone' => 'nullable|string|min:8|max:20|unique:users,telephone,'.$user->id,
            'phone' => 'nullable|string|min:8|max:20|unique:users,telephone,'.$user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'is_active' => 'nullable|boolean',
            'modules' => 'nullable|array',
            'modules.*' => 'string'
        ]);

        $phoneValue = $validated['phone'] ?? $validated['telephone'] ?? $user->telephone;

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'telephone' => $phoneValue,
            'phone' => $phoneValue,
            'is_active' => $request->boolean('is_active', true),
        ];

        // Hasher le mot de passe si fourni
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Mettre à jour l'utilisateur
        $user->update($userData);

        // Synchroniser les rôles
        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles($user->role);
        }

        // Appliquer les permissions de base du rôle
        if (class_exists('App\Services\UserRolePermissionService')) {
            \App\Services\UserRolePermissionService::applyRolePermissions($user);
        }

        // Gestion spécifique pour les modérateurs (permissions via model_has_permissions)
        if ($user->role === 'moderator' || $user->role === 'moderateur') {
            $modules = $request->input('modules', []);

            // Mapping noms français (formulaire) → noms anglais (permissions en BDD)
            $moduleMap = [
                'tresorerie' => 'treasury',
                'comptabilite' => 'accounting',
                'rh' => 'hr',
                'fournisseurs' => 'suppliers',
                'materiel' => 'fleet',
                'magasin' => 'warehouse',
                'entrepots' => 'warehouse',
            ];

            // Nettoyer les permissions de modules existantes
            DB::table('model_has_permissions')
                ->where('model_id', $user->id)
                ->where('model_type', 'App\\Models\\User')
                ->whereIn('permission_id', function ($query) {
                    $query->select('id')->from('permissions')->where('name', 'like', '%.access');
                })
                ->delete();

            // Ajouter les nouvelles permissions de modules cochés
            foreach ($modules as $module) {
                $mappedModule = $moduleMap[$module] ?? $module;
                $permissionName = $mappedModule . '.access';
                $permission = DB::table('permissions')->where('name', $permissionName)->first();
                if ($permission) {
                    DB::table('model_has_permissions')->insertOrIgnore([
                        'permission_id' => $permission->id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $user->id,
                    ]);
                }
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function destroy($compte)
    {
        $user = User::findOrFail($compte);
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès');
    }

    // Méthodes spécifiques pour les utilisateurs dans le sous-groupe users
    public function storeUser(Request $request)
    {
        return $this->store($request);
    }

    public function updateUser(Request $request, User $user)
    {
        return $this->update($request, $user);
    }

    public function deleteUser($user)
    {
        return $this->destroy($user);
    }
}
