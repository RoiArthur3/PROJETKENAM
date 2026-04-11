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
        Schema::table('vehicules', function (Blueprint $table) {
            // Ajouter la colonne prix_location si elle n'existe pas
            if (!Schema::hasColumn('vehicules', 'prix_location')) {
                $table->decimal('prix_location', 10, 2)->nullable()->after('modele')->comment('Prix de location par heure/jour');
            }

            // Ajouter la colonne prix_achat si elle n'existe pas
            if (!Schema::hasColumn('vehicules', 'prix_achat')) {
                $table->decimal('prix_achat', 10, 2)->nullable()->after('prix_location')->comment('Coût d\'achat par heure/jour');
            }

            // Ajouter la colonne statut si elle n'existe pas
            if (!Schema::hasColumn('vehicules', 'statut')) {
                $table->string('statut')->default('actif')->after('disponible')->comment('Statut du véhicule');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicules', function (Blueprint $table) {
            $table->dropColumn(['prix_location', 'prix_achat', 'statut']);
        });
    }
};
