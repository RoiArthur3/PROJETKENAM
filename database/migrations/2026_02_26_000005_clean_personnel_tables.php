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
        // Supprimer les tables existantes avec les mauvaises foreign keys
        Schema::dropIfExists('personnel_documents');
        Schema::dropIfExists('personnel_paies');
        Schema::dropIfExists('personnel_conges');
        Schema::dropIfExists('personnel');
        
        // Recréer la table personnel (si elle n'existe pas)
        if (!Schema::hasTable('personnel')) {
            Schema::create('personnel', function (Blueprint $table) {
                $table->id();
                $table->string('matricule', 20)->unique();
                $table->string('nom', 100);
                $table->string('prenoms', 150);
                $table->date('date_naissance');
                $table->string('lieu_naissance', 100);
                $table->string('nationalite', 50);
                $table->enum('sexe', ['M', 'F']);
                $table->string('situation_matrimoniale', 50);
                $table->integer('nb_enfants_charge')->default(0);
                
                // Coordonnées
                $table->string('telephone_principal');
                $table->string('telephone_secondaire')->nullable();
                $table->string('email_personnel')->nullable();
                $table->string('ville');
                $table->text('adresse_residence');
                $table->string('quartier')->nullable();
                
                // Pièce d'identité
                $table->string('type_piece');
                $table->string('numero_piece');
                $table->date('date_delivrance_piece');
                $table->date('expiration_piece')->nullable();
                $table->string('lieu_delivrance_piece');
                
                // Informations professionnelles
                $table->string('poste');
                $table->string('service');
                $table->string('departement')->nullable();
                $table->string('categorie', 10);
                $table->string('echelon')->nullable();
                $table->string('indice')->nullable();
                
                // Contrat
                $table->enum('type_contrat', ['CDI', 'CDD', 'STAGE', 'INTERIM', 'CONSULTANT']);
                $table->date('date_embauche');
                $table->date('date_fin_contrat')->nullable();
                $table->integer('duree_essai_jours')->default(90);
                    $table->date('fin_periode_essai')->nullable();
                    $table->decimal('salaire_base', 10, 2);
                $table->string('devise', 3)->default('XOF');
                $table->enum('frequence_paiement', ['MENSUEL', 'HEBDOMADAIRE', 'QUINZOMADAIRE'])->default('MENSUEL');
                
                // CNPS & Fiscalité
                $table->string('numero_cnps')->nullable();
                $table->date('date_affiliation_cnps')->nullable();
                $table->string('categorie_cnps', 10)->nullable();
                $table->string('numero_contribuable')->nullable();
                $table->integer('nb_parts_fiscales')->default(1);
                $table->enum('situation_fiscale', ['IMPOSABLE', 'NON_IMPOSABLE', 'EXONERE'])->default('IMPOSABLE');
                
                // Banque
                $table->string('banque')->nullable();
                $table->string('agence_bancaire')->nullable();
                $table->string('numero_compte')->nullable();
                $table->string('rib')->nullable();
                
                // Contact d'urgence
                $table->string('nom_urgence')->nullable();
                $table->string('telephone_urgence')->nullable();
                $table->string('lien_urgence')->nullable();
                
                // Santé
                $table->string('groupe_sanguin')->nullable();
                $table->text('allergies')->nullable();
                $table->text('maladies_chroniques')->nullable();
                $table->string('medecin_traitant')->nullable();
                $table->string('telephone_medecin')->nullable();
                
                // Statut et suivi
                $table->enum('statut', ['ACTIF', 'INACTIF', 'EN_ESSAI', 'RESILIE'])->default('ACTIF');
                $table->date('date_fin_contrat_reel')->nullable();
                $table->text('motif_depart')->nullable();
                
                // Liaison avec compte utilisateur (optionnel)
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                
                // Audit
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                
                $table->timestamps();
                
                // Index
                $table->index(['matricule']);
                $table->index(['nom', 'prenoms']);
                $table->index(['service']);
                $table->index(['statut']);
                $table->index(['date_embauche']);
                $table->index(['type_contrat']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_documents');
        Schema::dropIfExists('personnel_paies');
        Schema::dropIfExists('personnel_conges');
        Schema::dropIfExists('personnel');
    }
};
