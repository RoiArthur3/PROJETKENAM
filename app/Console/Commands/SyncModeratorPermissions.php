<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\UserPermissionService;
use Illuminate\Support\Facades\DB;

class SyncModeratorPermissions extends Command
{
    protected $signature = 'permissions:sync-moderators';
    protected $description = 'Synchroniser les permissions des modérateurs';

    public function handle()
    {
        $this->info('Synchronisation des permissions des modérateurs...');
        
        // Créer les permissions manquantes
        $permissions = [
            'comptabilite.access',
            'tresorerie.access',
            'operations.access',
            'validations.access',
            'requetes.access',
            'tracking.access',
            'suivi.access',
            'magasin.access',
            'entrepots.access',
            'materiel.access',
            'parc.access',
            'rh.access',
            'fournisseurs.access',
            'invoicing.access',
            'facturation.access',
            'prospection.access',
            'ateliers.access',
            'checking.access',
        ];

        foreach ($permissions as $permissionName) {
            $exists = DB::table('permissions')
                ->where('name', $permissionName)
                ->exists();
            
            if (!$exists) {
                DB::table('permissions')->insert([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->info("✅ Créé: {$permissionName}");
            }
        }

        // Synchroniser les modérateurs
        $moderators = User::where(function($query) {
            $query->where('role', 'moderator')
                  ->orWhere('role', 'moderateur');
        })->get();

        foreach ($moderators as $moderator) {
            $modules = is_array($moderator->modules) ? $moderator->modules : [];
            $submodules = is_array($moderator->submodules) ? $moderator->submodules : [];
            
            UserPermissionService::updateUserPermissions($moderator, $modules, $submodules);
            $this->info("✅ Synchronisé: {$moderator->name}");
        }

        $this->info('🎉 Synchronisation terminée !');
        return 0;
    }
}
