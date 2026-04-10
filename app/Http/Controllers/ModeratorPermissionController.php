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
        $permissionNames = DB::table('model_has_permissions')
            ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->where('model_has_permissions.model_id', $moderatorId)
            ->where('model_has_permissions.model_type', 'App\\Models\\User')
            ->pluck('permissions.name')
            ->toArray();

        // Convertir les noms de permissions en modules (enlever .access)
        $modules = array_map(function($permissionName) {
            return str_replace('.access', '', $permissionName);
        }, $permissionNames);

        return response()->json([
            'moderator' => $moderator,
            'permissions' => $modules
        ]);
    }

    /**
     * Mettre à jour les permissions d'un modérateur (AJAX)
     * 
     * Cette méthode utilise la SYNCHRONISATION INTELLIGENTE:
     * - Les modules che cochés et n'existaient pas : ajoutés
     * - Les modules décochés qui existaient : supprimés
     * - Les modules cochés qui existaient : conservés (même si non renvoyés par le formulaire)
     * 
     * Cela garantit que les modules cochés ne sont JAMAIS décochés accidentellement.
     */
    public function updateModeratorPermissions(Request $request, $moderatorId)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'array',
            'permissions.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $moderator = DB::table('users')->where('id', $moderatorId)->first();

        if (!$moderator) {
            return response()->json(['error' => 'Modérateur non trouvé'], 404);
        }

        // Récupérer les permissions actuelles du modérateur
        $currentPermissions = DB::table('model_has_permissions')
            ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->where('model_has_permissions.model_id', $moderatorId)
            ->where('model_has_permissions.model_type', 'App\\Models\\User')
            ->pluck('permissions.name')
            ->toArray();

        // Convertir les modules reçus en noms de permissions (ajouter .access)
        $requestedPermissions = [];
        if ($request->has('permissions')) {
            foreach ($request->permissions as $module) {
                $requestedPermissions[] = $module . '.access';
            }
        }

        // SYNCHRONISATION INTELLIGENTE
        // 1. Identifier les permissions à AJOUTER (cochées maintenant, n'existaient pas avant)
        $permissionsToAdd = array_diff($requestedPermissions, $currentPermissions);

        // 2. Identifier les permissions à SUPPRIMER (décochées maintenant, existaient avant)
        $permissionsToRemove = array_diff($currentPermissions, $requestedPermissions);

        // 3. Supprimer SEULEMENT les permissions décochées
        if (!empty($permissionsToRemove)) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', $permissionsToRemove)
                ->pluck('id')
                ->toArray();

            if (!empty($permissionIds)) {
                DB::table('model_has_permissions')
                    ->where('model_id', $moderatorId)
                    ->where('model_type', 'App\\Models\\User')
                    ->whereIn('permission_id', $permissionIds)
                    ->delete();
            }
        }

        // 4. Ajouter SEULEMENT les permissions cochées qui n'existaient pas
        if (!empty($permissionsToAdd)) {
            $permissionsData = DB::table('permissions')
                ->whereIn('name', $permissionsToAdd)
                ->get(['id', 'name']);

            foreach ($permissionsData as $permission) {
                // Vérifier que cette permission n'existe pas déjà (par sécurité)
                $exists = DB::table('model_has_permissions')
                    ->where('model_id', $moderatorId)
                    ->where('model_type', 'App\\Models\\User')
                    ->where('permission_id', $permission->id)
                    ->exists();

                if (!$exists) {
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
            'message' => 'Permissions du modérateur mises à jour avec succès',
            'added' => count($permissionsToAdd),
            'removed' => count($permissionsToRemove),
            'preserved' => count(array_intersect($requestedPermissions, $currentPermissions))
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
