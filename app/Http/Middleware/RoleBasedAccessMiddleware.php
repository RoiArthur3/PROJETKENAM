<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;

class RoleBasedAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role = null)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Vérification des permissions selon le rôle
        if ($role && !$this->hasRole($user, $role)) {
            return redirect()->route('login')
                ->with('error', 'Accès non autorisé pour votre rôle.');
        }

        // Vérification des permissions spécifiques au module
        $module = $this->getModuleFromRoute($request);
        if ($module && !AuthService::checkUserPermissions($user, $module)) {
            return redirect()->route('operations.index')
                ->with('error', 'Accès non autorisé à ce module.');
        }

        return $next($request);
    }

    /**
     * Vérifier si l'utilisateur a le rôle requis
     */
    private function hasRole($user, $requiredRole)
    {
        $roleHierarchy = [
            'superadmin' => ['superadmin', 'admin', 'moderator', 'moderateur', 'agent'],
            'admin' => ['admin', 'moderator', 'moderateur', 'agent'],
            'moderator' => ['moderator', 'moderateur'],
            'moderateur' => ['moderator', 'moderateur'],
            'agent' => ['agent']
        ];

        return in_array($user->role, $roleHierarchy[$requiredRole] ?? []);
    }

    /**
     * Extraire le module de la route actuelle
     */
    private function getModuleFromRoute($request)
    {
        $routeName = $request->route()->getName();
        
        if (str_starts_with($routeName, 'operations.')) {
            return 'operations';
        }
        
        if (str_starts_with($routeName, 'dashboard')) {
            return 'dashboard';
        }
        
        if (str_starts_with($routeName, 'admin.')) {
            return 'admin';
        }
        
        return null;
    }
}
