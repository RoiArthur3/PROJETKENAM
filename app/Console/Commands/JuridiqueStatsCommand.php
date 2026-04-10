<?php

namespace App\Console\Commands;

use App\Services\JuridiqueService;
use Illuminate\Console\Command;

class JuridiqueStatsCommand extends Command
{
    protected $signature = 'juridique:stats {--export= : Exporter les statistiques}';
    protected $description = 'Afficher les statistiques d\'interaction du module juridique';

    protected $juridiqueService;

    public function __construct(JuridiqueService $juridiqueService)
    {
        parent::__construct();
        $this->juridiqueService = $juridiqueService;
    }

    public function handle()
    {
        $this->info('Statistiques du module juridique:');
        
        $stats = $this->juridiqueService->getStatistiquesInteractions();

        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Contrats liés aux financements', $stats['contrats_lies_financements']],
                ['Utilisateurs actifs juridique', $stats['utilisateurs_actifs_juridique']],
                ['Taux de conversion financement', number_format($stats['taux_conversion_financement'], 2) . '%'],
            ]
        );

        if ($this->option('export')) {
            $format = $this->option('export');
            $this->exportStats($stats, $format);
        }

        return 0;
    }

    private function exportStats($stats, $format)
    {
        $filename = 'juridique_stats_' . now()->format('Y-m-d_H-i-s');
        
        switch ($format) {
            case 'json':
                $filename .= '.json';
                file_put_contents(storage_path("app/{$filename}"), json_encode($stats, JSON_PRETTY_PRINT));
                $this->info("Statistiques exportées dans: {$filename}");
                break;
            
            case 'csv':
                $filename .= '.csv';
                $handle = fopen(storage_path("app/{$filename}"), 'w');
                fputcsv($handle, ['Métrique', 'Valeur']);
                foreach ($stats as $key => $value) {
                    fputcsv($handle, [$key, is_array($value) ? json_encode($value) : $value]);
                }
                fclose($handle);
                $this->info("Statistiques exportées dans: {$filename}");
                break;
            
            default:
                $this->error("Format d'export non supporté: {$format}");
        }
    }
}
