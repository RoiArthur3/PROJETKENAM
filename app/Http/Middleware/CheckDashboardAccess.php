<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;

/**
 * Middleware pour protéger l'accès au dashboard principal
 * Seul le Superadmin peut y accéder
 */
class CheckDashboardAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Vérifier si l'utilisateur a accès au dashboard
        if (!AuthService::checkUserPermissions($user, 'dashboard')) {
            $redirectRoute = AuthService::getRedirectRoute($user);
            return redirect()->route($redirectRoute)
                ->with('warning', 'Accès au Dashboard réservé au Super Administrateur');
        }

        return $next($request);
    }
}
