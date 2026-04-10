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
        Schema::create('lignes_bons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_commande_id')->constrained('bons_commande')->onDelete('cascade');
            $table->string('reference_produit');
            $table->string('designation');
            $table->text('description')->nullable();
            $table->integer('quantite');
            $table->decimal('prix_unitaire_ht', 10, 2);
            $table->decimal('tva', 5, 2)->default(20.00);
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('montant_ttc', 10, 2);
            $table->enum('unite', ['unite', 'kg', 'litre', 'metre', 'piece', 'heure'])->default('unite');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index
            $table->index('bon_commande_id');
            $table->index('reference_produit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('lignes_bons');
    }
};
