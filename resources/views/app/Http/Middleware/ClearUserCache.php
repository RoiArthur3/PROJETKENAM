<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ClearUserCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Si c'est une requête GET sur la page d'édition d'utilisateur, nettoyer le cache
        if ($request->isMethod('GET') && $request->is('admin/users/*/edit')) {
            $userId = $request->route('user');
            if ($userId) {
                $cacheKeys = [
                    "user_permissions_{$userId}",
                    "user_roles_{$userId}",
                    "permissions_{$userId}",
                    "roles_{$userId}",
                ];

                foreach ($cacheKeys as $key) {
                    Cache::forget($key);
                }
            }
        }

        return $next($request);
    }
}
