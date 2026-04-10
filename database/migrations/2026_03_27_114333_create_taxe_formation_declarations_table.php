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
        Schema::create('taxe_formation_declarations', function (Blueprint $table) {
            $table->id();
            $table->string('periode')->unique(); // Format: YYYY-MM
            $table->decimal('masse_salariale_brut', 15, 2);
            $table->decimal('tfp', 15, 2); // Taxe de Formation Professionnelle
            $table->decimal('taxe_apprentissage', 15, 2); // Taxe d'Apprentissage
            $table->decimal('total_taxes', 15, 2);
            $table->date('date_declaration');
            $table->string('statut')->default('brouillon'); // brouillon, déposé, validé, rejeté
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index(['periode', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxe_formation_declarations');
    }
};
