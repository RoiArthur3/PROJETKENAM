<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Si l'utilisateur est admin ou superadmin, donner accès à tout
        if ($user->hasRole('admin') || $user->hasRole('superadmin')) {
            return $next($request);
        }

        // Vérifier si l'utilisateur a la permission spécifique
        if (!$user->hasPermission($permission)) {
            // Pour les services, si l'utilisateur a accès aux paramètres, autoriser l'accès
            if (str_starts_with($permission, 'services.') && $user->hasPermission('settings.view')) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized.'], 403);
            }

            abort(403, 'Accès non autorisé');
        }

        return $next($request);
    }
}
