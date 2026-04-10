<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Service;
use App\Models\Role;
use App\Services\UserPermissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Obtenir tous les modules disponibles pour le formulaire
        $allModules = UserPermissionService::getAllModules();

        return view('admin.users.create', compact('allModules'));
    }

    /**
     * Store a newly created user in storage.
     */
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'required|string|max:30|regex:/^[0-9]{10}$/',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:agent,moderator,moderateur,admin,superadmin',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
            'submodules' => 'nullable|array',
            'submodules.*' => 'string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'role' => $request->role,
            'is_active' => true,
            'password' => Hash::make($request->password),
            'modules' => $request->modules ?? [],
            'submodules' => $request->submodules ?? [],
        ]);

        // Récupérer les colonnes d'accès direct
        $extraColumns = $request->only([
            'can_access_dashboard', 'can_access_operations', 'can_access_hr', 
            'can_access_fleet', 'can_access_suppliers', 'can_access_warehouse', 
            'can_access_accounting', 'can_access_invoicing', 'can_access_reporting', 
            'can_access_commercial', 'can_access_prospection', 'can_access_ateliers'
        ]);

        // Déterminer les modules selon le rôle
        $selectedModules = $request->modules ?? [];
        if ($request->role === 'agent') {
            $selectedModules = ['operations', 'requetes'];
        } elseif ($request->role === 'superadmin') {
            $selectedModules = array_keys(UserPermissionService::getAllModules());
        }

        UserPermissionService::updateUserPermissions(
            $user, 
            $selectedModules, 
            $request->submodules ?? [],
            $extraColumns
        );

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $user->load('roles');
        // fallback au cas où Role n'est pas utilisé avec Spatie
        $roles = class_exists('App\Models\Role') ? \App\Models\Role::all() : collect([]);

        // Obtenir les modules disponibles pour le rôle actuel
        $availableModules = UserPermissionService::getModulesForRole($user->role);
        $allModules = UserPermissionService::getAllModules();
        $currentPermissions = UserPermissionService::getUserPermissions($user);

        return view('admin.users.edit', compact('user', 'roles', 'availableModules', 'currentPermissions', 'allModules'));
    }

    /**
     * Show form to edit user submodules permissions
     */
    public function editSubmodules(User $user)
    {
        // Uniformisation: utiliser la page standard d'édition utilisateur.
        return redirect()
            ->route('admin.users.edit', $user)
            ->with('info', 'La gestion des sous-modules est désormais centralisée dans la fiche utilisateur.');
    }

    /**
     * Update user submodules permissions
     */
    public function updateSubmodules(Request $request, User $user)
    {
        $validated = $request->validate([
            'modules' => 'nullable|array',
            'modules.*' => 'string',
            'submodules' => 'nullable|array',
            'submodules.*' => 'string',
        ]);

        UserPermissionService::updateUserPermissions(
            $user,
            $validated['modules'] ?? [],
            $validated['submodules'] ?? []
        );

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'Services, métiers, modules et sous-modules mis à jour avec succès.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telephone' => 'required|string|max:30|regex:/^[0-9]{10}$/',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:agent,moderator,moderateur,admin,superadmin',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
            'submodules' => 'nullable|array',
            'submodules.*' => 'string',
        ]);

        // Mettre à jour les informations de base
        $user->name = $request->name;
        $user->email = $request->email;
        $user->telephone = $request->telephone;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Récupérer les colonnes d'accès direct
        $extraColumns = $request->only([
            'can_access_dashboard', 'can_access_operations', 'can_access_hr', 
            'can_access_fleet', 'can_access_suppliers', 'can_access_warehouse', 
            'can_access_accounting', 'can_access_invoicing', 'can_access_reporting', 
            'can_access_commercial', 'can_access_prospection', 'can_access_ateliers'
        ]);

        // Déterminer les modules selon le rôle
        $selectedModules = $request->modules ?? [];
        if ($request->role === 'agent') {
            $selectedModules = ['operations', 'requetes'];
        } elseif ($request->role === 'superadmin') {
            $selectedModules = array_keys(UserPermissionService::getAllModules());
        }

        // Triple Sync s'occupe de sauvegarder les modules/submodules, les colonnes et Spatie
        UserPermissionService::updateUserPermissions(
            $user,
            $selectedModules,
            $request->submodules ?? [],
            $extraColumns
        );

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }



    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Ne pas supprimer l'utilisateur actuel
        if ($user->getKey() === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}
