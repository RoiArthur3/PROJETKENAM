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
        Schema::create('personnel_paies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personnel_id')->constrained('personnel')->onDelete('cascade');

            // Période et statut
            $table->date('periode'); // Premier jour du mois de paie
            $table->enum('statut', ['EN_ATTENTE', 'VALIDE', 'PAYE'])->default('EN_ATTENTE');
            $table->date('date_paiement')->nullable();

            // Éléments de rémunération
            $table->decimal('salaire_base', 10, 2);
            $table->decimal('primes', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('salaire_net', 10, 2);

            // Observations et suivi
            $table->text('observations')->nullable();
            $table->foreignId('cree_par')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('paye_par')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Index pour optimisation
            $table->unique(['personnel_id', 'periode']); // Une seule paie par personnel par mois
            $table->index(['personnel_id', 'statut']);
            $table->index(['statut', 'periode']);
            $table->index(['periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_paies');
    }
};
