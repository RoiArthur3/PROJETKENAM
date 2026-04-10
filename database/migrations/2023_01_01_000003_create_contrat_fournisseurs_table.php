<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContratFournisseursTable extends Migration
{
    public function up()
    {
        Schema::create('contrat_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 50);
            
            // Référence au fournisseur
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('cascade');
            
            // Informations générales
            $table->string('titre', 255);
            $table->text('description')->nullable();
            $table->string('type_contrat', 50);
            $table->string('numero_contrat', 100)->nullable();
            
            // Période de validité
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->boolean('renouvellement_auto')->default(false);
            $table->string('periode_renouvellement', 50)->nullable()->comment('Mensuel, Annuel, etc.');
            $table->integer('duree_preavis')->nullable()->comment('En jours');
            
            // Montants
            $table->decimal('montant_ht', 15, 2)->default(0);
            $table->decimal('tva', 5, 2)->default(20.00);
            $table->decimal('montant_ttc', 15, 2)->default(0);
            $table->decimal('montant_engage', 15, 2)->default(0);
            $table->decimal('montant_consomme', 15, 2)->default(0);
            $table->decimal('montant_restant', 15, 2)->default(0);
            
            // Conditions de paiement
            $table->string('mode_paiement', 50)->nullable();
            $table->string('conditions_paiement', 255)->nullable();
            $table->integer('delai_paiement')->nullable()->comment('En jours');
            
            // Fichier du contrat
            $table->string('fichier_contrat', 255)->nullable();
            $table->string('nom_fichier_original', 255)->nullable();
            
            // Statut
            $table->enum('statut', ['brouillon', 'en_attente', 'en_cours', 'termine', 'resilie', 'expire'])->default('brouillon');
            $table->date('date_signature')->nullable();
            $table->date('date_resiliation')->nullable();
            $table->text('motif_resiliation')->nullable();
            
            // Informations de suivi
            $table->text('notes')->nullable();
            $table->json('termes_speciaux')->nullable();
            
            // Responsable
            $table->foreignId('responsable_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Métadonnées
            $table->json('metadata')->nullable();
            
            // Relations
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index('reference');
            $table->index('fournisseur_id');
            $table->index('type_contrat');
            $table->index('date_debut');
            $table->index('date_fin');
            $table->index('statut');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contrat_fournisseurs');
    }
}
