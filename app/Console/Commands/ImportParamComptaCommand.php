<?php

namespace App\Console\Commands;

use App\Services\ParamComptaImportService;
use Illuminate\Console\Command;

class ImportParamComptaCommand extends Command
{
    protected $signature = 'compta:import-param
        {file? : Chemin du fichier PARAM COMPTA}
        {--dry-run : Analyse le fichier puis annule les ecritures en base}';

    protected $description = 'Importe le fichier PARAM COMPTA (plan comptable, journaux, tiers et projets)';

    public function handle(ParamComptaImportService $service): int
    {
        $input = (string) ($this->argument('file') ?: 'imports/excel-a-traiter/PARAM COMPTA KENAM S-260404.xlsx');
        $filePath = $this->resolvePath($input);
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Fichier: ' . $filePath);
        $this->info($dryRun ? 'Mode: dry-run' : 'Mode: import effectif');

        try {
            $stats = $service->import($filePath, $dryRun);
        } catch (\Throwable $e) {
            $this->error('Echec import PARAM COMPTA: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->table(
            ['Mesure', 'Valeur'],
            [
                ['Comptes generaux crees', $stats['general_accounts_created']],
                ['Comptes generaux mis a jour', $stats['general_accounts_updated']],
                ['Comptes clients crees', $stats['client_accounts_created']],
                ['Comptes clients mis a jour', $stats['client_accounts_updated']],
                ['Comptes fournisseurs crees', $stats['supplier_accounts_created']],
                ['Comptes fournisseurs mis a jour', $stats['supplier_accounts_updated']],
                ['Journaux crees', $stats['journals_created']],
                ['Journaux mis a jour', $stats['journals_updated']],
                ['Projets crees', $stats['projects_created']],
                ['Projets mis a jour', $stats['projects_updated']],
                ['Liens clients mis a jour', $stats['client_links_updated']],
                ['Codes fournisseurs mis a jour', $stats['supplier_codes_updated']],
            ]
        );

        foreach ($stats['warnings'] as $warning) {
            $this->warn($warning);
        }

        $this->info($dryRun
            ? 'Analyse terminee. Aucune ecriture n\'a ete conservee.'
            : 'Import PARAM COMPTA termine avec succes.'
        );

        return self::SUCCESS;
    }

    private function resolvePath(string $path): string
    {
        if (str_starts_with($path, DIRECTORY_SEPARATOR) || preg_match('#^[A-Za-z]:[\\/]#', $path)) {
            return $path;
        }

        return base_path($path);
    }
}