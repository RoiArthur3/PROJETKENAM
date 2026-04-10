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
        // Supprimer l'entrée de la migration problématique de la table des migrations
        \DB::table('migrations')
            ->where('migration', '2025_11_27_120000_rename_permissions_column_on_users_table')
            ->delete();

        // Supprimer la colonne permissions si elle existe
        if (Schema::hasColumn('users', 'permissions')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('permissions');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette opération est irréversible car nous ne pouvons pas recréer la migration problématique
        // Si nécessaire, vous devrez restaurer à partir d'une sauvegarde
    }
};
