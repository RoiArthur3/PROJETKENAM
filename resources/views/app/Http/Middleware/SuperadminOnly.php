<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperadminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Si l'utilisateur n'est pas connecté, rediriger vers login
        if (!$user) {
            return redirect()->route('login');
        }

        // Vérifier si l'utilisateur est superadmin
        try {
            $roles = $user->getRoleNames()->toArray();

            if (!in_array('superadmin', $roles)) {
                // Rediriger vers le dashboard avec un message d'erreur
                return redirect()->route('dashboard')
                    ->with('error', 'Accès réservé aux superadmins uniquement.');
            }

            // Superadmin = accès total sans aucune restriction
            return $next($request);

        } catch (\Exception $e) {
            // En cas d'erreur, refuser l'accès par sécurité
            return redirect()->route('dashboard')
                ->with('error', 'Erreur lors de la vérification des permissions.');
        }
    }
}
