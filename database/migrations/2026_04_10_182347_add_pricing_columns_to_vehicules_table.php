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
            $table->decimal('prix_location', 10, 2)->nullable()->after('modele')->comment('Prix de location par heure/jour');
            $table->decimal('prix_achat', 10, 2)->nullable()->after('prix_location')->comment('Coût d\'achat par heure/jour');
            $table->string('statut')->default('actif')->after('disponible')->comment('Statut du véhicule');
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
