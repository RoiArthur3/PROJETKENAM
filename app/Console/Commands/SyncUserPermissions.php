<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\UserRolePermissionService;

class SyncUserPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:sync-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise les permissions de tous les utilisateurs en fonction de leur rôle';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Démarrage de la synchronisation des permissions pour tous les utilisateurs...');

        $progressBar = $this->output->createProgressBar(User::count());
        $progressBar->start();

        User::chunk(100, function ($users) use ($progressBar) {
            foreach ($users as $user) {
                UserRolePermissionService::applyRolePermissions($user);
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->info('\nPermissions synchronisées avec succès pour tous les utilisateurs.');

        return 0;
    }
}
