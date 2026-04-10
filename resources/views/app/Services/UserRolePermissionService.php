<?php

namespace App\Services;

use App\Models\User;

class UserRolePermissionService
{
    /**
     * Appliquer les permissions selon le rôle de l'utilisateur
     */
    public static function applyRolePermissions(User $user): void
    {
        switch ($user->role) {
            case 'superadmin':
                self::applySuperadminPermissions($user);
                break;
            case 'admin':
                self::applyAdminPermissions($user);
                break;
            case 'moderator':
                self::applyModeratorPermissions($user);
                break;
            case 'agent':
            case 'user':
                self::applyAgentPermissions($user);
                break;
            default:
                self::applyDefaultPermissions($user);
                break;
        }

        $user->save();
    }

    /**
     * Permissions pour le Super Admin - ACCÈS TOTAL
     */
    private static function applySuperadminPermissions(User $user): void
    {
        // Tableau de bord
        $user->can_access_dashboard = 1;
        $user->can_access_validations = 1; // Accès au suivi et validations

        // Tous les modules
        $user->can_access_operations = 1;
        $user->can_access_hr = 1;
        $user->can_access_fleet = 1;
        $user->can_access_suppliers = 1;
        $user->can_access_warehouse = 1;
        $user->can_access_accounting = 1;
        $user->can_access_invoicing = 1;
        $user->can_access_reporting = 1;
        $user->can_access_commercial = 1;
        $user->can_access_prospection = 1;
        $user->can_access_ateliers = 1;
        $user->can_access_projects = 1;
        $user->can_access_treasury = 1;
        $user->can_access_audit = 1;
        $user->can_access_services = 1;
        $user->can_access_comptes = 1;
        $user->can_access_system = 1;
    }

    /**
     * Permissions pour l'Admin - TOUS SAUF tableau de bord général
     */
    private static function applyAdminPermissions(User $user): void
    {
        // PAS d'accès au tableau de bord général
        $user->can_access_dashboard = 0;
        $user->can_access_validations = 1; // Accès au suivi et validations

        // Tous les autres modules
        $user->can_access_operations = 1;
        $user->can_access_hr = 1;
        $user->can_access_fleet = 1;
        $user->can_access_suppliers = 1;
        $user->can_access_warehouse = 1;
        $user->can_access_accounting = 1;
        $user->can_access_invoicing = 1;
        $user->can_access_reporting = 1;
        $user->can_access_commercial = 1;
        $user->can_access_prospection = 1;
        $user->can_access_ateliers = 1;
        $user->can_access_projects = 1;
        $user->can_access_treasury = 1;
        $user->can_access_audit = 1;
        $user->can_access_services = 1;
        $user->can_access_comptes = 1;
        $user->can_access_system = 1;
    }

    /**
     * Permissions pour le Modérateur - UNIQUEMENT les modules cochés
     * 
     * Règles:
     * - Pas de permissions par défaut
     * - Le modérateur ne voit QUE les modules qu'il a été autorisé à voir
     * - Si aucun module n'est coché, il n'a accès à RIEN
     * - À l'édition, les modules déjà cochés restent cochés
     */
    private static function applyModeratorPermissions(User $user): void
    {
        // Pas d'accès au tableau de bord général
        $user->can_access_dashboard = 0;
        
        // Les autres colonnes peuvent être vides (gérées par model_has_permissions)
        // On NE donne PAS de permissions par défaut
        // Le modérateur n'a accès qu'aux modules qu'il a demandé
    }

    /**
     * Permissions pour l'Agent/Utilisateur simple - SEULEMENT Opérations & Validations
     */
    private static function applyAgentPermissions(User $user): void
    {
        // PAS d'accès au tableau de bord général
        $user->can_access_dashboard = 0;

        // SEULEMENT les opérations et le suivi/validations
        $user->can_access_operations = 1;
        $user->can_access_validations = 1;

        // TOUS les autres modules à 0
        $user->can_access_hr = 0;
        $user->can_access_fleet = 0;
        $user->can_access_suppliers = 0;
        $user->can_access_warehouse = 0;
        $user->can_access_accounting = 0;
        $user->can_access_invoicing = 0;
        $user->can_access_reporting = 0;
        $user->can_access_commercial = 0;
        $user->can_access_prospection = 0;
        $user->can_access_ateliers = 0;
        $user->can_access_projects = 0;
        $user->can_access_treasury = 0;
        $user->can_access_audit = 0;
        $user->can_access_services = 0;
        $user->can_access_comptes = 0;
        $user->can_access_system = 0;
    }

    /**
     * Permissions par défaut - minimal
     */
    private static function applyDefaultPermissions(User $user): void
    {
        // Tout désactivé par défaut
        $user->can_access_dashboard = 0;
        $user->can_access_operations = 0;
        $user->can_access_hr = 0;
        $user->can_access_fleet = 0;
        $user->can_access_suppliers = 0;
        $user->can_access_warehouse = 0;
        $user->can_access_accounting = 0;
        $user->can_access_invoicing = 0;
        $user->can_access_reporting = 0;
        $user->can_access_commercial = 0;
        $user->can_access_prospection = 0;
        $user->can_access_ateliers = 0;
        $user->can_access_projects = 0;
        $user->can_access_treasury = 0;
        $user->can_access_audit = 0;
        $user->can_access_services = 0;
        $user->can_access_comptes = 0;
        $user->can_access_system = 0;
    }

    /**
     * Mettre à jour les permissions pour tous les utilisateurs
     */
    public static function updateAllUsersPermissions(): void
    {
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                self::applyRolePermissions($user);
            }
        });
    }

    /**
     * Vérifier si un utilisateur a accès à un module
     */
    public static function canAccessModule(User $user, string $module): bool
    {
        $permissionField = match ($module) {
            'dashboard' => 'can_access_dashboard',
            'operations' => 'can_access_operations',
            'hr' => 'can_access_hr',
            'fleet' => 'can_access_fleet',
            'suppliers' => 'can_access_suppliers',
            'warehouse' => 'can_access_warehouse',
            'accounting' => 'can_access_accounting',
            'invoicing' => 'can_access_invoicing',
            'reporting' => 'can_access_reporting',
            'commercial' => 'can_access_commercial',
            'prospection' => 'can_access_prospection',
            'ateliers' => 'can_access_ateliers',
            'projects' => 'can_access_projects',
            'treasury' => 'can_access_treasury',
            'audit' => 'can_access_audit',
            'services' => 'can_access_services',
            'comptes' => 'can_access_comptes',
            'system' => 'can_access_system',
            default => null,
        };

        if (!$permissionField) {
            return false;
        }

        // Le superadmin a tout accès
        if ($user->role === 'superadmin') {
            return true;
        }

        // L'admin a tout accès sauf le dashboard
        if ($user->role === 'admin' && $module !== 'dashboard') {
            return true;
        }

        return (bool) $user->$permissionField;
    }

    /**
     * Obtenir le tableau de bord approprié pour un utilisateur
     */
    public static function getUserDashboardRoute(User $user): ?string
    {
        return match ($user->role) {
            'superadmin' => 'dashboard',
            'admin' => 'operations.dashboard', // Rediriger vers le dashboard des opérations
            'moderator' => 'operations.dashboard',
            'agent', 'user' => 'operations.dashboard',
            default => null,
        };
    }
}
