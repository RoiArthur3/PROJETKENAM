<?php

namespace App\Jobs;

use App\Services\JuridiqueService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class JuridiqueAlertesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jours;
    protected $type;

    public function __construct($jours = 30, $type = 'all')
    {
        $this->jours = $jours;
        $this->type = $type;
    }

    public function handle(JuridiqueService $juridiqueService)
    {
        try {
            $totalAlertes = 0;

            if ($this->type === 'all' || $this->type === 'contrats') {
                $contrats = $juridiqueService->verifierContratsExpirantBientot($this->jours);
                $totalAlertes += $contrats;
                Log::info("Job alertes contrats: {$contrats} contrats vérifiés");
            }

            if ($this->type === 'all' || $this->type === 'documents') {
                $documents = $juridiqueService->verifierDocumentsExpirantBientot($this->jours);
                $totalAlertes += $documents;
                Log::info("Job alertes documents: {$documents} documents vérifiés");
            }

            if ($this->type === 'all' || $this->type === 'echeances') {
                $echeances = $juridiqueService->verifierEcheancesEnRetard();
                $totalAlertes += $echeances;
                Log::info("Job alertes échéances: {$echeances} échéances vérifiées");
            }

            Log::info("Job juridique alertes terminé: {$totalAlertes} alertes générées", [
                'jours' => $this->jours,
                'type' => $this->type,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur dans le job juridique alertes", [
                'error' => $e->getMessage(),
                'jours' => $this->jours,
                'type' => $this->type,
            ]);
            throw $e;
        }
    }
}
