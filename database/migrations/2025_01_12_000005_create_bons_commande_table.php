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
        Schema::create('bons_commande', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('contrat_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date_commande');
            $table->date('date_livraison_prevue')->nullable();
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('tva', 5, 2)->default(20.00);
            $table->decimal('montant_ttc', 10, 2);
            $table->enum('statut', ['brouillon', 'envoye', 'valide', 'en_preparation', 'livre', 'annule'])->default('brouillon');
            $table->text('notes')->nullable();
            $table->text('conditions_livraison')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Index
            $table->index('client_id');
            $table->index('contrat_id');
            $table->index('statut');
            $table->index('date_commande');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('bons_commande');
    }
};
