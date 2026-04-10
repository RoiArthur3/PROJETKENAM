<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;
use App\Models\OperationalService;
use App\Models\EntrepriseSettings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        try {
            $services = Cache::remember('operational_services.active', now()->addMinutes(30), function () {
                if (!class_exists(OperationalService::class)) return collect();
                return OperationalService::query()
                    ->where('actif', true)
                    ->orderBy('ordre')
                    ->orderBy('nom')
                    ->get(['id','code','nom','ordre']);
            });
            View::share('operationalServices', $services);
        } catch (\Throwable $e) {
            // silently ignore during early migrations
        }

        try {
            $entrepriseSettings = Cache::remember('entreprise_settings.active', now()->addMinutes(30), function () {
                if (!class_exists(EntrepriseSettings::class)) return null;
                return EntrepriseSettings::getActive();
            });

            View::share('entrepriseSettings', $entrepriseSettings);
            View::share('entreprisePdf', $entrepriseSettings ? [
                'nom' => $entrepriseSettings->nom_entreprise,
                'sigle' => $entrepriseSettings->sigle,
                'adresse' => $entrepriseSettings->adresse,
                'telephone' => $entrepriseSettings->telephone,
                'email' => $entrepriseSettings->email_contact,
                'website' => $entrepriseSettings->site_web,
                'rccm' => $entrepriseSettings->rccm,
                'ifu' => $entrepriseSettings->ifu,
                'cnss' => $entrepriseSettings->cnss,
                'logo_storage_path' => $entrepriseSettings->logo_path ? public_path('storage/' . $entrepriseSettings->logo_path) : null,
            ] : null);
        } catch (\Throwable $e) {
            // silently ignore during early migrations
        }

        // Partage du nombre de notifications (validations en attente)
        View::composer('layouts.navigation', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                
                // Si admin/superadmin, on voit toutes les validations en cours
                if (in_array($user->role, ['admin', 'superadmin'])) {
                    $count = \DB::table('operation_service_validation')
                        ->where('statut', 'EN_COURS')
                        ->count();
                } else {
                    // Sinon, uniquement celles qui nous concernent
                    $count = \DB::table('operation_service_validation')
                        ->where('statut', 'EN_COURS')
                        ->where(function($query) use ($user) {
                            $query->where('service_operationnel_id', $user->service_id)
                                  ->orWhereIn('service_operationnel_id', function($sq) use ($user) {
                                      $sq->select('id')->from('services_operationnels')->where('email', $user->email);
                                  });
                        })
                        ->count();
                }
                
                $view->with('pendingValidationsCount', $count);
            }
        });
    }
}
