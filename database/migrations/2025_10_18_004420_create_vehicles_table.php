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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('immatriculation')->unique(); // Plaque d'immatriculation
            $table->string('modele'); // Modèle du véhicule
            $table->string('marque'); // Marque
            $table->year('annee'); // Année de fabrication
            $table->enum('type', ['voiture', 'camion', 'moto', 'utilitaire', 'autre'])->default('voiture');
            $table->enum('etat', ['neuf', 'bon', 'moyen', 'mauvais'])->default('bon');
            $table->boolean('disponibilite')->default(true); // Disponible ou non
            $table->integer('kilometrage')->default(0); // Kilométrage actuel
            $table->date('date_achat')->nullable();
            $table->decimal('prix_achat', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('service_assigne')->nullable(); // Service assigné
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
