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
        if (Schema::hasTable('bons_commande')) {
            return; // La table existe déjà, ne pas la recréer
        }

        Schema::create('bons_commande', function (Blueprint $table) {
            $table->id();
            $table->string('numero_bc')->unique(); // Numéro du bon de commande
            $table->date('date_bc'); // Date du bon de commande
            $table->string('client_id')->nullable(); // Référence vers table clients
            $table->string('projet_id')->nullable(); // Référence vers table projets
            $table->text('objet'); // Objet de la commande
            $table->text('description')->nullable(); // Description détaillée
            $table->decimal('montant_estime', 15, 2)->default(0); // Montant estimé
            $table->decimal('montant_reel', 15, 2)->nullable(); // Montant réel final
            $table->enum('statut', ['brouillon', 'envoye', 'accepte', 'refuse', 'converti_en_facture'])->default('brouillon');
            $table->date('date_acceptation')->nullable();
            $table->date('date_refus')->nullable();
            $table->text('motif_refus')->nullable();
            $table->string('facture_id')->nullable(); // Référence vers facture générée
            $table->enum('type', ['biens', 'services', 'mixte'])->default('services');
            $table->string('demandeur'); // Personne qui a fait la demande
            $table->string('approuve_par')->nullable(); // Personne qui a approuvé
            $table->text('conditions_paiement')->nullable(); // Conditions de paiement
            $table->date('date_livraison_prevue')->nullable();
            $table->text('notes')->nullable();
            $table->string('created_by');
            $table->timestamps();

            $table->index('client_id');
            $table->index('projet_id');
            $table->index('statut');
            $table->index(['date_bc', 'statut']);
            $table->index('numero_bc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bons_commande');
    }
};
