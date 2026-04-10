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
        'dashboard' => 'can_access_dashboard',
        'operations' => 'can_access_operations',
        'requetes' => 'can_access_operations',
        'tracking' => 'can_access_operations',
        'suivi' => 'can_access_operations', // Suivi utilise la même colonne que operations
        'validations' => 'can_access_operations', // Validations utilise la même colonne que operations
        'fleet' => 'can_access_fleet',
        'parc' => 'can_access_fleet',
        'hr' => 'can_access_hr',
        'rh' => 'can_access_hr',
        'suppliers' => 'can_access_suppliers',
        'fournisseurs' => 'can_access_suppliers',
        'warehouse' => 'can_access_warehouse',
        'stock' => 'can_access_warehouse',
        'accounting' => 'can_access_accounting',
        'comptabilite' => 'can_access_accounting',
        'invoicing' => 'can_access_invoicing',
        'facturation' => 'can_access_invoicing',
        'reporting' => 'can_access_reporting',
        'commercial' => 'can_access_commercial',
        'prospection' => 'can_access_prospection',
        'ateliers' => 'can_access_ateliers',
    ];

    private static function getPermissionColumnForModule(string $moduleCode): ?string
    {
        return self::$moduleToColumn[$moduleCode] ?? null;
    }

    /**
     * Définition des modules disponibles avec leurs métadonnées
     */
    private static $modules = [
        'operations' => [
            'label' => 'Opérations',
            'icon' => 'fas fa-cogs',
            'description' => 'Gestion des opérations',
            'mandatory_for' => ['agent', 'moderator'],
            'route' => '/operations'
        ],
        'suivi' => [
            'label' => 'Suivi',
            'icon' => 'fas fa-chart-line',
            'description' => 'Suivi des opérations',
            'mandatory_for' => ['moderator'],
            'route' => '/operations/suivi'
        ],
        'validations' => [
            'label' => 'Validations',
            'icon' => 'fas fa-check-circle',
            'description' => 'Validation des opérations',
            'mandatory_for' => ['moderator'],
            'route' => '/validations/pending'
        ],
        'dashboard' => [
            'label' => 'Tableau de bord',
            'icon' => 'fas fa-tachometer-alt',
            'description' => 'Accès au tableau de bord principal',
            'mandatory_for' => ['admin', 'superadmin'],
            'route' => '/dashboard'
        ],
        'requetes' => [
            'label' => 'Requêtes',
            'icon' => 'fas fa-clipboard-list',
            'description' => 'Gestion des requêtes',
            'mandatory_for' => ['agent'],
            'route' => '/requetes'
        ],
        'agents' => [
            'label' => 'Agents',
            'icon' => 'fas fa-users',
            'description' => 'Gestion des agents liés aux services',
            'mandatory_for' => ['admin', 'superadmin'],
            'route' => '/admin/agents'
        ],
        'fleet' => [
            'label' => 'Parc Auto',
            'icon' => 'fas fa-car',
            'mandatory_for' => [], // Plus obligatoire pour personne
            'route' => '/fleet'
        ],
        'warehouse' => [
            'label' => 'Entrepôt',
            'icon' => 'fas fa-warehouse',
            'mandatory_for' => [], // Plus obligatoire pour personne
            'route' => '/stock'
        ],
        'suppliers' => [
            'label' => 'Fournisseurs',
            'icon' => 'fas fa-truck',
            'mandatory_for' => [], // Plus obligatoire pour personne
            'route' => '/fournisseurs'
        ],
        'accounting' => [
            'label' => 'Comptabilité',
            'icon' => 'fas fa-calculator',
            'mandatory_for' => [], // Plus obligatoire pour personne
            'route' => '/comptabilite'
        ],
        'commercial' => [
            'label' => 'Commercial',
            'icon' => 'fas fa-handshake',
            'mandatory_for' => [], // Plus obligatoire pour personne
            'route' => '/commercial'
        ],
        'projects' => [
            'label' => 'Projets',
            'icon' => 'fas fa-project-diagram',
            'mandatory_for' => [], // Plus obligatoire pour personne
            'route' => '/projets'
        ],
        'audit' => [
            'label' => 'Audit',
            'icon' => 'fas fa-search',
            'mandatory_for' => [], // Plus obligatoire pour personne
            'route' => '/controle-audit'
        ],
        'treasury' => [
            'label' => 'Trésorerie',
            'icon' => 'fas fa-cash-register',
            'mandatory_for' => [], // Plus obligatoire pour personne
            'route' => '/tresorerie'
        ],
        'services' => [
            'label' => 'Services',
            'icon' => 'fas fa-sitemap',
            'mandatory_for' => [], // Plus obligatoire pour personne
            'route' => '/admin/services'
        ],
        'comptes' => [
            'label' => 'Comptes',
            'icon' => 'fas fa-users-cog',
            'mandatory_for' => [], // Plus obligatoire pour personne
            'route' => '/admin/comptes'
        ],
        'system' => [
            'label' => 'Système',
            'icon' => 'fas fa-server',
            'mandatory_for' => ['superadmin'], // Réservé aux superadmins
            'route' => '/admin/systeme'
        ],
        'reporting' => [
            'label' => 'Rapports',
            'icon' => 'fas fa-chart-bar',
            'mandatory_for' => [], // Plus obligatoire pour personne
            'route' => '/rapports'
        ]
    ];

    /**
     * Obtenir tous les modules disponibles
     */
    public static function getAllModules()
    {
        // Essayer la table modules si elle existe
        try {
            $dbModules = DB::table('modules')->where('is_active', true)->orderBy('sort_order')->get()->keyBy('name');

            if ($dbModules->isNotEmpty()) {
                $modules = [];
                foreach ($dbModules as $module) {
                    $modules[$module->name] = [
                        'label' => $module->display_name,
                        'icon' => $module->icon,
                        'description' => $module->description,
                        'color' => $module->color
                    ];
                }
                return $modules;
            }
        } catch (\Exception $e) {
            // Table n'existe pas, utiliser le fallback
        }

        // Fallback : liste des modules en dur
        return [
            'rh' => ['label' => 'Ressources Humaines', 'icon' => 'fas fa-users', 'description' => 'Gestion RH', 'color' => '#6f42c1'],
            'projects' => ['label' => 'Projets', 'icon' => 'fas fa-project-diagram', 'description' => 'Gestion de projets', 'color' => '#0d6efd'],
            'commercial' => ['label' => 'Commercial', 'icon' => 'fas fa-handshake', 'description' => 'Module commercial', 'color' => '#fd7e14'],
            'comptabilite' => ['label' => 'Comptabilité', 'icon' => 'fas fa-calculator', 'description' => 'Comptabilité', 'color' => '#198754'],
            'tresorerie' => ['label' => 'Trésorerie', 'icon' => 'fas fa-money-bill-wave', 'description' => 'Trésorerie', 'color' => '#20c997'],
            'fournisseurs' => ['label' => 'Fournisseurs', 'icon' => 'fas fa-truck', 'description' => 'Gestion fournisseurs', 'color' => '#6610f2'],
            'magasin' => ['label' => 'Magasin', 'icon' => 'fas fa-store', 'description' => 'Gestion magasin', 'color' => '#d63384'],
            'entrepots' => ['label' => 'Entrepôts', 'icon' => 'fas fa-warehouse', 'description' => 'Gestion entrepôts', 'color' => '#0dcaf0'],
            'materiel' => ['label' => 'Matériel', 'icon' => 'fas fa-tools', 'description' => 'Gestion matériel', 'color' => '#6c757d'],
            'materiel_roulant' => ['label' => 'Matériel Roulant', 'icon' => 'fas fa-car', 'description' => 'Parc automobile et matériel roulant', 'color' => '#dc3545'],
            'checking' => ['label' => 'Checking', 'icon' => 'fas fa-clipboard-check', 'description' => 'Vérifications et contrôles', 'color' => '#ffc107'],
            'audit' => ['label' => 'Audit', 'icon' => 'fas fa-search', 'description' => 'Audit et contrôle', 'color' => '#343a40'],
            'reporting' => ['label' => 'Reporting', 'icon' => 'fas fa-chart-bar', 'description' => 'Rapports', 'color' => '#17a2b8'],
            'settings' => ['label' => 'Paramètres', 'icon' => 'fas fa-cog', 'description' => 'Configuration', 'color' => '#adb5bd'],
        ];
    }

    /**
     * Obtenir les modules disponibles pour un rôle spécifique
     */
    public static function getModulesForRole($role)
    {
        $availableModules = [];
        $allModules = self::getAllModules();

        foreach ($allModules as $key => $module) {
            // Logique simplifiée : seul operations est obligatoire pour tous sauf superadmin
            $isMandatory = ($key === 'operations' && $role !== 'superadmin');

            // Pour les modérateurs : donner accès à tous les modules sauf ceux réservés aux admins
            if ($role === 'moderator') {
                // Toujours inclure operations (obligatoire)
                if ($key === 'operations') {
                    $isMandatory = true;
                } else {
                    // Exclure uniquement les modules réservés aux admins/superadmins
                    $adminOnlyModules = ['dashboard', 'agents', 'comptes', 'services', 'system'];
                    if (in_array($key, $adminOnlyModules)) {
                        continue; // Skip les modules réservés aux admins
                    }
                }
            }

            // Pour les admins : donner accès à tous les modules sans restriction
            if ($role === 'admin') {
                // Tous les modules sont disponibles pour les admins
                // Aucune restriction particulière
                if ($key === 'dashboard') {
                    $isMandatory = true; // Dashboard obligatoire pour les admins
                }
            }

            $availableModules[$key] = [
                'label' => $module['label'],
                'icon' => $module['icon'],
                'route' => $module['route'] ?? '#',
                'mandatory' => $isMandatory,
                'default_checked' => $isMandatory
            ];
        }

        return $availableModules;
    }

    /**
     * Mettre à jour les permissions d'un utilisateur de manière robuste
     */
    public static function updateUserPermissions(User $user, array $selectedModules = [])
    {
        $role = method_exists($user, 'getRoleNames') ? ($user->getRoleNames()->first() ?? null) : null;
        $role = $role ?: ($user->role ?? null);

        Log::info("Mise à jour des permissions pour user {$user->id}, rôle: {$role}");
        Log::info("Modules sélectionnés: " . json_encode($selectedModules));

        // Supprimer les anciennes permissions
        DB::table('user_modules')->where('user_id', $user->id)->delete();

        // Insérer les nouvelles permissions
        $insertData = [];
        foreach ($selectedModules as $moduleCode) {
            $insertData[] = [
                'user_id' => $user->id,
                'module_code' => $moduleCode,
                'can_access' => true,
                'created_at' => now(),
                'updated_at' => now()
            ];
            Log::info("Permission ajoutée: {$moduleCode}");
        }

        if (!empty($insertData)) {
            DB::table('user_modules')->insert($insertData);
            Log::info("Permissions insérées en base: " . count($insertData) . " modules");
        }

        // Nettoyer le cache
        self::clearUserCache($user);

        Log::info("Mise à jour des permissions terminée avec succès");
    }

    /**
     * Vérifier si un utilisateur peut accéder à un module spécifique
     */
    public static function canUserAccessModule(User $user, string $moduleCode): bool
    {
        // Forcer le rechargement depuis la base de données pour éviter les problèmes de cache
        $permission = DB::table('user_modules')
            ->where('user_id', $user->id)
            ->where('module_code', $moduleCode)
            ->where('can_access', true)
            ->first();

        $hasAccess = $permission ? true : false;

        // Debug logging
        Log::info("User {$user->id} ({$user->name}) access check for module '{$moduleCode}': " . ($hasAccess ? 'GRANTED' : 'DENIED'));

        return $hasAccess;
    }

    /**
     * Obtenir les permissions d'un utilisateur
     */
    public static function getUserPermissions(User $user): array
    {
        $permissions = [];

        // Récupérer les permissions depuis la table user_modules
        $userModulePermissions = DB::table('user_modules')
            ->where('user_id', $user->id)
            ->where('can_access', true)
            ->pluck('module_code')
            ->toArray();

        // Récupérer tous les modules disponibles
        $allModules = self::getAllModules();

        // Pour chaque module disponible, vérifier si l'utilisateur y a accès
        foreach ($allModules as $moduleCode => $moduleConfig) {
            $permissions[$moduleCode] = in_array($moduleCode, $userModulePermissions);
        }

        return $permissions;
    }

    /**
     * Nettoyer le cache d'un utilisateur
     */
    public static function clearUserCache(User $user)
    {
        $cacheKeys = [
            "user_permissions_{$user->id}",
            "user_roles_{$user->id}",
            "permissions_{$user->id}",
            "roles_{$user->id}",
            "user_modules_{$user->id}",
            "module_permissions_{$user->id}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Nettoyer aussi le cache global
        Cache::forget('all_modules');

        Log::info("Cache nettoyé pour user {$user->id}");
    }

    /**
     * Valider les permissions pour un rôle
     */
    public static function validatePermissionsForRole($role, array $permissions)
    {
        $role = strtolower($role);
        $errors = [];

        // Vérifier que les permissions obligatoires sont présentes
        foreach (self::$modules as $moduleKey => $module) {
            if (in_array($role, $module['mandatory_for'] ?? [])) {
                if (!in_array($moduleKey, $permissions)) {
                    $errors[] = "Le module '{$module['label']}' est obligatoire pour le rôle '{$role}'";
                }
            }
        }

        return $errors;
    }
}
