<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ModeratorPermissionController extends Controller
{
    /**
     * Afficher la page de gestion des permissions modérateur
     */
    public function index()
    {
        $moderators = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('users', 'model_has_roles.model_id', '=', 'users.id')
            ->where('roles.name', 'moderator')
            ->where('users.is_active', true)
            ->get(['users.id', 'users.name', 'users.email']);

        // Récupérer tous les modules disponibles
        $allModules = [
            'operations' => 'Opérations',
            'validations' => 'Validations',
            'admin' => 'Administration',
            'rh' => 'Ressources Humaines',
            'commercial' => 'Commercial',
            'comptabilite' => 'Comptabilité',
            'tresorerie' => 'Trésorerie',
            'stock' => 'Gestion des Stocks',
            'parc' => 'Parc Automobile',
            'materiel_roulant' => 'Matériel Roulant',
            'checking' => 'Checking',
            'reports' => 'Rapports',
            'settings' => 'Paramètres',
        ];

        return view('admin.permissions.moderator', compact('moderators', 'allModules'));
    }

    /**
     * Récupérer les permissions d'un modérateur spécifique (AJAX)
     */
    public function getModeratorPermissions($moderatorId)
    {
        $moderator = DB::table('users')->where('id', $moderatorId)->first();

        if (!$moderator) {
            return response()->json(['error' => 'Modérateur non trouvé'], 404);
        }

        // Récupérer les permissions actuelles du modérateur
        $permissions = DB::table('model_has_permissions')
            ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->where('model_has_permissions.model_id', $moderatorId)
            ->where('model_has_permissions.model_type', 'App\\Models\\User')
            ->pluck('permissions.name')
            ->toArray();

        return response()->json([
            'moderator' => $moderator,
            'permissions' => $permissions
        ]);
    }

    /**
     * Mettre à jour les permissions d'un modérateur (AJAX)
     */
    public function updateModeratorPermissions(Request $request, $moderatorId)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $moderator = DB::table('users')->where('id', $moderatorId)->first();

        if (!$moderator) {
            return response()->json(['error' => 'Modérateur non trouvé'], 404);
        }

        // Supprimer toutes les permissions actuelles
        DB::table('model_has_permissions')
            ->where('model_id', $moderatorId)
            ->where('model_type', 'App\\Models\\User')
            ->delete();

        // Ajouter les nouvelles permissions
        if ($request->has('permissions')) {
            foreach ($request->permissions as $permissionName) {
                $permission = DB::table('permissions')->where('name', $permissionName)->first();
                if ($permission) {
                    DB::table('model_has_permissions')->insert([
                        'permission_id' => $permission->id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $moderatorId,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Permissions du modérateur mises à jour avec succès'
        ]);
    }

    /**
     * Obtenir les modules disponibles pour un modérateur
     */
    private function getAvailableModules()
    {
        return [
            'operations.access' => 'Accès aux opérations',
            'operations.create' => 'Créer des opérations',
            'operations.edit' => 'Modifier des opérations',
            'validations.view' => 'Voir les validations',
            'validations.approve' => 'Approuver les validations',
            'validations.reject' => 'Rejeter les validations',
            'admin.access' => 'Accès administration',
            'admin.users' => 'Gérer les utilisateurs',
            'reports.view' => 'Voir les rapports',
            'reports.export' => 'Exporter les rapports',
            'settings.access' => 'Accès aux paramètres',
        ];
    }
}
