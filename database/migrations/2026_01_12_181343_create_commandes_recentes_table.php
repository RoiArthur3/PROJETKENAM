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
        Schema::create('commandes_recentes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('client_nom');
            $table->decimal('montant_total', 15, 2);
            $table->enum('statut', ['en_attente', 'confirmee', 'en_cours', 'livree', 'annulee'])->default('en_attente');
            $table->date('date_commande');
            $table->date('date_livraison_prevue')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes_recentes');
    }
};
