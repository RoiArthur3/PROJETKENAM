<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class ServiceAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si le service est authentifié
        if (!Session::get('service_authenticated')) {
            return redirect()->route('services.login')
                ->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        // Vérifier si le service existe toujours et est actif
        $service = Session::get('authenticated_service');
        if (!$service || !$service->actif) {
            Session::forget('service_authenticated');
            Session::forget('authenticated_service');
            Session::forget('service_id');

            return redirect()->route('services.login')
                ->with('error', 'Votre service n\'est plus actif. Veuillez contacter l\'administrateur.');
        }

        return $next($request);
    }
}
