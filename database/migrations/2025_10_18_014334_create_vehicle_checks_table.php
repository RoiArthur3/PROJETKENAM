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
        Schema::create('vehicle_checks', function (Blueprint $table) {
            $table->id();
            $table->string('numero_fiche')->unique(); // Numéro unique de la fiche
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade'); // Véhicule contrôlé
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Contrôleur
            $table->date('date_check');
            $table->enum('type_check', ['entree', 'sortie', 'maintenance', 'inspection'])->default('inspection');
            $table->json('items_checked')->nullable(); // Liste des éléments vérifiés (JSON)
            $table->enum('etat_general', ['bon', 'moyen', 'mauvais'])->default('bon');
            $table->text('observations')->nullable();
            $table->text('recommandations')->nullable();
            $table->boolean('valide')->default(false);
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_validation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_checks');
    }
};
