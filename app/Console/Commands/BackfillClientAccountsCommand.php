<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Services\ClientAccountService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class BackfillClientAccountsCommand extends Command
{
    protected $signature = 'clients:backfill-accounts {--chunk=100 : Nombre de clients par lot}';

    protected $description = 'Cree et rattache un compte comptable auxiliaire pour chaque client sans compte';

    public function handle(ClientAccountService $clientAccountService): int
    {
        if (!Schema::hasTable('clients')) {
            $this->error('La table clients est introuvable.');
            return self::FAILURE;
        }

        if (!$clientAccountService->canManageClientAccounts()) {
            $this->error('Le lien comptable client n\'est pas disponible. Lancez d\'abord la migration.');
            return self::FAILURE;
        }

        $chunkSize = max(1, (int) $this->option('chunk'));
        $processed = 0;

        Client::query()
            ->whereNull('compte_comptable_id')
            ->orderBy('id')
            ->chunkById($chunkSize, function ($clients) use ($clientAccountService, &$processed) {
                foreach ($clients as $client) {
                    $clientAccountService->ensureForClient($client);
                    $processed++;
                }
            });

        $this->info("Backfill termine. {$processed} client(s) traites.");

        return self::SUCCESS;
    }
}