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
        Schema::create('proformas', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('projet_id')->nullable();
            $table->date('date_proposition');
            $table->date('date_validite');
            $table->text('conditions_generales')->nullable();
            $table->text('note')->nullable();
            $table->decimal('total_ht', 15, 2)->default(0);
            $table->decimal('total_tva', 15, 2)->default(0);
            $table->decimal('total_ttc', 15, 2)->default(0);
            $table->string('montant_lettres')->nullable();
            $table->enum('statut', ['brouillon', 'envoye', 'valide', 'expire'])->default('brouillon');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->foreign('projet_id')->references('id')->on('operations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proformas');
    }
};
