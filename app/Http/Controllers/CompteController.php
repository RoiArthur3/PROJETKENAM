<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
    public function index()
    {
        // Afficher tous les comptes (actifs et inactifs) triés par statut actif en premier, puis par date de création
        $users = User::orderBy('is_active', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate(35);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $allModules = config('submodules', []);

        return view('admin.users.create', compact('allModules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:superadmin,admin,moderator,moderateur',
            'telephone' => 'required|string|min:8|max:20|unique:users,telephone',
            'is_active' => 'nullable|boolean',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
            'submodules' => 'nullable|array',
            'submodules.*' => 'string',
        ]);

        $role = $this->normalizeRole($validated['role']);
        [$selectedModules, $selectedSubmodules] = $this->sanitizeModulesAndSubmodules(
            $role,
            $request->input('modules', []),
            $request->input('submodules', [])
        );

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'telephone' => $validated['telephone'],
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
            'modules' => $selectedModules,
            'submodules' => $selectedSubmodules,
        ];

        if (Schema::hasColumn('users', 'phone')) {
            $userData['phone'] = $validated['telephone'];
        }

        $user = User::create($userData);

        // Triple Synchronisation (JSON + Permissions + Colonnes) - SOURCE DE VÉRITÉ
        \App\Services\UserPermissionService::updateUserPermissions(
            $user,
            $selectedModules,
            $selectedSubmodules
        );

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès');
    }

    public function show($user)
    {
        $user = User::findOrFail($user);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Charger les roles seulement si la table existe (environnement sans module roles)
        $roles = collect();
        if (Schema::hasTable('roles')) {
            $roles = DB::table('roles')->get();
        }

        // Récupérer tous les modules définis dans le système
        $allModules = config('submodules', []);

        // Récupérer les permissions actuelles via le service (gère JSON + Colonnes + Cache)
        $currentPermissions = \App\Services\UserPermissionService::getUserPermissions($user);

        return view('admin.users.edit', compact('user', 'roles', 'allModules', 'currentPermissions'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|string|in:superadmin,admin,moderator,moderateur',
            'telephone' => 'required|string|min:8|max:20|unique:users,telephone,'.$user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'is_active' => 'nullable|boolean',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
            'submodules' => 'nullable|array',
            'submodules.*' => 'string',
        ]);

        $role = $this->normalizeRole($validated['role']);
        [$selectedModules, $selectedSubmodules] = $this->sanitizeModulesAndSubmodules(
            $role,
            $request->input('modules', []),
            $request->input('submodules', [])
        );

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $role,
            'telephone' => $validated['telephone'],
            'is_active' => $request->boolean('is_active', true),
            'modules' => $selectedModules,
            'submodules' => $selectedSubmodules,
        ];

        if (Schema::hasColumn('users', 'phone')) {
            $userData['phone'] = $validated['telephone'];
        }

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Triple Synchronisation (JSON + Permissions + Colonnes)
        \App\Services\UserPermissionService::updateUserPermissions(
            $user,
            $selectedModules,
            $selectedSubmodules
        );

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès');
    }

    /**
     * Helper pour synchroniser les permissions/modules
     */
    private function syncPermissions($user, $modules)
    {
        if ($user->role === 'moderator' || $user->role === 'moderateur') {
            $moduleMap = [
                'tresorerie' => 'treasury',
                'comptabilite' => 'accounting',
                'rh' => 'hr',
                'fournisseurs' => 'suppliers',
                'materiel' => 'fleet',
                'magasin' => 'warehouse',
                'entrepots' => 'warehouse',
            ];

            // Nettoyage
            DB::table('model_has_permissions')
                ->where('model_id', $user->id)
                ->where('model_type', 'App\\Models\\User')
                ->delete();

            // Ajout
            foreach ($modules as $module) {
                $mappedModule = $moduleMap[$module] ?? $module;
                $permission = DB::table('permissions')->where('name', $mappedModule . '.access')->first();
                if ($permission) {
                    DB::table('model_has_permissions')->insertOrIgnore([
                        'permission_id' => $permission->id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $user->id,
                    ]);
                }
            }
        }
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

    /**
     * Formater le numéro de téléphone
     */
    private function formatPhoneNumber($phone)
    {
        // Supprimer tous les caractères non numériques
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Si le numéro commence par 225 (indicatif CI), le remplacer par 0
        if (strlen($phone) === 12 && str_starts_with($phone, '225')) {
            $phone = '0' . substr($phone, 3);
        }

        // Si le numéro commence par +225, le remplacer par 0
        if (str_starts_with($phone, '+225')) {
            $phone = '0' . substr($phone, 4);
        }

        // Si le numéro a 9 chiffres (sans le 0 initial), ajouter le 0
        if (strlen($phone) === 9) {
            $phone = '0' . $phone;
        }

        // S'assurer que le numéro a exactement 10 chiffres et commence par 0
        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            return $phone;
        }

        return $phone; // Retourner tel quel si le format n'est pas reconnu
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return $this->edit($user);
    }


    public function destroyUser($id)
    {
        return $this->destroy($id);
    }

    private function normalizeRole(string $role): string
    {
        return strtolower($role) === 'moderateur' ? 'moderator' : strtolower($role);
    }

    /**
     * Nettoie et valide strictement modules/sous-modules selon le rôle.
     * - superadmin : accès total automatique
     * - admin/moderator : accès uniquement aux modules + sous-modules cochés
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

        $selectedSubmodules = array_values(array_unique($selectedSubmodules));
        $selectedModules = array_values(array_unique($selectedModules));

        // Admin: tous les sous-modules des modules cochés sont accordés automatiquement.
        if ($role === 'admin') {
            $selectedSubmodules = [];
            foreach ($selectedModules as $moduleKey) {
                $selectedSubmodules = array_merge(
                    $selectedSubmodules,
                    array_keys($config[$moduleKey]['submodules'] ?? [])
                );
            }
            $selectedSubmodules = array_values(array_unique($selectedSubmodules));
            return [$selectedModules, $selectedSubmodules];
        }

        // Modérateur: sélection explicite obligatoire des sous-modules pour chaque module coché.
        if (in_array($role, ['moderator', 'moderateur'], true)) {
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

        return [$selectedModules, $selectedSubmodules];
    }

}
