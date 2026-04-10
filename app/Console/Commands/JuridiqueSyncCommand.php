<?php

namespace App\Console\Commands;

use App\Services\JuridiqueService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class JuridiqueSyncCommand extends Command
{
    protected $signature = 'juridique:sync {--type=rh : Type de synchronisation}';
    protected $description = 'Synchroniser les données juridiques avec les autres modules';

    protected $juridiqueService;

    public function __construct(JuridiqueService $juridiqueService)
    {
        parent::__construct();
        $this->juridiqueService = $juridiqueService;
    }

    public function handle()
    {
        $this->info('Début de la synchronisation juridique...');
        
        $type = $this->option('type');

        switch ($type) {
            case 'rh':
                $result = $this->juridiqueService->synchroniserAvecRH();
                $this->info("✓ {$result} contrats synchronisés avec le module RH");
                break;
            
            default:
                $this->error('Type de synchronisation non reconnu: ' . $type);
                return 1;
        }

        Log::info('Commande juridique:sync exécutée', [
            'type' => $type,
            'result' => $result,
        ]);

        return 0;
    }
}
