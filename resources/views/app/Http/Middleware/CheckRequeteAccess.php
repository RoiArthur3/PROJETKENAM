<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\CheckAdminAccess;
use Symfony\Component\HttpFoundation\Response;

class CheckRequeteAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return response()->view('auth.login', [], 403);
        }

        // Utiliser le middleware CheckAdminAccess pour la vérification du superadmin
        $adminMiddleware = new CheckAdminAccess();
        return $adminMiddleware->handle($request, $next, 'requetes');
    }

    /**
     * Vérifie si l'utilisateur est superadmin
     */
    private function isSuperadmin($user): bool
    {
        try {
            return in_array('superadmin', $user->getRoleNames()->toArray());
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du rôle superadmin', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return false;
        }
    }

    /**
     * Vérifie si l'utilisateur a accès à la ressource
     */
    private function userHasAccess($user): bool
    {
        // Vérifier les rôles autorisés
        return $this->hasAnyRole($user, ['admin', 'agent']);
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    private function hasRole($user, string $role): bool
    {
        if (!method_exists($user, 'getRoleNames')) {
            return false;
        }
        return in_array($role, $user->getRoleNames()->toArray());
    }

    /**
     * Vérifie si l'utilisateur a un des rôles spécifiés
     */
    private function hasAnyRole($user, array $roles): bool
    {
        if (!method_exists($user, 'getRoleNames')) {
            return false;
        }
        return !empty(array_intersect($roles, $user->getRoleNames()->toArray()));
    }

    /**
     * Journalise un accès refusé
     */
    private function logAccessDenied($user, Request $request): void
    {
        $roles = [];
        try {
            $roles = method_exists($user, 'getRoleNames')
                ? $user->getRoleNames()
                : ['getRoleNames non disponible'];
        } catch (\Exception $e) {
            $roles = ['Erreur: ' . $e->getMessage()];
        }

        Log::warning('Accès refusé', [
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $roles,
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_class' => get_class($user)
        ]);
    }
}
