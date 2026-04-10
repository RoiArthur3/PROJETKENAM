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
        Schema::create('ateliers', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null'); // Lié à un véhicule du parc
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Responsable de l'atelier
            $table->enum('type', ['maintenance', 'reparation', 'inspection', 'autre'])->default('maintenance');
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'annule'])->default('planifie');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->decimal('cout_estime', 15, 2)->default(0);
            $table->decimal('cout_reel', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ateliers');
    }
};
