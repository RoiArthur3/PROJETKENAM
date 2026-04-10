<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Exception;

class UserPermissionService
{
    /**
     * Mapping des modules vers les colonnes réelles en base.
     */
    private static array $moduleToColumn = [
        'dashboard'    => 'can_access_dashboard',
        'operations'   => 'can_access_operations',
        'validations'  => 'can_access_operations',
        'requetes'     => 'can_access_operations',
        'tracking'     => 'can_access_operations',
        'suivi'        => 'can_access_operations',
        'warehouse'    => 'can_access_warehouse',
        'magasin'      => 'can_access_warehouse',
        'entrepots'    => 'can_access_warehouse',
        'materiel'     => 'can_access_fleet',
        'fleet'        => 'can_access_fleet',
        'hr'           => 'can_access_hr',
        'rh'           => 'can_access_hr',
        'suppliers'    => 'can_access_suppliers',
        'fournisseurs' => 'can_access_suppliers',
        'achat'        => 'can_access_suppliers',
        'accounting'   => 'can_access_accounting',
        'comptabilite' => 'can_access_accounting',
        'tresorerie'   => 'can_access_accounting',
        'juridique'    => 'can_access_accounting',
        'invoicing'    => 'can_access_invoicing',
        'reporting'    => 'can_access_reporting',
        'commercial'   => 'can_access_commercial',
        'prospection'  => 'can_access_prospection',
        'ateliers'     => 'can_access_ateliers',
        'projects'     => 'can_access_ateliers',
        'audit'        => 'can_access_ateliers',
        'checking'     => 'can_access_fleet',
        'cost_control' => 'can_access_fleet',
    ];

    private static function getPermissionColumnForModule(string $moduleCode): ?string
    {
        return self::$moduleToColumn[$moduleCode] ?? null;
    }

    /**
     * Obtenir tous les modules disponibles
     */
    public static function getAllModules()
    {
        // 1. Source prioritaire : config/submodules.php
        $configModules = config('submodules', []);
        if (!empty($configModules)) {
            $modules = [];
            foreach ($configModules as $key => $data) {
                $modules[$key] = [
                    'label' => $data['name'] ?? ucfirst($key),
                    'icon' => $data['icon'] ?? 'fas fa-cube',
                    'description' => $data['description'] ?? '',
                    'mandatory_for' => $data['mandatory_for'] ?? [],
                    'route' => $data['route'] ?? null
                ];
            }
            return $modules;
        }

        // 2. Fallback : liste des modules en dur si config absente
        return [
            'operations'   => ['label' => 'Opérations', 'icon' => 'fas fa-cogs', 'description' => 'Gestion des opérations'],
            'validations'  => ['label' => 'Validations', 'icon' => 'fas fa-check-circle', 'description' => 'Validation'],
            'rh'           => ['label' => 'Ressources Humaines', 'icon' => 'fas fa-users', 'description' => 'Gestion RH'],
            'projects'     => ['label' => 'Projets', 'icon' => 'fas fa-project-diagram', 'description' => 'Gestion de projets'],
            'commercial'   => ['label' => 'Commercial', 'icon' => 'fas fa-handshake', 'description' => 'Module commercial'],
            'comptabilite' => ['label' => 'Comptabilité', 'icon' => 'fas fa-calculator', 'description' => 'Comptabilité'],
            'tresorerie'   => ['label' => 'Trésorerie', 'icon' => 'fas fa-money-bill-wave', 'description' => 'Trésorerie'],
            'fournisseurs' => ['label' => 'Fournisseurs', 'icon' => 'fas fa-truck', 'description' => 'Gestion fournisseurs'],
            'magasin'      => ['label' => 'Magasin', 'icon' => 'fas fa-store', 'description' => 'Gestion magasin'],
            'materiel'     => ['label' => 'Matériel', 'icon' => 'fas fa-tools', 'description' => 'Gestion matériel'],
            'reporting'    => ['label' => 'Reporting', 'icon' => 'fas fa-chart-bar', 'description' => 'Rapports'],
        ];
    }

