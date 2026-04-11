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
        Schema::table('commande_fournisseurs', function (Blueprint $table) {
            // Ajouter le champ engin_id s'il n'existe pas
            if (!Schema::hasColumn('commande_fournisseurs', 'engin_id')) {
                $table->foreignId('engin_id')->nullable()->after('fournisseur_id')->constrained('vehicules')->nullOnDelete();
                $table->string('engin_statut')->default('disponible')->after('engin_id'); // disponible, en_panne, en_maintenance, loue
            }
            
            // Ajouter l'index pour optimiser les requêtes
            $table->index(['engin_id', 'engin_statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commande_fournisseurs', function (Blueprint $table) {
            if (Schema::hasColumn('commande_fournisseurs', 'engin_id')) {
                $table->dropForeign(['engin_id']);
                $table->dropColumn(['engin_id', 'engin_statut']);
            }
        });
    }
};
