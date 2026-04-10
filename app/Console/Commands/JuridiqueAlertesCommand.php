<?php

namespace App\Console\Commands;

use App\Services\JuridiqueService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class JuridiqueAlertesCommand extends Command
{
    protected $signature = 'juridique:alertes {--jours=30 : Jours avant expiration} {--type=all : Type d\'alerte}';
    protected $description = 'Vérifier et envoyer les alertes juridiques';

    protected $juridiqueService;

    public function __construct(JuridiqueService $juridiqueService)
    {
        parent::__construct();
        $this->juridiqueService = $juridiqueService;
    }

    public function handle()
    {
        $this->info('Début de la vérification des alertes juridiques...');
        
        $jours = $this->option('jours');
        $type = $this->option('type');

        $totalAlertes = 0;

        if ($type === 'all' || $type === 'contrats') {
            $contrats = $this->juridiqueService->verifierContratsExpirantBientot($jours);
            $this->info("✓ {$contrats} contrats expirant dans {$jours} jours");
            $totalAlertes += $contrats;
        }

        if ($type === 'all' || $type === 'documents') {
            $documents = $this->juridiqueService->verifierDocumentsExpirantBientot($jours);
            $this->info("✓ {$documents} documents expirant dans {$jours} jours");
            $totalAlertes += $documents;
        }

        if ($type === 'all' || $type === 'echeances') {
            $echeances = $this->juridiqueService->verifierEcheancesEnRetard();
            $this->info("✓ {$echeances} échéances en retard");
            $totalAlertes += $echeances;
        }

        $this->info("Total des alertes générées : {$totalAlertes}");
        
        Log::info('Commande juridique:alertes exécutée', [
            'jours' => $jours,
            'type' => $type,
            'total_alertes' => $totalAlertes,
        ]);

        return $totalAlertes > 0 ? 1 : 0;
    }
}
