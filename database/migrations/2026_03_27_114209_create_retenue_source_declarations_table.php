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
        Schema::create('retenue_source_declarations', function (Blueprint $table) {
            $table->id();
            $table->string('periode')->unique(); // Format: YYYY-MM
            $table->decimal('total_montant_ht', 15, 2);
            $table->decimal('total_retenue_source', 15, 2);
            $table->decimal('total_tva', 15, 2);
            $table->decimal('total_montant_ttc', 15, 2);
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
        Schema::dropIfExists('retenue_source_declarations');
    }
};
