<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_assignments', function (Blueprint $table) {
            $table->id();

            // Véhicule concerné
            $table->foreignId('vehicle_id')
                ->constrained('vehicules')
                ->cascadeOnDelete();

            // Chauffeur (utilisateur)
            $table->foreignId('driver_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Lien éventuel avec une opération/requête
            $table->foreignId('operation_id')
                ->nullable()
                ->constrained('operations')
                ->nullOnDelete();

            // Lien éventuel avec une requête (table créée plus tard dans l'historique)
            $table->unsignedBigInteger('requete_id')->nullable();

            // Informations de mission
            $table->dateTime('assigned_at');
            $table->dateTime('returned_at')->nullable();
            $table->string('mission')->nullable();
            $table->string('destination')->nullable();

            // Kilométrage
            $table->unsignedInteger('kilometrage_depart')->nullable();
            $table->unsignedInteger('kilometrage_retour')->nullable();

            // Statut de l'affectation
            $table->string('status')->default('assigned'); // assigned, in_progress, completed, cancelled

            // Notes et compte-rendu
            $table->text('notes')->nullable();
            $table->text('report')->nullable();

            $table->timestamps();

            $table->index(['vehicle_id', 'driver_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_assignments');
    }
};
