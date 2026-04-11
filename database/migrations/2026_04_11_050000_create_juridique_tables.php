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
        // 1. Table des contrats juridiques
        Schema::create('juridique_contrats', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 100)->unique();
            $table->string('titre', 255);
            $table->text('description')->nullable();
            $table->string('type_contrat', 100); // vente, achat, service, location, etc.
            $table->string('partie_a', 255); // Client/Fournisseur A
            $table->string('partie_b', 255); // Client/Fournisseur B
            $table->date('date_signature');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('montant', 15, 2)->nullable();
            $table->string('devise', 10)->default('XOF');
            $table->string('statut', 50)->default('actif'); // actif, expire, resilie, etc.
            $table->string('fichier_contrat')->nullable(); // chemin vers le fichier PDF
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->index('type_contrat');
            $table->index('statut');
            $table->index('date_signature');
            $table->index('date_fin');
        });

        // 2. Table des documents juridiques
        Schema::create('juridique_documents', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 100)->unique()->nullable();
            $table->string('titre', 255);
            $table->string('type_document', 100)->nullable(); // facture, contrat, convention, etc.
            $table->text('description')->nullable();
            $table->unsignedBigInteger('contrat_id')->nullable();
            $table->date('date_document')->nullable();
            $table->date('date_expiration')->nullable();
            $table->string('statut', 50)->default('actif');
            $table->string('chemin_fichier')->nullable(); // chemin vers le fichier
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('contrat_id')->references('id')->on('juridique_contrats')->onDelete('set null');
            $table->index('type_document');
            $table->index('statut');
            $table->index('date_document');
            $table->index('date_expiration');
        });

        // 3. Table des financements
        Schema::create('juridique_financements', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 100)->unique();
            $table->string('titre', 255);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('contrat_id')->nullable();
            $table->string('type_financement', 100); // bancaire, propre, leasing, etc.
            $table->string('organisme_preteur', 255);
            $table->decimal('montant_emprunte', 15, 2);
            $table->decimal('taux_interet', 8, 4); // taux en %
            $table->integer('duree_mois'); // durée en mois
            $table->date('date_debut');
            $table->date('date_fin');
            $table->decimal('mensualite', 15, 2);
            $table->string('devise', 10)->default('XOF');
            $table->string('statut', 50)->default('en_cours'); // en_cours, termine, etc.
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('contrat_id')->references('id')->on('juridique_contrats')->onDelete('set null');
            $table->index('type_financement');
            $table->index('statut');
            $table->index('date_debut');
        });

        // 4. Table des offres bancaires
        Schema::create('juridique_offres_bancaires', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 100)->unique();
            $table->string('titre', 255);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('financement_id')->nullable();
            $table->string('banque', 255);
            $table->decimal('montant_offre', 15, 2);
            $table->decimal('taux_interet', 8, 4);
            $table->integer('duree_mois');
            $table->decimal('apport_personnel', 15, 2)->default(0);
            $table->string('devise', 10)->default('XOF');
            $table->string('statut', 50)->default('en_attente'); // en_attente, acceptee, refusee
            $table->date('date_offre');
            $table->date('date_limite')->nullable();
            $table->text('conditions')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('financement_id')->references('id')->on('juridique_financements')->onDelete('set null');
            $table->index('statut');
            $table->index('date_offre');
        });

        // 5. Table des échéanciers
        Schema::create('juridique_echeanciers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('financement_id');
            $table->unsignedBigInteger('offre_bancaire_id')->nullable();
            $table->integer('numero_echeance');
            $table->date('date_echeance');
            $table->decimal('capital_amorti', 15, 2);
            $table->decimal('interets', 15, 2);
            $table->decimal('mensualite', 15, 2);
            $table->decimal('capital_restant', 15, 2);
            $table->string('statut', 50)->default('en_attente'); // en_attente, paye, retard
            $table->date('date_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('financement_id')->references('id')->on('juridique_financements')->onDelete('cascade');
            $table->foreign('offre_bancaire_id')->references('id')->on('juridique_offres_bancaires')->onDelete('set null');
            $table->index('financement_id');
            $table->index('offre_bancaire_id');
            $table->index('date_echeance');
            $table->index('statut');
        });

        // Insertion de données de test
        $this->insertTestData();
    }

    /**
     * Insertion de données de test
     */
    private function insertTestData(): void
    {
        // Créer un contrat de test
        $contratId = DB::table('juridique_contrats')->insertGetId([
            'reference' => 'CONT-2026-001',
            'titre' => 'Contrat de prestations de services KENAM',
            'description' => 'Contrat pour prestations de services informatiques et conseil',
            'type_contrat' => 'service',
            'partie_a' => 'KENAM SERVICES',
            'partie_b' => 'CLIENT EXEMPLE',
            'date_signature' => '2026-01-15',
            'date_debut' => '2026-01-15',
            'date_fin' => '2026-12-31',
            'montant' => 5000000,
            'devise' => 'XOF',
            'statut' => 'actif',
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Créer quelques documents de test
        DB::table('juridique_documents')->insert([
            [
                'reference' => 'DOC-2026-001',
                'titre' => 'Convention de collaboration',
                'type_document' => 'convention',
                'contrat_id' => $contratId,
                'date_document' => '2026-01-15',
                'statut' => 'actif',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'reference' => 'DOC-2026-002',
                'titre' => 'Facture proforma Q1-2026',
                'type_document' => 'facture',
                'contrat_id' => $contratId,
                'date_document' => '2026-03-31',
                'statut' => 'actif',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Créer un financement de test
        $financementId = DB::table('juridique_financements')->insertGetId([
            'reference' => 'FIN-2026-001',
            'titre' => 'Financement équipement informatique',
            'description' => 'Financement pour l\'acquisition de nouveaux serveurs',
            'contrat_id' => $contratId,
            'type_financement' => 'bancaire',
            'organisme_preteur' => 'BANQUE NATIONALE',
            'montant_emprunte' => 10000000,
            'taux_interet' => 8.5,
            'duree_mois' => 24,
            'date_debut' => '2026-02-01',
            'date_fin' => '2028-01-31',
            'mensualite' => 454500,
            'devise' => 'XOF',
            'statut' => 'en_cours',
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Créer une offre bancaire de test
        DB::table('juridique_offres_bancaires')->insert([
            'reference' => 'OFF-2026-001',
            'titre' => 'Offre de financement BIAO',
            'description' => 'Offre préférentielle pour équipement informatique',
            'financement_id' => $financementId,
            'banque' => 'BANQUE INTERNATIONALE POUR L\'AFRIQUE OCCIDENTALE',
            'montant_offre' => 10000000,
            'taux_interet' => 8.5,
            'duree_mois' => 24,
            'apport_personnel' => 1000000,
            'devise' => 'XOF',
            'statut' => 'acceptee',
            'date_offre' => '2026-01-20',
            'date_limite' => '2026-02-15',
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juridique_echeanciers');
        Schema::dropIfExists('juridique_offres_bancaires');
        Schema::dropIfExists('juridique_financements');
        Schema::dropIfExists('juridique_documents');
        Schema::dropIfExists('juridique_contrats');
    }
};
