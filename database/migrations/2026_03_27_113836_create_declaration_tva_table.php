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
        Schema::create('declaration_tva', function (Blueprint $table) {
            $table->id();
            $table->string('periode')->unique(); // Format: YYYY-MM
            $table->decimal('tva_collectee_totale', 15, 2);
            $table->decimal('tva_deductible_totale', 15, 2);
            $table->decimal('tva_a_reverser', 15, 2);
            $table->decimal('credit_tva_reporter', 15, 2)->default(0);
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
        Schema::dropIfExists('declaration_tva');
    }
};
