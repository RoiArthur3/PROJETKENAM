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
        Schema::create('service_personne_ressources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['chef_service', 'personne_ressource'])->default('personne_ressource');
            $table->boolean('recevoir_emails')->default(true);
            $table->timestamps();

            // Un utilisateur ne peut être qu'une seule fois personne ressource pour un service
            $table->unique(['service_id', 'user_id']);

            // Index pour les recherches fréquentes
            $table->index(['service_id', 'recevoir_emails']);
            $table->index(['user_id', 'recevoir_emails']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_personne_ressources');
    }
};
