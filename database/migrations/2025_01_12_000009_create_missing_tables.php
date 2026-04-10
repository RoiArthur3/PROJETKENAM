<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Créer la table vehicules si elle n'existe pas
        if (!Schema::hasTable('vehicules')) {
            Schema::create('vehicules', function (Blueprint $table) {
                $table->id();
                $table->string('immatriculation');
                $table->string('marque');
                $table->string('modele');
                $table->boolean('disponible')->default(true);
                $table->timestamps();
            });
        }

        // Créer la table pointages si elle n'existe pas
        if (!Schema::hasTable('pointages')) {
            Schema::create('pointages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->date('date_pointage');
                $table->time('heure_arrivee');
                $table->time('heure_depart');
                $table->string('statut')->default('present');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Créer la table factures si elle n'existe pas
        if (!Schema::hasTable('factures')) {
            Schema::create('factures', function (Blueprint $table) {
                $table->id();
                $table->string('numero')->unique();
                $table->foreignId('client_id')->constrained()->onDelete('cascade');
                $table->date('date_facture');
                $table->decimal('montant_ht', 10, 2);
                $table->decimal('tva', 5, 2)->default(20.00);
                $table->decimal('montant_ttc', 10, 2);
                $table->enum('statut', ['en_attente', 'payee', 'en_retard', 'annulee'])->default('en_attente');
                $table->text('description')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('vehicules');
        Schema::dropIfExists('pointages');
        Schema::dropIfExists('factures');
    }
};
