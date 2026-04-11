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
        // 1. Table des écritures comptables
        Schema::create('ecritures_comptables', function (Blueprint $table) {
            $table->id();
            $table->date('date_ecriture'); // Date de l'écriture
            $table->string('code_journal', 10); // Code du journal (AC, VT, BQ, CA, OD, etc.)
            $table->string('numero_piece', 50)->nullable(); // Numéro de pièce justificative
            $table->string('reference', 100)->nullable(); // Référence de l'opération
            $table->string('compte_debit', 20); // Compte débité
            $table->string('compte_credit', 20); // Compte crédité
            $table->decimal('debit', 15, 2)->default(0); // Montant débit
            $table->decimal('credit', 15, 2)->default(0); // Montant crédit
            $table->text('libelle'); // Libellé de l'écriture
            $table->string('type_document', 50)->nullable(); // Type de document (facture, paiement, etc.)
            $table->unsignedBigInteger('document_id')->nullable(); // ID du document lié
            $table->string('document_type')->nullable(); // Type de document lié (operation, facture, etc.)
            $table->unsignedBigInteger('user_id')->nullable(); // Utilisateur qui a créé l'écriture
            $table->boolean('lettrage')->default(false); // Lettrage effectué ou non
            $table->string('lettrage_reference')->nullable(); // Référence de lettrage
            $table->timestamps();
            
            // Index
            $table->index('date_ecriture');
            $table->index('code_journal');
            $table->index('compte_debit');
            $table->index('compte_credit');
            $table->index(['document_type', 'document_id']);
        });

        // 2. Table des comptes bancaires
        Schema::create('compte_bancaires', function (Blueprint $table) {
            $table->id();
            $table->string('nom_banque'); // Nom de la banque
            $table->string('numero_compte', 50); // Numéro de compte
            $table->string('iban', 50)->nullable(); // IBAN
            $table->string('swift', 20)->nullable(); // Code SWIFT
            $table->decimal('solde_initial', 15, 2)->default(0); // Solde initial
            $table->decimal('solde', 15, 2)->default(0); // Solde actuel
            $table->string('devise', 10)->default('XOF'); // Devise (FCFA)
            $table->string('type_compte', 20)->default('courant'); // Type de compte
            $table->boolean('actif')->default(true); // Compte actif
            $table->text('description')->nullable(); // Description
            $table->unsignedBigInteger('user_id')->nullable(); // Responsable
            $table->timestamps();
            
            // Index
            $table->unique('numero_compte');
            $table->index('actif');
        });

        // 3. Table des recettes
        Schema::create('recettes', function (Blueprint $table) {
            $table->id();
            $table->date('date_recette'); // Date de la recette
            $table->string('type_recette', 50); // Type (vente, service, etc.)
            $table->string('reference', 100)->nullable(); // Référence (facture, etc.)
            $table->decimal('montant', 15, 2); // Montant
            $table->string('mode_paiement', 50)->nullable(); // Mode de paiement
            $table->string('compte_comptable', 20)->nullable(); // Compte comptable
            $table->text('description')->nullable(); // Description
            $table->unsignedBigInteger('client_id')->nullable(); // Client
            $table->unsignedBigInteger('operation_id')->nullable(); // Opération liée
            $table->unsignedBigInteger('user_id')->nullable(); // Utilisateur
            $table->boolean('comptabilise')->default(false); // Comptabilisé ou non
            $table->timestamps();
            
            // Index
            $table->index('date_recette');
            $table->index('type_recette');
            $table->index(['operation_id']);
        });

        // 4. Table des paiements
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->date('date_paiement'); // Date du paiement
            $table->string('type_paiement', 50); // Type (fournisseur, salaire, etc.)
            $table->decimal('montant', 15, 2); // Montant
            $table->string('mode_paiement', 50); // Mode de paiement
            $table->string('reference', 100)->nullable(); // Référence
            $table->string('compte_comptable', 20)->nullable(); // Compte comptable
            $table->text('description')->nullable(); // Description
            $table->unsignedBigInteger('fournisseur_id')->nullable(); // Fournisseur
            $table->unsignedBigInteger('operation_id')->nullable(); // Opération liée
            $table->unsignedBigInteger('user_id')->nullable(); // Utilisateur
            $table->boolean('comptabilise')->default(false); // Comptabilisé ou non
            $table->timestamps();
            
            // Index
            $table->index('date_paiement');
            $table->index('type_paiement');
            $table->index(['operation_id']);
        });

        // 5. Insertion des comptes comptables standards pour KENAM Services
        DB::table('comptes_comptables')->insert([
            // Classe 1 - Comptes de capitaux
            ['numero_compte' => '101000', 'intitule' => 'Capital social', 'description' => 'Capital social de KENAM Services', 'type' => 'capitaux', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '106000', 'intitule' => 'Réserves', 'description' => 'Réserves et reports à nouveau', 'type' => 'capitaux', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '131000', 'intitule' => 'Résultat net', 'description' => 'Résultat net de l\'exercice', 'type' => 'capitaux', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            
            // Classe 2 - Comptes d\'immobilisations
            ['numero_compte' => '218100', 'intitule' => 'Matériel de transport', 'description' => 'Véhicules et engins de transport', 'type' => 'immobilisations', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '218200', 'intitule' => 'Matériel et outillage', 'description' => 'Équipements et matériel professionnel', 'type' => 'immobilisations', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '281810', 'intitule' => 'Amortissement matériel transport', 'description' => 'Amortissement cumulé du matériel de transport', 'type' => 'immobilisations', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            
            // Classe 3 - Comptes de stocks
            ['numero_compte' => '311000', 'intitule' => 'Matériel et fournitures', 'description' => 'Stock de matériel et fournitures', 'type' => 'stocks', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            
            // Classe 4 - Comptes de tiers
            ['numero_compte' => '401000', 'intitule' => 'Fournisseurs', 'description' => 'Dettes fournisseurs', 'type' => 'tiers', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '411000', 'intitule' => 'Clients', 'description' => 'Créances clients', 'type' => 'tiers', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '421000', 'intitule' => 'Personnel', 'description' => 'Dettes salariales', 'type' => 'tiers', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '431000', 'intitule' => 'Sécurité sociale', 'description' => 'Dettes sociales CNSS', 'type' => 'tiers', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '444000', 'intitule' => 'État', 'description' => 'Dettes fiscales', 'type' => 'tiers', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '521000', 'intitule' => 'Banques', 'description' => 'Comptes bancaires', 'type' => 'tiers', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '531000', 'intitule' => 'Caisse', 'description' => 'Caisse et espèces', 'type' => 'tiers', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            
            // Classe 6 - Comptes de charges
            ['numero_compte' => '601000', 'intitule' => 'Achats de matériel', 'description' => 'Achats de matériel et fournitures', 'type' => 'charges', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '602000', 'intitule' => 'Achats de carburant', 'description' => 'Achats de carburant et lubrifiants', 'type' => 'charges', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '606000', 'intitule' => 'Entretien et réparations', 'description' => 'Maintenance et réparations', 'type' => 'charges', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '613000', 'intitule' => 'Locations', 'description' => 'Locations immobilières et matériel', 'type' => 'charges', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '621000', 'intitule' => 'Personnel extérieur', 'description' => 'Prestataires de services', 'type' => 'charges', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '641000', 'intitule' => 'Salaires et traitements', 'description' => 'Rémunération du personnel', 'type' => 'charges', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '642000', 'intitule' => 'Charges sociales', 'description' => 'Cotisations sociales patronales', 'type' => 'charges', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '645000', 'intitule' => 'Charges fiscales', 'description' => 'Impôts et taxes', 'type' => 'charges', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '681000', 'intitule' => 'Dotations aux amortissements', 'description' => 'Amortissements des immobilisations', 'type' => 'charges', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            
            // Classe 7 - Comptes de produits
            ['numero_compte' => '701000', 'intitule' => 'Ventes de services', 'description' => 'Prestations de services logistiques', 'type' => 'produits', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '702000', 'intitule' => 'Ventes de matériel', 'description' => 'Ventes de matériel et équipements', 'type' => 'produits', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '707000', 'intitule' => 'Prestations diverses', 'description' => 'Autres prestations de services', 'type' => 'produits', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['numero_compte' => '771000', 'intitule' => 'Produits exceptionnels', 'description' => 'Produits non courants', 'type' => 'produits', 'actif' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 6. Insertion d'un compte bancaire par défaut
        DB::table('compte_bancaires')->insert([
            'nom_banque' => 'SOCIETE GENERALE',
            'numero_compte' => 'SG-KENAM-001',
            'solde_initial' => 0,
            'solde' => 0,
            'devise' => 'XOF',
            'type_compte' => 'courant',
            'actif' => true,
            'description' => 'Compte principal KENAM Services',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
        Schema::dropIfExists('recettes');
        Schema::dropIfExists('compte_bancaires');
        Schema::dropIfExists('ecritures_comptables');
    }
};
