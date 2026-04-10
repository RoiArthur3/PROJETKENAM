<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Standardize Fournisseurs table - ensure est_actif boolean column exists
     */
    public function up(): void
    {
        if (Schema::hasTable('fournisseurs')) {
            // Vérifier si la colonne est_actif n'existe pas
            if (!Schema::hasColumn('fournisseurs', 'est_actif')) {
                Schema::table('fournisseurs', function (Blueprint $table) {
                    $table->boolean('est_actif')->default(true)->after('type');
                });

                // Si une colonne 'statut' existe avec une valeur 'actif', migrer vers est_actif
                if (Schema::hasColumn('fournisseurs', 'statut')) {
                    DB::statement("UPDATE fournisseurs SET est_actif = TRUE WHERE statut = 'actif'");
                    DB::statement("UPDATE fournisseurs SET est_actif = FALSE WHERE statut != 'actif'");
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('fournisseurs') && Schema::hasColumn('fournisseurs', 'est_actif')) {
            Schema::table('fournisseurs', function (Blueprint $table) {
                $table->dropColumn('est_actif');
            });
        }
    }
};
