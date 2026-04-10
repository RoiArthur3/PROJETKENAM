<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assurances', function (Blueprint $table) {
            $table->id();
            // Référence au parc auto (table 'vehicules')
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('numero_police');
            $table->string('assureur');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->decimal('prime_annuelle', 15, 2)->nullable();
            $table->string('statut')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assurances');
    }
};
