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
        Schema::create('entrepots', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('nom', 200);
            $table->string('adresse', 300)->nullable();
            $table->string('responsable', 200)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->decimal('capacite', 10, 2)->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->index(['code', 'actif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrepots');
    }
};
