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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('raison_sociale');
            $table->string('contact_nom');
            $table->string('contact_prenom');
            $table->string('email')->unique();
            $table->string('telephone');
            $table->string('adresse');
            $table->string('ville');
            $table->string('pays')->default('Cote dIvoire');
            $table->string('ice')->unique()->nullable();
            $table->string('if')->nullable();
            $table->string('patente')->nullable();
            $table->string('cnss')->nullable();
            $table->enum('type', ['particulier', 'entreprise', 'administration'])->default('entreprise');
            $table->enum('statut', ['actif', 'inactif', 'en_attente'])->default('actif');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
