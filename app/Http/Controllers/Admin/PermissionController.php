<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserPermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    /**
     * Display the permissions overview page.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Appliquer les filtres
        if ($roleFilter = $request->get('role')) {
            $query->whereHas('roles', function($query) use ($roleFilter) {
                $query->where('name', $roleFilter);
            });
        }

        if ($search = $request->get('search')) {
            $query->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->paginate(15);
        $allModules = UserPermissionService::getAllModules();
        $roles = Role::all();

        $roleFilter = $request->get('role');
        $moduleFilter = $request->get('module');

        // Filtrer par module si spécifié
        if ($moduleFilter) {
            $filteredUsers = [];
            foreach ($users as $user) {
                $permissions = UserPermissionService::getUserPermissions($user);
                if ($permissions[$moduleFilter] ?? false) {
                    $filteredUsers[] = $user;
                }
            }
            // Créer une pagination manuelle pour les utilisateurs filtrés
            $collection = collect($filteredUsers);
            $currentPage = request()->get('page', 1);
            $perPage = 15;
            $users = new \Illuminate\Pagination\LengthAwarePaginator(
                $collection->forPage($currentPage, $perPage),
                $collection->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        // Préparer les données pour le tableau
        $usersData = [];
        foreach ($users as $user) {
            $permissions = UserPermissionService::getUserPermissions($user);
            $usersData[] = [
                'user' => $user,
                'role' => $user->getRoleNames()->first(),
                'permissions' => $permissions,
                'active_modules' => array_keys(array_filter($permissions))
            ];
        }

        return view('admin.permissions.index', compact('usersData', 'allModules', 'roles', 'roleFilter', 'moduleFilter'));
    }

    /**
     * Show permissions management by role.
     */
    public function byRole($role)
    {
        $role = Role::where('name', $role)->firstOrFail();
        $users = User::whereHas('roles', function($query) use ($role) {
            $query->where('name', $role->name);
        })->get();

        $allModules = UserPermissionService::getAllModules();
        $availableModules = UserPermissionService::getModulesForRole($role->name);

        // Préparer les données
        $usersData = [];
        foreach ($users as $user) {
            $permissions = UserPermissionService::getUserPermissions($user);
            $usersData[] = [
                'user' => $user,
                'permissions' => $permissions,
                'active_modules' => array_keys(array_filter($permissions))
            ];
        }

        return view('admin.permissions.by-role', compact('role', 'usersData', 'allModules', 'availableModules'));
    }

    /**
     * Show permissions for a specific user.
     */
    public function user(Request $request, User $user)
    {
        return $this->userPermissions($user);
    }

    /**
     * Show permissions for a specific user.
     */
    public function userPermissions(User $user)
    {
        $user->load('roles');
        $role = $user->getRoleNames()->first();

        $allModules = UserPermissionService::getAllModules();
        $availableModules = UserPermissionService::getModulesForRole($role);
        $currentPermissions = UserPermissionService::getUserPermissions($user);

        return view('admin.permissions.user', compact('user', 'role', 'allModules', 'availableModules', 'currentPermissions'));
    }

    /**
     * Update user permissions.
     */
    public function updateUserPermissions(Request $request, User $user)
    {
        $request->validate([
            'modules' => 'nullable|array',
            'modules.*' => 'string',
        ]);

        $selectedModules = $request->modules ?? [];

        // Appliquer les permissions
        $result = UserPermissionService::updateUserPermissions($user, $selectedModules);

        if ($result) {
            Log::info("Permissions mises à jour pour user {$user->id} via interface admin");
            return redirect()->route('admin.permissions.user', $user)
                ->with('success', 'Permissions mises à jour avec succès.');
        } else {
            return redirect()->route('admin.permissions.user', $user)
                ->with('error', 'Erreur lors de la mise à jour des permissions.');
        }
    }

    /**
     * Update permissions for multiple users (bulk update).
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
            'action' => 'required|in:add,remove,replace'
        ]);

        $userIds = $request->users;
        $modules = $request->modules ?? [];
        $action = $request->action;

        $updatedCount = 0;

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                if ($action === 'replace') {
                    UserPermissionService::updateUserPermissions($user, $modules);
                } else {
                    $currentPermissions = UserPermissionService::getUserPermissions($user);
                    $newPermissions = $currentPermissions;

                    if ($action === 'add') {
                        foreach ($modules as $module) {
                            $newPermissions[$module] = true;
                        }
                    } elseif ($action === 'remove') {
                        foreach ($modules as $module) {
                            $newPermissions[$module] = false;
                        }
                    }

                    $activeModules = array_keys(array_filter($newPermissions));
                    UserPermissionService::updateUserPermissions($user, $activeModules);
                }
                $updatedCount++;
            }
        }

        Log::info("Mise à jour en masse de permissions: {$updatedCount} utilisateurs, action: {$action}");

        return redirect()->route('admin.permissions.index')
            ->with('success', "{$updatedCount} utilisateur(s) mis à jour(s) avec succès.");
    }

    /**
     * Display profiles with their permissions.
     */
    public function profiles()
    {
        $roles = Role::with(['users', 'permissions'])->get();
        $allModules = UserPermissionService::getAllModules();
        
        $profilesData = [];
        
        foreach ($roles as $role) {
            $rolePermissions = config('permissions.roles.' . $role->name, []);
            $roleModules = config('permissions.modules.' . $role->name, []);
            
            $profilesData[] = [
                'role' => $role,
                'users_count' => $role->users->count(),
                'permissions' => $rolePermissions,
                'modules' => $roleModules,
                'users' => $role->users->map(function($user) {
                    $permissions = UserPermissionService::getUserPermissions($user);
                    return [
                        'user' => $user,
                        'permissions' => $permissions,
                        'active_modules' => array_keys(array_filter($permissions))
                    ];
                })
            ];
        }

        return view('admin.permissions.profiles', compact('profilesData', 'allModules'));
    }

    /**
     * Get permissions statistics.
     */
    public function statistics()
    {
        $allModules = UserPermissionService::getAllModules();
        $roles = Role::all();

        $stats = [];

        foreach ($roles as $role) {
            $users = User::whereHas('roles', function($query) use ($role) {
                $query->where('name', $role->name);
            })->get();

            $roleStats = [
                'role' => $role->name,
                'user_count' => $users->count(),
                'modules' => []
            ];

            foreach (array_keys($allModules) as $module) {
                $count = $users->filter(function($user) use ($module) {
                    return $user->canAccessModule($module);
                })->count();

                $roleStats['modules'][$module] = [
                    'count' => $count,
                    'percentage' => $users->count() > 0 ? round(($count / $users->count()) * 100, 1) : 0
                ];
            }

            $stats[] = $roleStats;
        }

        return view('admin.permissions.statistics', compact('stats', 'allModules', 'roles'));
    }
}
