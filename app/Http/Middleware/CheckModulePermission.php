<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckModulePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $module
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $module = null)
    {
        if (!$module) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Si pas d'utilisateur connecté, continuer (le middleware auth s'en chargera)
        if (!$user) {
            return $next($request);
        }

        // Vérifier si l'utilisateur a accès au module
        if (!$user->canAccessModule($module)) {
            abort(403, 'Accès non autorisé au module ' . $module);
        }

        return $next($request);
    }
}
