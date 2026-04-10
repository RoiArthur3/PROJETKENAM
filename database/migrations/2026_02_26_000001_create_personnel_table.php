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
        Schema::create('personnel', function (Blueprint $table) {
            $table->id();

            // Informations personnelles de base
            $table->string('matricule', 20)->unique(); // Matricule interne
            $table->string('nom', 100);
            $table->string('prenoms', 150);
            $table->date('date_naissance');
            $table->string('lieu_naissance', 100);
            $table->string('nationalite', 50)->default('Ivoirienne');
            $table->enum('sexe', ['M', 'F']);
            $table->string('situation_matrimoniale', 30); // Célibataire, Marié(e), Divorcé(e), Veuf(ve)
            $table->integer('nb_enfants_charge')->default(0);
            $table->string('photo_profil')->nullable();

            // Coordonnées
            $table->string('telephone_principal', 20);
            $table->string('telephone_secondaire', 20)->nullable();
            $table->string('email_personnel', 100)->nullable();
            $table->text('adresse_residence');
            $table->string('ville', 50);
            $table->string('quartier', 50)->nullable();

            // Pièces d'identité (Côte d'Ivoire)
            $table->enum('type_piece', ['CNI', 'PASSEPORT', 'CARTE_SEJOUR', 'AUTRE']);
            $table->string('numero_piece', 50)->unique();
            $table->date('date_delivrance_piece');
            $table->date('expiration_piece')->nullable();
            $table->string('lieu_delivrance_piece', 100);

            // Informations professionnelles
            $table->string('poste', 100);
            $table->string('service', 100);
            $table->string('departement', 100)->nullable();
            $table->string('categorie', 50); // A, B, C, D, etc.
            $table->string('echelon', 20)->nullable();
            $table->string('indice', 20)->nullable();

            // Contrat de travail
            $table->enum('type_contrat', ['CDI', 'CDD', 'STAGE', 'INTERIM', 'CONSULTANT']);
            $table->date('date_embauche');
            $table->date('date_fin_contrat')->nullable(); // Pour CDD
            $table->integer('duree_essai_jours')->default(90);
            $table->date('fin_periode_essai')->nullable();
            $table->decimal('salaire_base', 12, 2);
            $table->string('devise', 3)->default('XOF'); // FCFA
            $table->enum('frequence_paiement', ['MENSUEL', 'HEBDOMADAIRE', 'QUINZOMADAIRE'])->default('MENSUEL');

            // CNPS (Caisse Nationale de Prévoyance Sociale - Côte d'Ivoire)
            $table->string('numero_cnps', 20)->unique()->nullable();
            $table->date('date_affiliation_cnps')->nullable();
            $table->enum('categorie_cnps', ['A', 'B', 'C', 'D', 'E', 'F', 'G'])->nullable();

            // Impôts et taxes (Côte d'Ivoire)
            $table->string('numero_contribuable', 30)->unique()->nullable();
            $table->integer('nb_parts_fiscales')->default(1);
            $table->enum('situation_fiscale', ['IMPOSABLE', 'NON_IMPOSABLE', 'EXONERE'])->default('IMPOSABLE');

            // Banque
            $table->string('banque', 100)->nullable();
            $table->string('agence_bancaire', 100)->nullable();
            $table->string('numero_compte_bancaire', 30)->nullable();
            $table->string('rib', 30)->nullable();

            // Contact d'urgence
            $table->string('nom_urgence', 100);
            $table->string('telephone_urgence', 20);
            $table->string('lien_parente', 50);
            $table->text('adresse_urgence')->nullable();

            // Santé et sécurité
            $table->enum('groupe_sanguin', ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])->nullable();
            $table->text('allergies')->nullable();
            $table->text('maladies_chroniques')->nullable();
            $table->string('medecin_traitant', 100)->nullable();
            $table->string('telephone_medecin', 20)->nullable();

            // Documents RH
            $table->string('cv_path')->nullable();
            $table->string('lettre_motivation_path')->nullable();
            $table->string('contrat_path')->nullable();
            $table->string('casier_judiciaire_path')->nullable();
            $table->string('certificat_medical_path')->nullable();
            $table->string('diplomes_path')->nullable();
            $table->string('attestations_path')->nullable();

            // Suivi administratif
            $table->enum('statut', ['ACTIF', 'CONGE', 'MALADIE', 'SUSPENDU', 'DEMISSION', 'LICENCIE', 'RETRAITE'])->default('ACTIF');
            $table->date('date_depart')->nullable();
            $table->text('motif_depart')->nullable();
            $table->text('observations')->nullable();

            // Liaison avec le compte utilisateur (si applicable)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Audit
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes(); // Pour les soft deletes

            // Index
            $table->index(['nom', 'prenoms']);
            $table->index('matricule');
            $table->index('service');
            $table->index('poste');
            $table->index('statut');
            $table->index('date_embauche');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel');
    }
};
