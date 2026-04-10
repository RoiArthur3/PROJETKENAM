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
        Schema::create('services_operationnels', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->string('code', 10)->unique();
            $table->text('description')->nullable();
            $table->string('email', 150)->unique();
            $table->string('password')->default('12345678');
            $table->string('telephone', 20)->nullable();
            $table->string('responsable', 100)->nullable();
            $table->string('couleur', 7)->default('#007bff');
            $table->string('icone', 50)->default('fas fa-cogs');
            $table->boolean('actif')->default(true);
            $table->integer('ordre')->default(0);
            $table->timestamps();

            // Index pour optimisation
            $table->index('code');
            $table->index('email');
            $table->index('actif');
            $table->index('ordre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services_operationnels');
    }
};
