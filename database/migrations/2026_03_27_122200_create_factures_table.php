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
        // Vérifier si la table existe déjà pour éviter l'erreur en production
        if (Schema::hasTable('factures')) {
            return; // La table existe déjà, ne pas la recréer
        }

        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero_facture')->unique(); // Numéro manuel de facture
            $table->date('date_facturation'); // Date automatique
            $table->date('date_depot')->nullable(); // Date de dépôt (vide si rien)
            $table->string('client_nom'); // Nom du client
            $table->string('client_id')->nullable(); // Référence vers table clients
            $table->string('bon_commande_numero')->nullable(); // Numéro BC
            $table->string('bon_commande_id')->nullable(); // Référence vers table bons_commande
            $table->text('designation'); // Désignation des services/produits
            $table->string('projet_nom')->nullable(); // Nom du projet
            $table->string('projet_id')->nullable(); // Référence vers table projets
            $table->decimal('montant_ht', 15, 2); // Montant HT (constitue le chiffre d'affaires)
            $table->decimal('tva_taux', 5, 2)->default(18.00); // Taux TVA (18% par défaut)
            $table->decimal('montant_tva', 15, 2); // Montant TVA
            $table->decimal('montant_ttc', 15, 2); // Montant TTC
            $table->decimal('acompte', 15, 2)->default(0); // Acompte si existant
            $table->decimal('montant_restant', 15, 2); // Montant TTC - acompte
            $table->string('statut')->default('en_attente'); // en_attente, payee, partiellement_payee, annulee
            $table->string('mode_paiement')->nullable(); // espece, virement, cheque, mobile_money
            $table->date('date_echeance')->nullable(); // Date d'échéance de paiement
            $table->text('notes')->nullable(); // Notes additionnelles
            $table->string('created_by'); // Utilisateur qui a créé la facture
            $table->string('updated_by')->nullable(); // Utilisateur qui a modifié
            $table->timestamps();

            $table->index(['client_id', 'statut']);
            $table->index(['projet_id', 'statut']);
            $table->index(['date_facturation', 'statut']);
            $table->index('numero_facture');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
