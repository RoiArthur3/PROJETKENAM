<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminAccess
{
    /**
     * Vérifie si l'utilisateur est superadmin ou a accès au module demandé
     */
    public function handle(Request $request, Closure $next, string $module = null): Response
    {
        $user = Auth::user();

        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
        if (!$user) {
            Log::warning('ACCÈS REFUSÉ - UTILISATEUR NON AUTHENTIFIÉ', [
                'module' => $module,
                'path' => $request->path(),
                'ip' => $request->ip(),
                'session_id' => $request->session()->getId(),
                'user_agent' => $request->userAgent(),
            ]);
            return response()->view('auth.login', [], 403);
        }

        // PRIORITÉ ABSOLUE: Si l'utilisateur est superadmin, accès total immédiat sans AUCUNE vérification
        if ($this->isSuperadmin($user)) {
            Log::info('ACCÈS SUPERADMIN AUTORISÉ - ACCÈS TOTAL SANS RESTRICTION', [
                'user_id' => $user->id,
                'email' => $user->email,
                'module' => $module,
                'path' => $request->path(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);
            return $next($request);
        }

        // Pour les non-superadmins, appliquer les vérifications normales
        if ($module && !$user->canAccessModule($module)) {
            Log::warning("Tentative d'accès non autorisé au module $module", ['user' => $user->id]);
            abort(403, 'Accès non autorisé à ce module');
        }

        // Vérification pour le module Stock
        if ($module === 'stock') {
            if (!$user->hasRole('superadmin') && !$user->hasPermissionTo('stock.view')) {
                Log::warning("Tentative d'accès non autorisé au module Stock", ['user' => $user->id]);
                abort(403, 'Accès non autorisé au module Stock');
            }
        }

        return $next($request);
    }

    /**
     * Vérifie si l'utilisateur est superadmin
     */
    private function isSuperadmin($user): bool
    {
        try {
            $roles = $user->getRoleNames()->toArray();
            return in_array('superadmin', $roles);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du rôle superadmin', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return false;
        }
    }

    /**
     * Vérifie si l'utilisateur a accès au module spécifié
     */
    private function hasModuleAccess($user, string $module): bool
    {
        // Si l'utilisateur a le rôle superadmin, il a accès à tout (double sécurité)
        if ($this->isSuperadmin($user)) {
            return true;
        }

        // Pour le module admin, autoriser les admins
        if ($module === 'admin') {
            return in_array('admin', $user->getRoleNames()->toArray());
        }

        // Pour les modules comptabilité, vérifier les deux noms
        if ($module === 'compta' || $module === 'accounting') {
            return in_array('admin', $user->getRoleNames()->toArray()) ||
                   in_array('compta', $user->getRoleNames()->toArray()) ||
                   $this->canAccessModule($user, 'accounting');
        }

        // Pour le module requetes, autoriser tous les utilisateurs connectés
        if ($module === 'requetes') {
            return true; // Tous les utilisateurs connectés ont accès aux requêtes
        }

        // Pour les autres modules, utiliser notre système de permissions
        return $this->canAccessModule($user, $module);
    }

    /**
     * Vérifie si l'utilisateur peut accéder à un module avec notre système
     */
    private function canAccessModule($user, string $module): bool
    {
        try {
            // Utiliser notre service UserPermissionService
            $permissions = \App\Services\UserPermissionService::getUserPermissions($user);
            return $permissions[$module] ?? false;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du module', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'module' => $module
            ]);
            return false;
        }
    }
}
