<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('designation', 200);
            $table->string('categorie', 100)->nullable();
            $table->string('unite', 50)->default('Unité');
            $table->integer('stock_min')->default(0);
            $table->integer('stock_actuel')->default(0);
            $table->decimal('prix_unitaire', 12, 2)->nullable();
            $table->string('emplacement', 200)->nullable();
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->index(['code', 'actif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
