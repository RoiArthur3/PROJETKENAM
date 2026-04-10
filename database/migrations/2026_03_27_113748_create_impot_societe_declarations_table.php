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
        Schema::create('impot_societe_declarations', function (Blueprint $table) {
            $table->id();
            $table->integer('exercice_fiscal')->unique();
            $table->decimal('benefice_comptable', 15, 2);
            $table->decimal('deductions_fiscales', 15, 2)->nullable();
            $table->decimal('benefice_fiscal', 15, 2);
            $table->decimal('is_du', 15, 2);
            $table->decimal('acomptes_verses', 15, 2);
            $table->decimal('solde_is', 15, 2);
            $table->date('date_declaration');
            $table->string('statut')->default('brouillon'); // brouillon, déposé, validé, rejeté
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index(['exercice_fiscal', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impot_societe_declarations');
    }
};
