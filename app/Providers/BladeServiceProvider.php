<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
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
        // Directive pour vérifier l'accès à un module
        Blade::if('canAccessModule', function ($module) {
            return auth()->check() && auth()->user()->canAccessModule($module);
        });

        // Directive pour vérifier l'accès à un sous-menu
        Blade::if('canAccessSubmenu', function ($module, $submenu) {
            return auth()->check() && auth()->user()->canAccessSubmenu($module, $submenu);
        });

        // Directive pour vérifier si l'utilisateur a des permissions granulaires
        Blade::if('hasGranularPermissions', function () {
            return auth()->check() && auth()->user()->hasGranularPermissions();
        });

        // Directive pour vérifier si l'utilisateur est admin
        Blade::if('isAdmin', function () {
            return auth()->check() && auth()->user()->isAdmin();
        });
    }
}
