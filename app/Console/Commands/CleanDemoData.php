<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-demo-data {--force : Force la suppression sans confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprime toutes les données de démonstration de la base de données';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  Cette commande va supprimer TOUTES les données de démonstration. Continuer ?')) {
                $this->info('Opération annulée.');
                return;
            }
        }

        $this->info('🧹 Début du nettoyage des données de démonstration...');

        // Compter les données avant suppression
        $counts = [
            'users' => \App\Models\User::where('role', '!=', 'admin')->count(),
            'vehicles' => \App\Models\Vehicle::count(),
            'operations' => \App\Models\Operation::count(),
            'expenses' => \App\Models\Expense::count(),
            'vehicle_assignments' => \App\Models\VehicleAssignment::count(),
            'vehicle_missions' => \App\Models\VehicleMission::count(),
            'validations' => \App\Models\Validation::count(),
            'validation_logs' => \App\Models\ValidationLog::count(),
        ];

        $this->info('📊 Données détectées :');
        foreach ($counts as $table => $count) {
            $this->line("  - {$table}: {$count} enregistrements");
        }

        // Commencer les suppressions
        $this->info('🗑️  Suppression en cours...');

        // Supprimer dans l'ordre pour respecter les contraintes de clés étrangères

        // 1. Logs de validation
        \App\Models\ValidationLog::truncate();
        $this->line('✓ Validation logs supprimés');

        // 2. Validations
        \App\Models\Validation::truncate();
        $this->line('✓ Validations supprimées');

        // 3. Affectations de véhicules
        \App\Models\VehicleAssignment::truncate();
        $this->line('✓ Affectations de véhicules supprimées');

        // 4. Missions de véhicules
        \App\Models\VehicleMission::truncate();
        $this->line('✓ Missions de véhicules supprimées');

        // 5. Dépenses
        \App\Models\Expense::truncate();
        $this->line('✓ Dépenses supprimées');

        // 6. Opérations
        \App\Models\Operation::truncate();
        $this->line('✓ Opérations supprimées');

        // 7. Véhicules
        \App\Models\Vehicle::truncate();
        $this->line('✓ Véhicules supprimés');

        // 8. Utilisateurs non-admin (attention : garder au moins un admin)
        $adminCount = \App\Models\User::where('role', 'admin')->count();
        if ($adminCount > 0) {
            \App\Models\User::where('role', '!=', 'admin')->delete();
            $this->line('✓ Utilisateurs non-admin supprimés');
        } else {
            $this->warn('⚠️  Aucun utilisateur admin trouvé, utilisateurs conservés');
        }

        // 9. Autres tables potentiellement avec données de test
        // (ajouter selon les besoins)

        $this->info('✅ Nettoyage terminé avec succès !');
        $this->info('📋 Résumé des suppressions :');

        foreach ($counts as $table => $count) {
            if ($count > 0) {
                $this->line("  - {$table}: {$count} enregistrements supprimés");
            }
        }

        // Vider le cache des vues compilées
        $this->call('view:clear');
        $this->call('cache:clear');

        $this->info('🎉 Base de données nettoyée et prête pour la production !');
        $this->info('💡 Pensez à créer de nouvelles données via l\'interface utilisateur.');

        return Command::SUCCESS;
    }
}
