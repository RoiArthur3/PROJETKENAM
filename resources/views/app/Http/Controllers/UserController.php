<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Role;
use App\Services\UserPermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::with('roles')->paginate(15);
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'required|string|max:30|regex:/^[0-9]{10}$/',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:agent,moderator,admin,superadmin',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone, // Utiliser le champ telephone
            'role' => $request->role,
            'is_active' => true,
            'password' => Hash::make($request->password),
        ]);

        // Assigner le rôle
        $user->syncRoles([$request->role]);

        // Définir les permissions selon le rôle
        if ($request->role === 'agent') {
            // Pour les agents : opérations et requêtes auto-sélectionnés
            $selectedModules = ['operations', 'requetes'];
        } elseif ($request->role === 'admin' || $request->role === 'superadmin') {
            // Pour les admins et superadmins : tous les modules disponibles
            $allModules = UserPermissionService::getAllModules();
            $selectedModules = array_keys($allModules);
        } else {
            // Pour les modérateurs : modules sélectionnés par l'utilisateur
            $selectedModules = $request->modules ?? [];
        }

        // Mettre à jour les permissions
        UserPermissionService::updateUserPermissions($user, $selectedModules);

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
        $roles = Role::all();

        // Obtenir les modules disponibles pour le rôle actuel
        $currentRole = $user->getRoleNames()->first();
        $availableModules = UserPermissionService::getModulesForRole($currentRole);
        $allModules = UserPermissionService::getAllModules();
        $currentPermissions = UserPermissionService::getUserPermissions($user);

        return view('admin.users.edit', compact('user', 'roles', 'availableModules', 'currentPermissions', 'allModules'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|string|max:30|regex:/^[0-9]{10}$/',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:agent,moderator,admin,superadmin',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
        ]);

        // Mettre à jour les informations de base
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone, // Utiliser le champ phone existant
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Mettre à jour le rôle
        $user->syncRoles([$request->role]);

        // Mettre à jour les permissions avec le service robuste
        if ($request->role === 'agent') {
            // Pour les agents : opérations et requêtes auto-sélectionnés
            $selectedModules = ['operations', 'requetes'];
        } elseif ($request->role === 'admin' || $request->role === 'superadmin') {
            // Pour les admins et superadmins : tous les modules disponibles
            $allModules = UserPermissionService::getAllModules();
            $selectedModules = array_keys($allModules);
        } else {
            // Pour les modérateurs : modules sélectionnés par l'utilisateur
            $selectedModules = $request->modules ?? [];
        }

        UserPermissionService::updateUserPermissions($user, $selectedModules);

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
