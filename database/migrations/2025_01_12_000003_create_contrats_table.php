<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->unsignedBigInteger('client_id');
            $table->foreignId('service_id')->nullable();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('tva', 5, 2)->default(20.00);
            $table->decimal('montant_ttc', 10, 2);
            $table->enum('statut', ['en_attente', 'actif', 'termine', 'annule'])->default('en_attente');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Index
            $table->index('client_id');
            $table->index('service_id');
            $table->index('statut');
            $table->index('date_debut');
            $table->index('date_fin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('contrats');
    }
};
