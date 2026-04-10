<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carburant_appoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicule_id')->constrained('vehicules')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('type', ['Gasoil', 'Essence']);
            $table->dateTime('date');
            $table->decimal('litres', 10, 2);
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('montant', 12, 2);
            $table->string('station', 100);
            $table->timestamps();
            $table->index(['vehicule_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carburant_appoints');
    }
};
