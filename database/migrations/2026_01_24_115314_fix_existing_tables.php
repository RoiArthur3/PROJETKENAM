<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Marquer toutes les migrations problématiques comme déjà exécutées
        $migrations = [
            '2025_11_28_095632_add_service_id_to_users_table',
            '2026_01_12_181501_create_historique_commandes_table',
            // Ajoutez ici d'autres migrations problématiques si nécessaire
        ];

        foreach ($migrations as $migration) {
            if (!DB::table('migrations')->where('migration', $migration)->exists()) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => DB::table('migrations')->max('batch') + 1
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire en cas de rollback pour éviter de casser la base de données
    }
};
