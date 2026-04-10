<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_entrees', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('type', 50)->default('Achat');
            $table->unsignedBigInteger('produit_id')->nullable();
            $table->string('produit_nom', 200);
            $table->decimal('quantite', 10, 2);
            $table->decimal('prix_unitaire', 12, 2)->nullable();
            $table->decimal('montant_total', 14, 2)->nullable();
            $table->string('fournisseur', 200)->nullable();
            $table->string('reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_entrees');
    }
};
