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
        Schema::create('proforma_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proforma_id');
            $table->string('designation');
            $table->integer('tva')->default(19); // TVA en pourcentage
            $table->decimal('prix_unitaire_ht', 12, 2);
            $table->decimal('quantite', 10, 2)->default(1);
            $table->string('unite')->default('pièce');
            $table->decimal('total_ht', 15, 2);
            $table->timestamps();

            $table->foreign('proforma_id')->references('id')->on('proformas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_items');
    }
};
