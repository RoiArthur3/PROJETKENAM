<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reparations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicule_id')->constrained('vehicules')->cascadeOnDelete();
            $table->date('date');
            $table->string('type_panne', 100);
            $table->string('urgence', 50)->default('Normale');
            $table->text('description');
            $table->string('garage', 200)->nullable();
            $table->decimal('cout_estime', 12, 2)->nullable();
            $table->decimal('cout_reel', 12, 2)->nullable();
            $table->string('statut', 50)->default('En attente');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['date', 'statut', 'urgence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reparations');
    }
};
