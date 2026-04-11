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
        if (!Schema::hasTable('operation_vehicule')) {
            Schema::create('operation_vehicule', function (Blueprint $table) {
                $table->id();
                $table->foreignId('operation_id')->constrained('operations')->onDelete('cascade');
                $table->foreignId('vehicule_id')->constrained('vehicules')->onDelete('cascade');
                $table->date('date_affectation')->default(now());
                $table->date('date_fin_affectation')->nullable();
                $table->boolean('actif')->default(true);
                $table->text('notes')->nullable();
                
                $table->timestamps();
                
                // Index unique pour éviter les doublons
                $table->unique(['operation_id', 'vehicule_id'], 'unique_operation_vehicule');
                
                // Index pour les performances
                $table->index(['operation_id']);
                $table->index(['vehicule_id']);
                $table->index(['actif']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('operation_vehicule');
    }
};
