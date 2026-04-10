<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Standardize Personnel table - ensure statut ENUM has all required values
     */
    public function up(): void
    {
        if (Schema::hasTable('personnel')) {
            // Vérifier si la colonne date_depart existe, sinon la créer
            if (!Schema::hasColumn('personnel', 'date_depart')) {
                Schema::table('personnel', function (Blueprint $table) {
                    $table->date('date_depart')->nullable()->after('fin_periode_essai');
                });
            }

            // Mettre à jour le statut ENUM pour inclure tous les états de résilition
            DB::statement("
                ALTER TABLE personnel 
                MODIFY COLUMN statut ENUM(
                    'ACTIF',
                    'EN_ESSAI',
                    'DEMISSION',
                    'LICENCIE',
                    'RETRAITE',
                    'SUSPENDU',
                    'CONGE'
                ) DEFAULT 'ACTIF'
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('personnel')) {
            // Restaurer l'ancien ENUM
            DB::statement("
                ALTER TABLE personnel 
                MODIFY COLUMN statut ENUM(
                    'ACTIF',
                    'EN_ESSAI',
                    'RESILIE'
                ) DEFAULT 'ACTIF'
            ");

            if (Schema::hasColumn('personnel', 'date_depart')) {
                Schema::table('personnel', function (Blueprint $table) {
                    $table->dropColumn('date_depart');
                });
            }
        }
    }
};
