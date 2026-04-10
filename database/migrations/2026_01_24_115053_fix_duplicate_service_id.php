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
        // Vérifier si la colonne existe déjà
        if (!Schema::hasColumn('users', 'service_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('service_id')->nullable()->after('phone');
            });
        }

        // Marquer la migration problématique comme déjà exécutée
        if (!DB::table('migrations')->where('migration', '2025_11_28_095632_add_service_id_to_users_table')->exists()) {
            DB::table('migrations')->insert([
                'migration' => '2025_11_28_095632_add_service_id_to_users_table',
                'batch' => DB::table('migrations')->max('batch') + 1
            ]);
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
