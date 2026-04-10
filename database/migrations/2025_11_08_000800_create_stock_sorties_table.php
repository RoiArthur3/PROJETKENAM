<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_sorties', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('type', 50)->default('Utilisation');
            $table->foreignId('produit_id')->nullable()->constrained('produits')->nullOnDelete();
            $table->string('produit_nom', 200);
            $table->decimal('quantite', 10, 2);
            $table->string('destinataire', 200)->nullable();
            $table->string('demandeur', 200)->nullable();
            $table->text('motif')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_sorties');
    }
};
