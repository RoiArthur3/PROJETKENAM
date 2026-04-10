<?php

namespace App\Providers;

use App\Models\Expense;
use App\Policies\ExpensePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Expense::class => ExpensePolicy::class,
        \App\Models\Caisse::class => \App\Policies\CaissePolicy::class,
        \App\Models\ApprovisionnementCaisse::class => \App\Policies\ApprovisionnementCaissePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Définition de la porte pour les administrateurs
        Gate::define('isAdmin', function ($user) {
            return $user->role === 'admin';
        });

        // Super Administrateur & Admin: accès à tout (bypass des permissions)
        // Modérateur: accès aux sous-modules si le module parent est coché
        Gate::before(function ($user, $ability, $arguments = []) {
            if (!method_exists($user, 'hasRole')) {
                return null;
            }

            // Superadmin et Admin : accès total
            if ($user->hasRole('superadmin') || $user->hasRole('super-admin') || $user->hasRole('admin')) {
                return true;
            }

            // Modérateur : vérifier le module parent du modèle concerné
            if ($user->hasRole('moderator') || $user->hasRole('moderateur')) {
                $modelClass = null;
                if (!empty($arguments)) {
                    $arg = $arguments[0];
                    $modelClass = is_string($arg) ? $arg : get_class($arg);
                }

                if ($modelClass) {
                    $modelToModule = [
                        'App\\Models\\Caisse' => 'tresorerie',
                        'App\\Models\\ApprovisionnementCaisse' => 'tresorerie',
                        'App\\Models\\DepenseCaisse' => 'tresorerie',
                        'App\\Models\\Virement' => 'tresorerie',
                        'App\\Models\\CompteBancaire' => 'tresorerie',
                        'App\\Models\\Banque' => 'tresorerie',
                        'App\\Models\\Expense' => 'tresorerie',
                        'App\\Models\\Vehicule' => 'materiel',
                        'App\\Models\\Vehicle' => 'materiel',
                        'App\\Models\\Maintenance' => 'materiel',
                        'App\\Models\\Employe' => 'rh',
                        'App\\Models\\Employee' => 'rh',
                        'App\\Models\\Fournisseur' => 'fournisseurs',
                        'App\\Models\\Client' => 'commercial',
                        'App\\Models\\Contrat' => 'commercial',
                        'App\\Models\\Devis' => 'commercial',
                        'App\\Models\\Facture' => 'commercial',
                        'App\\Models\\Invoice' => 'commercial',
                    ];

                    $module = $modelToModule[$modelClass] ?? null;
                    if ($module && $user->canAccessModule($module)) {
                        return true;
                    }
                }
            }

            return null;
        });
    }
}
