<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_transferts', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('produit_id')->nullable();
            $table->string('produit_nom', 200);
            $table->decimal('quantite', 10, 2);
            $table->string('entrepot_source', 200);
            $table->string('entrepot_destination', 200);
            $table->text('motif')->nullable();
            $table->string('statut', 50)->default('En attente');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['date', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transferts');
    }
};
