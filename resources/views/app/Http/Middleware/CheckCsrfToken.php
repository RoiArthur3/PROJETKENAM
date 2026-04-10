<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class CheckCsrfToken extends BaseVerifier
{
    /**
     * Les URIs à exclure de la vérification CSRF
     */
    protected $except = [
        // Exclure les routes de connexion/déconnexion pour éviter les faux positifs
        'login',
        'logout',
        'register',
        'phone-login',
        'phone-login/*',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        // En développement, être beaucoup plus permissif
        if (app()->environment('local')) {
            // Ne pas vérifier CSRF pour les requêtes GET
            if ($request->isMethod('get')) {
                return $next($request);
            }
        }

        return parent::handle($request, $next);
    }
}
