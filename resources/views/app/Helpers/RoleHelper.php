<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class RoleHelper
{
    /**
     * Récupère la configuration des rôles
     *
     * @return array
     */
    public static function getRolesConfig()
    {
        return config('app.roles', []);
    }

    /**
     * Vérifie si l'utilisateur connecté a un rôle spécifique
     *
     * @param string|array $roles
     * @return bool
     */
    public static function hasRole($roles)
    {
        $user = Auth::user();

        if (!$user || !isset($user->role)) {
            return false;
        }

        if (is_string($roles)) {
            $roles = [$roles];
        }

        return in_array($user->role, $roles);
    }

    /**
     * Vérifie si l'utilisateur connecté a un niveau d'accès minimum
     *
     * @param int $minLevel
     * @return bool
     */
    public static function hasMinLevel($minLevel)
    {
        $user = Auth::user();

        if (!$user || !isset($user->role)) {
            return false;
        }

        $roles = self::getRolesConfig();

        if (!isset($roles[$user->role])) {
            return false;
        }

        return $roles[$user->role]['level'] >= $minLevel;
    }

    /**
     * Récupère le tableau de bord par défaut pour un rôle
     *
     * @param string|null $role
     * @return string
     */
    public static function getDashboardForRole($role = null)
    {
        if ($role === null) {
            $user = Auth::user();
            $role = $user ? $user->role : 'user';
        }

        $roles = self::getRolesConfig();

        return $roles[$role]['dashboard'] ?? '/';
    }

    /**
     * Vérifie si l'utilisateur actuel peut accéder à une route protégée
     *
     * @param string $requiredRole
     * @return bool
     */
    public static function canAccess($requiredRole)
    {
        $user = Auth::user();

        if (!$user || !isset($user->role)) {
            return false;
        }

        $roles = self::getRolesConfig();

        // Si le rôle de l'utilisateur n'existe pas dans la configuration
        if (!isset($roles[$user->role])) {
            return false;
        }

        // Si le rôle requis n'existe pas dans la configuration
        if (!isset($roles[$requiredRole])) {
            return false;
        }

        // Vérifier si le niveau de l'utilisateur est suffisant
        return $roles[$user->role]['level'] >= $roles[$requiredRole]['level'];
    }

    /**
     * Vérifie si l'utilisateur actuel peut accéder à un module
     *
     * @param string $module
     * @return bool
     */
    public static function canAccessModule($module)
    {
        $user = Auth::user();

        if (!$user || !isset($user->role)) {
            return false;
        }

        // Vérifier les restrictions pour l'admin
        if ($user->role === 'admin') {
            $adminConfig = config('admin_modules');

            // Si l'admin n'a pas accès complet et le module est restreint
            if (!$adminConfig['admin_has_full_access'] &&
                in_array($module, $adminConfig['restricted_modules'])) {
                return false;
            }

            // Si l'admin a accès complet, il peut tout voir
            if ($adminConfig['admin_has_full_access']) {
                return true;
            }
        }

        // Vérifier les modules supplémentaires pour le modérateur
        if ($user->role === 'moderator') {
            $moderatorConfig = config('moderator_modules');

            // Modules de base du modérateur
            $baseModules = ['operations', 'validations'];

            // Si le modérateur peut avoir des modules supplémentaires
            if ($moderatorConfig['can_have_additional_modules']) {
                $additionalModules = $moderatorConfig['additional_modules'];
                $allowedModules = array_merge($baseModules, $additionalModules);
                return in_array($module, $allowedModules);
            }

            // Sinon, seulement les modules de base
            return in_array($module, $baseModules);
        }

        // Pour les autres rôles, utiliser la configuration des modules
        $modules = config('modules.modules', []);

        if (!isset($modules[$user->role])) {
            return false;
        }

        return in_array('*', $modules[$user->role]) ||
               in_array($module, $modules[$user->role]);
    }
}
