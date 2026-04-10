<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilterUserOperations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Si l'utilisateur est admin ou superadmin, voir tout
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Pour les autres rôles, filtrer selon leurs permissions
        if ($request->route()->getName() === 'operations.index') {
            // Ajouter un filtre pour ne voir que les opérations pertinentes
            $request->merge([
                'user_filter' => $this->getUserOperationFilter($user)
            ]);
        }

        return $next($request);
    }

    /**
     * Déterminer le filtre d'opérations pour l'utilisateur
     */
    private function getUserOperationFilter($user)
    {
        $userRole = $user->roles->first()?->name ?? 'user';

        if ($userRole === 'agent') {
            // Un agent voit ses propres opérations et celles qui lui sont assignées
            return [
                'own_operations' => true,
                'assigned_operations' => true,
                'user_id' => $user->id,
                'service_email' => $user->email
            ];
        }

        if ($userRole === 'manager') {
            // Un manager voit les opérations de son service
            return [
                'service_operations' => true,
                'service_id' => $user->service_id
            ];
        }

        // Par défaut, voir seulement ses propres opérations
        return [
            'own_operations' => true,
            'user_id' => $user->id
        ];
    }
}
