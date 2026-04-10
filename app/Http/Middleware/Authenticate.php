<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if (! $this->auth->guard($guard)->check()) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Non authentifié.'], 401);
                }
                return redirect()->guest(route('login'));
            }

            // Vérifier si l'utilisateur est actif
            $user = Auth::guard($guard)->user();
            if ($user && isset($user->is_active) && !$user->is_active) {
                Auth::guard($guard)->logout();
                return redirect()->route('login')->with('error', 'Votre compte est désactivé.');
            }
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
