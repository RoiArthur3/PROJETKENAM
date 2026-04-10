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
        Schema::create('entreprise_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nom_entreprise')->nullable();
            $table->text('adresse')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('rc')->nullable(); // Registre de Commerce
            $table->string('cc')->nullable(); // Centre de Contribution
            $table->string('logo')->nullable(); // Chemin vers le logo
            $table->string('siteweb')->nullable();
            $table->string('ifu')->nullable(); // Identifiant Fiscal Unique
            $table->string('rccm')->nullable(); // Registre du Commerce et du Crédit Mobilier
            $table->text('description')->nullable();
            $table->boolean('est_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprise_settings');
    }
};
