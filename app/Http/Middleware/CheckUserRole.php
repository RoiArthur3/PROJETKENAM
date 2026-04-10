<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\RoleHelper;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
        if (!$user) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        // Si l'utilisateur n'a pas de rôle valide
        if (!isset($user->role) || empty($user->role)) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte n\'a pas de rôle défini. Veuillez contacter l\'administrateur.');
        }

        // Vérifier si l'utilisateur a l'un des rôles requis
        $hasRequiredRole = false;

        foreach ($roles as $role) {
            if (RoleHelper::canAccess($role)) {
                $hasRequiredRole = true;
                break;
            }
        }

        // Si l'utilisateur n'a aucun des rôles requis
        if (!$hasRequiredRole) {
            // Journaliser la tentative d'accès non autorisé
            Log::warning('Tentative d\'accès non autorisé', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'required_roles' => $roles,
                'path' => $request->path(),
                'ip' => $request->ip()
            ]);

            // Rediriger vers la page d'accueil appropriée avec un message d'erreur
            return redirect(RoleHelper::getDashboardForRole($user->role))
                ->with('error', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
        }

        return $next($request);
    }
}
