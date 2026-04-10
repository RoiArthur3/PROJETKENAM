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
        if (Schema::hasTable('clients')) {
            return; // La table existe déjà, ne pas la recréer
        }

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('code_client')->unique(); // Code unique du client
            $table->string('nom_complet'); // Nom complet du client
            $table->string('raison_sociale')->nullable(); // Raison sociale si entreprise
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->text('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->default('Cote dIvoire');
            $table->string('numero_contribuable')->nullable(); // Numéro contribuable fiscal
            $table->string('registre_commerce')->nullable(); // RC
            $table->string('compte_bancaire')->nullable();
            $table->string('banque')->nullable();
            $table->enum('type_client', ['particulier', 'entreprise', 'administration'])->default('particulier');
            $table->decimal('plafond_credit', 15, 2)->default(0); // Plafond de crédit autorisé
            $table->decimal('solde_du', 15, 2)->default(0); // Solde dû par le client
            $table->boolean('est_actif')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('nom_complet');
            $table->index('type_client');
            $table->index('est_actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
