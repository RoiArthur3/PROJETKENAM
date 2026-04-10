<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommandeFournisseursTables extends Migration
{
    public function up()
    {
        // Table des commandes fournisseurs
        Schema::create('commande_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 50);

            // Références
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('restrict');
            $table->foreignId('contrat_id')->nullable()->constrained('contrat_fournisseurs')->onDelete('set null');

            // Informations générales
            $table->date('date_commande');
            $table->date('date_livraison_prevue')->nullable();
            $table->date('date_livraison_reelle')->nullable();

            // Adresse de livraison
            $table->string('adresse_livraison', 255)->nullable();
            $table->string('code_postal_livraison', 10)->nullable();
            $table->string('ville_livraison', 100)->nullable();
            $table->string('pays_livraison', 100)->nullable();

            // Montants
            $table->decimal('montant_ht', 15, 2)->default(0);
            $table->decimal('tva', 15, 2)->default(0);
            $table->decimal('montant_ttc', 15, 2)->default(0);
            $table->decimal('frais_livraison', 15, 2)->default(0);
            $table->decimal('remise', 15, 2)->default(0);
            $table->string('type_remise', 20)->default('pourcentage')->comment('pourcentage ou montant');

            // Conditions de paiement
            $table->string('mode_paiement', 50)->nullable();
            $table->string('conditions_paiement', 255)->nullable();
            $table->integer('delai_paiement')->nullable()->comment('En jours');

            // Statut
            $table->enum('statut', [
                'brouillon', 'en_attente', 'validee', 'en_cours',
                'livree', 'partiellement_livree', 'annulee', 'refusee'
            ])->default('brouillon');

            // Suivi
            $table->text('notes')->nullable();
            $table->text('commentaires_livraison')->nullable();

            // Responsable
            $table->foreignId('responsable_id')->nullable()->constrained('users')->onDelete('set null');

            // Relations
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('reference');
            $table->index('fournisseur_id');
            $table->index('contrat_id');
            $table->index('date_commande');
            $table->index('statut');
        });

        // Table des lignes de commande fournisseurs
        Schema::create('ligne_commande_fournisseurs', function (Blueprint $table) {
            $table->id();

            // Références
            $table->foreignId('commande_id')->constrained('commande_fournisseurs')->onDelete('cascade');
            $table->unsignedBigInteger('article_id')->nullable();

            // Désignation
            $table->string('reference_article', 100)->nullable();
            $table->string('designation', 255);
            $table->text('description')->nullable();

            // Quantité et prix
            $table->decimal('quantite', 12, 3)->default(1);
            $table->string('unite', 20)->default('unité');
            $table->decimal('prix_unitaire_ht', 15, 4)->default(0);
            $table->decimal('tva_taux', 5, 2)->default(20.00);
            $table->decimal('remise', 5, 2)->default(0);
            $table->string('type_remise', 20)->default('pourcentage')->comment('pourcentage ou montant');

            // Montants
            $table->decimal('montant_ht', 15, 2)->default(0);
            $table->decimal('montant_tva', 15, 2)->default(0);
            $table->decimal('montant_ttc', 15, 2)->default(0);

            // Livraison
            $table->decimal('quantite_livree', 12, 3)->default(0);
            $table->date('date_livraison_prevue')->nullable();
            $table->date('date_livraison_reelle')->nullable();

            // Statut
            $table->enum('statut', [
                'en_attente', 'en_cours', 'livree',
                'partiellement_livree', 'annulee', 'refusee'
            ])->default('en_attente');

            // Informations complémentaires
            $table->text('notes')->nullable();
            $table->json('caracteristiques')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('commande_id');
            $table->index('article_id');
            $table->index('reference_article');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ligne_commande_fournisseurs');
        Schema::dropIfExists('commande_fournisseurs');
    }
}
