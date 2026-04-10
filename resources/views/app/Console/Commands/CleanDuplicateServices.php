<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanDuplicateServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-duplicate-services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoyer les services en double dans la base de données';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Nettoyage des services en double ===');
        $this->line('');

        // Récupérer tous les services groupés par nom
        $duplicates = \App\Models\ServiceOperationnel::all()
            ->groupBy('nom')
            ->filter(function ($services) {
                return $services->count() > 1;
            });

        if ($duplicates->isEmpty()) {
            $this->info('✅ Aucun doublon trouvé dans la base de données.');
            return 0;
        }

        $this->info('Doublons trouvés :');
        $totalDuplicates = 0;

        foreach ($duplicates as $nom => $services) {
            $this->line("- {$nom} : {$services->count()} occurrences");

            // Garder le premier service et supprimer les autres
            $toDelete = $services->slice(1);
            $totalDuplicates += $toDelete->count();

            foreach ($toDelete as $service) {
                $this->line("  → Suppression du service ID: {$service->id}");
                $service->delete();
            }
        }

        $this->line('');
        $this->info("✅ Nettoyage terminé ! {$totalDuplicates} doublon(s) supprimé(s).");

        return 0;
    }
}
