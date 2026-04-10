<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SimpleCheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Vérification simple par rôle depuis la colonne role de la table users
        $userRole = $user->role ?? 'user';
        
        // Admin et superadmin ont accès à tout
        if (in_array($userRole, ['admin', 'superadmin'])) {
            return $next($request);
        }
        
        // Pour les services, autoriser l'accès si l'utilisateur a un rôle valide
        if (str_starts_with($permission, 'services.') && in_array($userRole, ['admin', 'superadmin', 'tresorerie', 'commercial', 'rh', 'comptabilite'])) {
            return $next($request);
        }
        
        // Pour les paramètres, autoriser l'accès aux rôles de gestion
        if ($permission === 'settings.view' && in_array($userRole, ['admin', 'superadmin'])) {
            return $next($request);
        }
        
        // Si la permission est dans la configuration, vérifier
        $rolePermissions = config('permissions.roles.' . $userRole, []);
        if (in_array('*', $rolePermissions) || in_array($permission, $rolePermissions)) {
            return $next($request);
        }
        
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        abort(403, 'Accès non autorisé');
    }
}
