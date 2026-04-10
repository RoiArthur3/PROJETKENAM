<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RolePermissionController extends Controller
{
    /**
     * Afficher la page de gestion des permissions d'un rôle
     */
    public function edit($roleId)
    {
        $role = DB::table('roles')->where('id', $roleId)->first();
        
        if (!$role) {
            abort(404, 'Rôle non trouvé');
        }

        // Récupérer toutes les permissions groupées par module
        $allPermissions = DB::table('permissions')->get();
        
        $groupedPermissions = [
            'Opérations' => $allPermissions->filter(function($p) {
                return str_starts_with($p->name, 'operations.');
            }),
            'Validations' => $allPermissions->filter(function($p) {
                return str_starts_with($p->name, 'validations.');
            }),
            'Administration' => $allPermissions->filter(function($p) {
                return str_starts_with($p->name, 'admin.');
            }),
            'Rapports' => $allPermissions->filter(function($p) {
                return str_starts_with($p->name, 'reports.');
            }),
            'Paramètres' => $allPermissions->filter(function($p) {
                return str_starts_with($p->name, 'settings.');
            }),
            'Notifications' => $allPermissions->filter(function($p) {
                return str_starts_with($p->name, 'notifications.');
            }),
            'Services' => $allPermissions->filter(function($p) {
                return str_starts_with($p->name, 'services.');
            }),
        ];

        // Récupérer les permissions actuelles du rôle
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->pluck('permission_id')
            ->toArray();

        return view('admin.roles.permissions', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    /**
     * Mettre à jour les permissions d'un rôle
     */
    public function update(Request $request, $roleId)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $role = DB::table('roles')->where('id', $roleId)->first();
        
        if (!$role) {
            abort(404, 'Rôle non trouvé');
        }

        // Supprimer toutes les permissions actuelles
        DB::table('role_has_permissions')->where('role_id', $roleId)->delete();

        // Ajouter les nouvelles permissions
        if ($request->has('permissions')) {
            foreach ($request->permissions as $permissionId) {
                DB::table('role_has_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "Permissions du rôle '{$role->name}' mises à jour avec succès");
    }

    /**
     * Afficher la liste des rôles avec leurs permissions
     */
    public function index()
    {
        $roles = DB::table('roles')->get();
        
        $rolesWithPermissions = [];
        
        foreach ($roles as $role) {
            $permissions = DB::table('role_has_permissions')
                ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                ->where('role_has_permissions.role_id', $role->id)
                ->pluck('permissions.name')
                ->toArray();
            
            $rolesWithPermissions[] = [
                'role' => $role,
                'permissions' => $permissions,
                'permissions_count' => count($permissions),
            ];
        }

        return view('admin.roles.index', compact('rolesWithPermissions'));
    }
}