    /**
     * Obtenir les modules disponibles pour un rôle spécifique
     */
    public static function getModulesForRole($role)
    {
        $availableModules = [];
        $allModules = self::getAllModules();
        $isModerator = in_array($role, ['moderator', 'moderateur']);

        foreach ($allModules as $key => $module) {
            $isModerator = in_array($role, ['moderator', 'moderateur']);

            // Seul operations est obligatoire pour tout le monde (sauf superadmin et moderateur)
            $isMandatory = ($key === 'operations' && !$isModerator);

            // Pour les modérateurs : exclure les modules réservés aux admins
            if ($isModerator) {
                $adminOnlyModules = ['dashboard', 'agents', 'comptes', 'services', 'system', 'settings'];
                if (in_array($key, $adminOnlyModules)) {
                    continue;
                }
            }

            $availableModules[$key] = [
                'label' => $module['label'],
                'icon' => $module['icon'],
                'mandatory' => $isMandatory,
                'default_checked' => $isMandatory
            ];
        }

        return $availableModules;
    }

    /**
     * Mettre à jour les permissions d'un utilisateur (Triple Synchronisation)
     */
    public static function updateUserPermissions(User $user, array $selectedModules = [], array $selectedSubmodules = [], array $extraColumns = [])
    {
        try {
            DB::beginTransaction();

            $roleLower = strtolower($user->role);
            $allModulesConfig = config('submodules', []);
            $allModuleKeys = array_keys($allModulesConfig);
            $allSubmoduleKeys = [];
            foreach ($allModulesConfig as $moduleData) {
                $allSubmoduleKeys = array_merge($allSubmoduleKeys, array_keys($moduleData['submodules'] ?? []));
            }

            if ($roleLower === 'superadmin') {
                $selectedModules = $allModuleKeys;
                $selectedSubmodules = $allSubmoduleKeys;
            }

            // SYNC 1 : Sauvegarde JSON (Source de vérité)
            $user->modules = array_values(array_unique($selectedModules));
            $user->submodules = array_values(array_unique($selectedSubmodules));

            // SYNC 2 : model_has_permissions (Spatie)
            self::syncModelHasPermissions($user, $user->modules, $user->submodules);

            // SYNC 3 : Colonnes can_access_* (Performances)
            $updateData = [];

            // Reset des colonnes d'accès définies dans le mapping
            foreach (array_unique(self::$moduleToColumn) as $column) {
                $updateData[$column] = false;
            }

            // 1. Activer les modules sélectionnés
            foreach ($user->modules as $moduleCode) {
                $column = self::getPermissionColumnForModule($moduleCode);
                if ($column) {
                    $updateData[$column] = true;
                }
            }

            // 2. Fusionner avec les extraColumns (Switches directs de l'UI)
            foreach ($extraColumns as $col => $val) {
                if (in_array($col, self::$moduleToColumn)) {
                    $updateData[$col] = (bool)$val;
                }
            }

            // 3. Cas forcés par rôle (Précédence maximale)
            if ($roleLower === 'superadmin') {
                foreach (array_unique(self::$moduleToColumn) as $column) {
                    $updateData[$column] = true;
                }
                $updateData['can_access_dashboard'] = true;
            } elseif ($roleLower === 'admin') {
                // Différence métier: l'admin voit le dashboard général.
                $updateData['can_access_dashboard'] = true;
            } elseif (in_array($roleLower, ['moderator', 'moderateur'], true)) {
                $updateData['can_access_dashboard'] = false;
            }

            $user->fill($updateData);
            $user->save();

            self::clearUserCache($user);

            DB::commit();
            Log::info("Permissions synchronisées pour l'utilisateur {$user->id}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur Triple Synchronisation : " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtenir les permissions réelles (lecture JSON prioritaire avec fallback colonnes)
     */
    public static function getUserPermissions(User $user): array
    {
        $permissions = [];
        $jsonModules = is_array($user->modules) ? $user->modules : [];
        $allModules = self::getAllModules();

        // Ajouter les alias de modules (fleet, parc => materiel)
        // Si l'utilisateur a 'fleet' ou 'parc' en JSON, ça compte comme 'materiel'
        if (in_array('fleet', $jsonModules) || in_array('parc', $jsonModules)) {
            $jsonModules[] = 'materiel';
        }

        foreach ($allModules as $moduleCode => $data) {
            $hasAccess = in_array($moduleCode, $jsonModules);

            // Fallback sur colonnes si non trouvé en JSON
            if (!$hasAccess) {
                $col = self::getPermissionColumnForModule($moduleCode);
                if ($col && $user->$col) {
                    $hasAccess = true;
                }
            }

            $permissions[$moduleCode] = $hasAccess;
        }

        return $permissions;
    }

    /**
     * Obtenir les sous-modules autorisés pour un utilisateur
     */
    public static function getAllowedSubmodules(User $user, string $module): array
    {
        if (!$user) {
            return [];
        }

        $submodules = is_array($user->submodules) ? $user->submodules : [];

        // Filtrer les sous-modules pour le module demandé
        $allowedSubmodules = array_filter($submodules, function($submodule) use ($module) {
            return strpos($submodule, $module . '_') === 0;
        });

        return array_values($allowedSubmodules);
    }

    /**
     * Recharger les permissions depuis la base (ou initialiser par défaut)
     */
    public static function reloadUserPermissionsFromDatabase(User $user): void
    {
        $user->refresh();

        $modules = is_array($user->modules) ? $user->modules : [];
        $submodules = is_array($user->submodules) ? $user->submodules : [];

        // Si vraiment vide, donner les accès par défaut du rôle
        if (empty($modules) && empty($submodules)) {
            $modules = self::getDefaultModulesForRole($user->role);
            Log::info("Initialisation des permissions par défaut pour l'utilisateur {$user->id}");
        }

        self::updateUserPermissions($user, $modules, $submodules);
    }

    /**
     * Modules par défaut selon le rôle
     */
    private static function getDefaultModulesForRole(string $role): array
    {
        return match ($role) {
            'superadmin' => array_keys(self::getAllModules()),
            'admin' => ['dashboard'],
            'moderator', 'moderateur' => [], // Aucun module par défaut pour les modérateurs
            default => [],
        };
    }

    /**
     * Synchroniser model_has_permissions
     */
    public static function syncModelHasPermissions(User $user, array $modules, array $submodules = []): void
    {
        try {
            DB::table('model_has_permissions')
                ->where('model_id', $user->id)
                ->where('model_type', 'App\\Models\\User')
                ->delete();

            $allToSync = array_merge($modules, $submodules);
            $permissionIds = DB::table('permissions')
                ->whereIn('name', array_map(fn($n) => $n . '.access', $allToSync))
                ->pluck('id');

            if ($permissionIds->isNotEmpty()) {
                $inserts = $permissionIds->map(fn($id) => [
                    'permission_id' => $id,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $user->id
                ])->toArray();
                DB::table('model_has_permissions')->insert($inserts);
            }
        } catch (\Exception $e) {
            Log::warning("Erreur sync model_has_permissions : " . $e->getMessage());
        }
    }

    /**
     * Nettoyer le cache utilisateur
     */
    public static function clearUserCache(User $user)
    {
        $keys = ["user_permissions_{$user->id}", "user_roles_{$user->id}", "permissions_{$user->id}"];
        foreach ($keys as $key) Cache::forget($key);
    }


    /**
     * Valider les permissions pour un rôle
     */
    public static function validatePermissionsForRole($role, array $permissions)
    {
        $role = strtolower($role);
        $errors = [];
        $allModules = self::getAllModules();

        // Vérifier que les permissions obligatoires sont présentes
        foreach ($allModules as $moduleKey => $module) {
            if (in_array($role, $module['mandatory_for'] ?? [])) {
                if (!in_array($moduleKey, $permissions)) {
                    $label = $module['label'] ?? $module['name'] ?? $moduleKey;
                    $errors[] = "Le module '{$label}' est obligatoire pour le rôle '{$role}'";
                }
            }
        }

        return $errors;
    }
    /**
     * Synchronise les modules dans la table user_modules
     */
    public static function syncUserModulesTable(User $user, array $selectedModules): void
    {
        try {
            // 1. Supprimer les anciens accès
            DB::table('user_modules')->where('user_id', $user->id)->delete();

            // 2. Insérer les nouveaux accès
            if (!empty($selectedModules)) {
                $inserts = [];
                foreach ($selectedModules as $moduleCode) {
                    $inserts[] = [
                        'user_id' => $user->id,
                        'module_code' => $moduleCode,
                        'can_access' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('user_modules')->insert($inserts);
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de la synchronisation user_modules : " . $e->getMessage());
        }
    }
}
