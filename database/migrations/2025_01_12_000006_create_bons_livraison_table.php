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
        Schema::create('bons_livraison', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->unsignedBigInteger('bon_commande_id');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->date('date_livraison');
            $table->string('livreur')->nullable();
            $table->text('adresse_livraison')->nullable();
            $table->enum('statut', ['en_preparation', 'en_transit', 'livre', 'retourne', 'annule'])->default('en_preparation');
            $table->text('notes')->nullable();
            $table->text('observations')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('delivered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Index
            $table->index('bon_commande_id');
            $table->index('client_id');
            $table->index('statut');
            $table->index('date_livraison');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('bons_livraison');
    }
};
