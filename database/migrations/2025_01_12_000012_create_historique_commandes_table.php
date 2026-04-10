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
        Schema::create('historique_commandes', function (Blueprint $table) {
            $table->id();
            $table->string('reference_commande')->unique();
            $table->string('type_commande');
            $table->text('description')->nullable();
            $table->enum('statut', ['en_attente', 'en_cours', 'termine', 'annule'])->default('en_attente');
            $table->date('date_commande');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('lieu_depart');
            $table->string('lieu_arrivee')->nullable();
            $table->string('lieu_retour')->nullable();
            $table->decimal('kilometrage_depart', 10, 2)->default(0);
            $table->decimal('kilometrage_retour', 10, 2)->default(0);
            $table->text('observations')->nullable();
            $table->foreignId('chauffeur_id')->nullable()->constrained('chauffeurs')->onDelete('set null');
            $table->foreignId('materiel_roulant_id')->nullable()->constrained('materiel_roulant')->onDelete('set null');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Index
            $table->index('reference_commande');
            $table->index('statut');
            $table->index('date_commande');
            $table->index('chauffeur_id');
            $table->index('materiel_roulant_id');
            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('historique_commandes');
    }
};
