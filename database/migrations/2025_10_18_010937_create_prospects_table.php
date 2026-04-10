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
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->text('adresse')->nullable();
            $table->string('entreprise')->nullable();
            $table->string('source'); // Comment ils nous ont connu (réseaux sociaux, référencement, etc.)
            $table->enum('statut', ['nouveau', 'en_cours', 'qualifie', 'converti', 'perdu'])->default('nouveau');
            $table->text('notes')->nullable();
            $table->decimal('valeur_potentielle', 15, 2)->default(0); // Valeur estimée du prospect
            $table->date('date_contact')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Qui gère le prospect
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};
