<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class RoleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Directives Blade pour contrôle d'accès basé sur les rôles
        // Utilise notre propre système de rôles (colonne 'role' dans users)
        
        Blade::directive('hasrole', function ($expression) {
            // Supporte 'admin' ou 'admin|manager|agent'
            return "<?php if(auth()->check() && in_array(auth()->user()->role, explode('|', {$expression}))): ?>";
        });

        Blade::directive('endhasrole', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('role', function ($expression) {
            return "<?php if(auth()->check() && in_array(auth()->user()->role, explode('|', {$expression}))): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });
    }
}
