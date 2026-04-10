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
        // Vérifier si la table existe déjà pour éviter l'erreur en production
        if (Schema::hasTable('projets')) {
            return; // La table existe déjà, ne pas la recréer
        }

        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            $table->string('code_projet')->unique(); // Code unique du projet
            $table->string('nom_projet'); // Nom du projet
            $table->text('description')->nullable(); // Description détaillée
            $table->string('client_id')->nullable(); // Référence vers table clients
            $table->date('date_debut')->nullable();
            $table->date('date_fin_prevue')->nullable();
            $table->date('date_fin_reelle')->nullable();
            $table->decimal('budget_previsionnel', 15, 2)->default(0);
            $table->decimal('budget_consomme', 15, 2)->default(0);
            $table->enum('statut', ['planification', 'en_cours', 'en_pause', 'termine', 'annule'])->default('planification');
            $table->enum('priorite', ['basse', 'moyenne', 'haute', 'urgente'])->default('moyenne');
            $table->string('responsable')->nullable(); // Responsable du projet
            $table->string('chef_projet')->nullable();
            $table->decimal('taux_realisation', 5, 2)->default(0); // Pourcentage de réalisation
            $table->text('notes')->nullable();
            $table->string('created_by');
            $table->timestamps();

            $table->index('client_id');
            $table->index('statut');
            $table->index('priorite');
            $table->index(['date_debut', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};
