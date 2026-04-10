<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('permissions') || !Schema::hasTable('model_has_permissions') || !Schema::hasTable('users')) {
            return;
        }

        // Créer les permissions manquantes pour les modérateurs
        $permissionsToCreate = [
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

        foreach ($permissionsToCreate as $permissionName) {
            // Vérifier si la permission existe déjà
            $exists = \DB::table('permissions')
                ->where('name', $permissionName)
                ->exists();
            
            if (!$exists) {
                \DB::table('permissions')->insert([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Synchroniser les permissions des modérateurs existants
        $moderators = \DB::table('users')
            ->where(function($query) {
                $query->where('role', 'moderator')
                      ->orWhere('role', 'moderateur');
            })
            ->get();

        foreach ($moderators as $moderator) {
            $modules = json_decode($moderator->modules, true) ?: [];
            
            // Supprimer anciennes permissions
            \DB::table('model_has_permissions')
                ->where('model_id', $moderator->id)
                ->where('model_type', 'App\\Models\\User')
                ->delete();

            // Ajouter nouvelles permissions
            foreach ($modules as $module) {
                $permissionName = $module . '.access';
                
                $permissionId = \DB::table('permissions')
                    ->where('name', $permissionName)
                    ->value('id');
                
                if ($permissionId) {
                    \DB::table('model_has_permissions')->insert([
                        'permission_id' => $permissionId,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $moderator->id
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        // Supprimer les permissions créées
        $permissionsToDelete = [
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

        \DB::table('permissions')
            ->whereIn('name', $permissionsToDelete)
            ->delete();
    }
};
