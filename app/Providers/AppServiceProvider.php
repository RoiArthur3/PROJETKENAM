<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            if (Auth::check()) {
                $user = Auth::user();
                $validationTable = 'operation_service_validation';
                
                // Si admin/superadmin, on voit toutes les validations en cours
                if (in_array($user->role, ['admin', 'superadmin'])) {
                    $count = DB::table($validationTable)
                        ->where('statut', 'EN_COURS')
                        ->count();
                } else {
                    // Sinon, uniquement celles qui concernent l'utilisateur, en tenant compte
                    // des schémas legacy (service_valideur) et récent (service_operationnel_id).
                    $baseQuery = DB::table($validationTable)
                        ->where('statut', 'EN_COURS');

                    if (Schema::hasColumn($validationTable, 'service_operationnel_id')) {
                        $serviceIds = [];
                        if (isset($user->service_id) && $user->service_id) {
                            $serviceIds[] = (int) $user->service_id;
                        }
                        if (!empty($user->email)) {
                            $serviceIds = array_merge(
                                $serviceIds,
                                DB::table('services_operationnels')
                                    ->where('email', $user->email)
                                    ->pluck('id')
                                    ->map(fn($id) => (int) $id)
                                    ->all()
                            );
                        }
                        $serviceIds = array_values(array_unique(array_filter($serviceIds)));

                        $count = empty($serviceIds)
                            ? 0
                            : $baseQuery->where(function ($query) use ($serviceIds) {
                                $query->whereNull('service_operationnel_id')
                                      ->orWhereIn('service_operationnel_id', $serviceIds);
                            })->count();
                    } elseif (Schema::hasColumn($validationTable, 'service_valideur')) {
                        $serviceValues = [];
                        if (!empty($user->email)) {
                            $service = DB::table('services_operationnels')
                                ->where('email', $user->email)
                                ->first(['id', 'code', 'nom', 'email']);
                            if ($service) {
                                $serviceValues = array_filter([
                                    (string) $service->id,
                                    (string) ($service->code ?? ''),
                                    (string) ($service->nom ?? ''),
                                    (string) ($service->email ?? ''),
                                ]);
                            }
                        }

                        $count = empty($serviceValues)
                            ? 0
                            : $baseQuery->where(function ($query) use ($serviceValues) {
                                $query->whereNull('service_valideur')
                                      ->orWhereIn('service_valideur', $serviceValues);
                            })->count();
                    } else {
                        $count = 0;
                    }
                }
                
                $view->with('pendingValidationsCount', $count);
            }
        });
    }
}
