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
        if (!Schema::hasTable('personnel_conges')) {
            Schema::create('personnel_conges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('personnel_id')->constrained('personnel')->onDelete('cascade');

                // Informations du congé
                $table->enum('type', ['ANNUEL', 'MALADIE', 'MATERNITE', 'PATERNITE', 'EXCEPTIONNEL']);
                $table->date('date_debut');
                $table->date('date_fin');
                $table->integer('nb_jours')->unsigned();
                $table->text('motif')->nullable();

                // Workflow de validation
                $table->enum('statut', ['EN_ATTENTE', 'VALIDE', 'REFUSE', 'ANNULE'])->default('EN_ATTENTE');
                $table->foreignId('demande_par')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('date_demande')->default(now());
                $table->timestamp('date_decision')->nullable();
                $table->foreignId('decision_par')->nullable()->constrained('users')->onDelete('set null');
                $table->text('motif_decision')->nullable();

                // Annulation
                $table->timestamp('date_annulation')->nullable();
                $table->foreignId('annule_par')->nullable()->constrained('users')->onDelete('set null');

                $table->timestamps();

                // Index pour optimisation
                $table->index(['personnel_id', 'statut']);
                $table->index(['personnel_id', 'date_debut']);
                $table->index(['statut', 'date_demande']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_conges');
    }
};
