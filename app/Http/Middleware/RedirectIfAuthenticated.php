<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    private const REDIRECT_MODULES = [
        'operations',
        'validations',
        'tresorerie',
        'accounting',
        'comptabilite',
        'rh',
        'materiel',
        'warehouse',
        'commercial',
        'fournisseurs',
        'projects',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  ...$guards
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                /** @var User $user */
                $user = Auth::guard($guard)->user();

                if (in_array($user->role, ['superadmin', 'admin'], true)) {
                    return redirect()->route('dashboard');
                }

                foreach (self::REDIRECT_MODULES as $module) {
                    if ($user->canAccessModule($module)) {
                        return redirect($user->getFirstAccessibleModuleUrl());
                    }
                }

                Auth::guard($guard)->logout();
            }
        }

        return $next($request);
    }
}
