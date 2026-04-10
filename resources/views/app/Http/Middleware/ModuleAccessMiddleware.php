<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ModuleAccessMiddleware
{
    /**
     * Mapping des modules vers les colonnes de permissions
     */
    private array $modulePermissions = [
        'dashboard' => 'can_access_dashboard',
        'operations' => 'can_access_operations',
        'requetes' => 'can_access_operations', // Les requêtes (agents) font partie des opérations
        'tracking' => 'can_access_operations', // Le tracking fait partie des opérations
        'fleet' => 'can_access_fleet',
        'parc' => 'can_access_fleet', // Alias pour fleet
        'hr' => 'can_access_hr',
        'rh' => 'can_access_hr', // Alias pour hr
        'suppliers' => 'can_access_suppliers',
        'fournisseurs' => 'can_access_suppliers', // Alias pour suppliers (FR)
        'warehouse' => 'can_access_warehouse',
        'stock' => 'can_access_warehouse', // Alias pour warehouse
        'accounting' => 'can_access_accounting',
        'comptabilite' => 'can_access_accounting', // Alias pour accounting
        'invoicing' => 'can_access_invoicing',
        'facturation' => 'can_access_invoicing', // Alias pour invoicing
        'reporting' => 'can_access_reporting',
        'commercial' => 'can_access_commercial',
        'prospection' => 'can_access_prospection',
        'ateliers' => 'can_access_ateliers',
        'admin' => 'role', // Special case for admin routes
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Utiliser la méthode canAccessModule du modèle User pour une logique centralisée
        // Cette méthode gère correctement les règles:
        // - Superadmin: accès à tout
        // - Admin: accès à tout sauf dashboard
        // - Moderateur: operations + validations + modules cochés
        // - Agent: operations + validations uniquement
        if (!$user->canAccessModule($module)) {
            // Rediriger vers operations/dashboard au lieu d'afficher une erreur 403
            if ($module === 'dashboard') {
                return redirect('/operations/dashboard')
                    ->with('warning', 'Accès au Dashboard réservé au Super Administrateur');
            }
            
            abort(403, "Accès refusé au module {$module}");
        }

        return $next($request);
    }

    private function isSuperadmin($user): bool
    {
        try {
            if (method_exists($user, 'hasRole')) {
                return $user->hasRole('superadmin');
            }
            if (method_exists($user, 'getRoleNames')) {
                return in_array('superadmin', $user->getRoleNames()->toArray(), true);
            }
        } catch (\Throwable $e) {
        }

        return false;
    }

    /**
     * Get the appropriate dashboard route for a user based on their role/permissions
     */
    private function getUserDashboardRoute($user): ?string
    {
        // Redirection selon le rôle
        return match ($user->role) {
            'superadmin' => 'dashboard', // Le superadmin peut accéder au dashboard général
            'admin' => 'operations.dashboard', // Admin redirigé vers les opérations
            'moderator' => 'operations.dashboard', // Modérateur vers les opérations
            'agent', 'user' => 'operations.dashboard', // Agent/utilisateur vers les opérations
            default => null,
        };
    }

    /**
     * Get the first accessible module route for a user
     */
    private function getFirstAccessibleModuleRoute($user): ?string
    {
        $moduleRoutes = [
            'operations' => 'operations.dashboard',
            'fleet' => 'fleet.dashboard',
            'hr' => 'hr.dashboard',
            'warehouse' => 'warehouse.dashboard',
            'accounting' => 'accounting.dashboard',
            'commercial' => 'commercial.dashboard',
            'suppliers' => 'suppliers.dashboard',
            'reporting' => 'reporting.dashboard',
        ];

        foreach ($moduleRoutes as $module => $route) {
            $permissionField = $this->modulePermissions[$module] ?? null;
            if ($permissionField && $user->$permissionField) {
                return $route;
            }
        }

        // Si aucun module spécifique n'est accessible, rediriger vers les opérations (module de base)
        // ou créer un dashboard général pour les utilisateurs sans permissions spécifiques
        return 'operations.dashboard';
    }

    /**
     * Get display name for module
     */
    private function getModuleDisplayName(string $module): string
    {
        $displayNames = [
            'dashboard' => 'Tableau de bord',
            'operations' => 'Opérations',
            'tracking' => 'Suivi des missions',
            'fleet' => 'Parc auto',
            'parc' => 'Parc auto',
            'hr' => 'Ressources Humaines',
            'rh' => 'Ressources Humaines',
            'suppliers' => 'Fournisseurs',
            'warehouse' => 'Entrepôt',
            'stock' => 'Stock',
            'accounting' => 'Comptabilité',
            'comptabilite' => 'Comptabilité',
            'invoicing' => 'Facturation',
            'facturation' => 'Facturation',
            'reporting' => 'Rapports',
            'commercial' => 'Commercial',
            'prospection' => 'Prospection',
            'ateliers' => 'Ateliers',
        ];

        return $displayNames[$module] ?? $module;
    }
}
