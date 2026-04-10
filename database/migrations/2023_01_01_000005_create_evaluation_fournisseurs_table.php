<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationFournisseursTable extends Migration
{
    public function up()
    {
        Schema::create('evaluation_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 50);
            
            // Références
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('cascade');
            $table->foreignId('evaluateur_id')->constrained('users')->onDelete('cascade');
            
            // Période d'évaluation
            $table->date('date_evaluation');
            $table->date('periode_debut');
            $table->date('periode_fin');
            
            // Notes par critère
            $table->decimal('note_globale', 5, 2)->nullable();
            $table->decimal('note_qualite', 5, 2)->nullable();
            $table->decimal('note_prix', 5, 2)->nullable();
            $table->decimal('note_delai', 5, 2)->nullable();
            $table->decimal('note_service', 5, 2)->nullable();
            $table->decimal('note_reactivite', 5, 2)->nullable();
            
            // Note globale pondérée
            $table->decimal('note_globale_ponderee', 5, 2)->nullable();
            
            // Classement
            $table->string('classement', 50)->nullable()->comment('Or, Argent, Bronze, etc.');
            
            // Commentaires
            $table->text('commentaires')->nullable();
            $table->json('points_forts')->nullable();
            $table->json('points_faibles')->nullable();
            $table->json('preconisations')->nullable();
            
            // Critères détaillés (stockés en JSON)
            $table->json('criteres_qualite')->nullable();
            $table->json('criteres_prix')->nullable();
            $table->json('criteres_delai')->nullable();
            $table->json('criteres_service')->nullable();
            $table->json('criteres_reactivite')->nullable();
            
            // Statut
            $table->enum('statut', ['brouillon', 'en_cours', 'termine', 'annule'])->default('brouillon');
            
            // Validation
            $table->boolean('est_valide')->default(false);
            $table->dateTime('date_validation')->nullable();
            $table->foreignId('validateur_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Métadonnées
            $table->json('metadata')->nullable();
            
            // Relations
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index('reference');
            $table->index('fournisseur_id');
            $table->index('evaluateur_id');
            $table->index('date_evaluation');
            $table->index('statut');
        });
        
        // Table de liaison entre évaluations et commandes
        Schema::create('evaluation_commande', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluation_fournisseurs')->onDelete('cascade');
            $table->foreignId('commande_id')->constrained('commande_fournisseurs')->onDelete('cascade');
            $table->timestamps();
            
            // Index
            $table->unique(['evaluation_id', 'commande_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_commande');
        Schema::dropIfExists('evaluation_fournisseurs');
    }
}
