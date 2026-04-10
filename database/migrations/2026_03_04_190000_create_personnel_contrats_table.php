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
        if (!Schema::hasTable('personnel_contrats')) {
            Schema::create('personnel_contrats', function (Blueprint $table) {
                $table->id();
                $table->foreignId('personnel_id')->constrained('personnel')->onDelete('cascade');
                $table->string('numero_contrat')->unique();
                $table->string('type_contrat'); // CDI, CDD, STAGE, etc.
                $table->date('date_debut');
                $table->date('date_fin')->nullable();
                $table->integer('duree_essai_jours')->nullable();
                $table->date('fin_periode_essai')->nullable();

                // Description du poste et tâches
                $table->string('poste');
                $table->text('description_taches')->nullable();
                $table->text('obligations_employeur')->nullable();
                $table->text('obligations_employe')->nullable();
                $table->text('conditions_travail')->nullable();

                // Rémunération
                $table->decimal('salaire_base', 12, 2)->nullable();
                $table->string('devise', 10)->default('XOF');
                $table->string('frequence_paiement', 20)->default('MENSUEL');
                $table->text('avantages')->nullable(); // primes, indemnités, etc.

                // Lieu de travail
                $table->string('lieu_travail')->nullable();
                $table->string('service_affectation')->nullable();
                $table->string('horaire_travail')->nullable();

                // Documents
                $table->string('fichier_contrat_path')->nullable();
                $table->string('fichier_annexe_path')->nullable();

                // Statut et validation
                $table->enum('statut', ['PROJET', 'SIGNE', 'ACTIF', 'SUSPENDU', 'TERMINE', 'RESILIE'])->default('PROJET');
                $table->date('date_signature')->nullable();
                $table->foreignId('signe_par_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('motif_resiliation')->nullable();
                $table->date('date_resiliation')->nullable();

                // Audit
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                // Index
                $table->index(['personnel_id', 'statut']);
                $table->index('date_debut');
                $table->index('date_fin');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_contrats');
    }
};
