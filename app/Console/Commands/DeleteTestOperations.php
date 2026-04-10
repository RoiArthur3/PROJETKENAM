<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Operation;

class DeleteTestOperations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'operations:delete-test {--force : Forcer la suppression sans confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprimer toutes les opérations de test';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== Recherche d\'opérations de test ===');

        // Rechercher les opérations avec "test" dans le titre
        $operations = Operation::where('titre', 'LIKE', '%test%')
            ->orWhere('titre', 'LIKE', '%Test%')
            ->orWhere('titre', 'LIKE', '%TEST%')
            ->get(['id', 'titre', 'statut_courant', 'created_at']);

        if ($operations->isEmpty()) {
            $this->info('❌ Aucune opération trouvée contenant "test"');
            return 0;
        }

        $this->info('✅ Opération(s) trouvée(s) :');
        foreach ($operations as $operation) {
            $this->line("   ID: {$operation->id}");
            $this->line("   Titre: '{$operation->titre}'");
            $this->line("   Statut: {$operation->statut_courant}");
            $this->line("   Créée le: {$operation->created_at}");
            $this->line('   -------------------');
        }

        $count = $operations->count();
        
        if (!$this->option('force')) {
            if ($this->confirm("Voulez-vous supprimer ces {$count} opération(s) ?")) {
                $this->performDeletion();
            } else {
                $this->info('❌ Suppression annulée');
                return 0;
            }
        } else {
            $this->warn('⚠️  Suppression forcée activée');
            $this->performDeletion();
        }

        return 0;
    }

    private function performDeletion()
    {
        $deleted = Operation::where('titre', 'LIKE', '%test%')
            ->orWhere('titre', 'LIKE', '%Test%')
            ->orWhere('titre', 'LIKE', '%TEST%')
            ->delete();

        if ($deleted > 0) {
            $this->info("✅ {$deleted} opération(s) supprimée(s) avec succès");
        } else {
            $this->error('❌ Erreur lors de la suppression');
        }
    }
}
