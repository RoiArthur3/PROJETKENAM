<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompteTresoMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur est connecté et a le rôle comptetreso
        if (!$user || $user->role !== 'comptetreso') {
            abort(403, 'Accès réservé au Compte Treso');
        }
        
        return $next($request);
    }
}
