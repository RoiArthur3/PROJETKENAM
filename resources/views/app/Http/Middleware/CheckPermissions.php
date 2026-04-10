<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $module = null, string $submenu = null)
    {
        $user = Auth::user();

        // If no user is logged in, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // Admin users can access everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check module access
        if ($module && !$user->canAccessModule($module)) {
            abort(403, 'Accès non autorisé à ce module.');
        }

        // Check submenu access
        if ($module && $submenu && !$user->canAccessSubmenu($module, $submenu)) {
            abort(403, 'Accès non autorisé à cette fonctionnalité.');
        }

        return $next($request);
    }
}
