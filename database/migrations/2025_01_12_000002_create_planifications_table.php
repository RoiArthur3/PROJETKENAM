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
        Schema::create('planifications', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 255);
            $table->text('description');
            $table->enum('type_planification', [
                'audit_interne',
                'audit_externe',
                'controle_qualite',
                'inspection_securite',
                'evaluation_risque',
                'revue_processus',
                'verification_conformite'
            ]);
            $table->foreignId('service_concerne_id')->constrained('services_operationnels');
            $table->date('date_planification');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->string('lieu', 255);
            $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente']);
            $table->enum('statut', ['planifie', 'en_cours', 'terminee', 'annulee', 'reportee']);
            $table->foreignId('createur_id')->constrained('users');
            $table->json('participants')->nullable();
            $table->json('documents')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('budget_estime', 15, 2)->nullable();
            $table->boolean('rapport_attendu')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Index pour optimisation
            $table->index(['service_concerne_id']);
            $table->index(['createur_id']);
            $table->index(['date_planification']);
            $table->index(['statut']);
            $table->index(['priorite']);
            $table->index(['type_planification']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planifications');
    }
};
