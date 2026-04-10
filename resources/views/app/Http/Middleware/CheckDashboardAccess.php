<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware pour protéger l'accès au dashboard principal
 * Seul le Superadmin peut y accéder
 */
class CheckDashboardAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Seul le Superadmin peut accéder au dashboard principal
        if (Auth::user()->role !== 'superadmin') {
            // Rediriger vers operations/dashboard au lieu d'afficher une erreur 403
            return redirect('/operations/dashboard')
                ->with('warning', 'Accès au Dashboard réservé au Super Administrateur');
        }

        return $next($request);
    }
}
