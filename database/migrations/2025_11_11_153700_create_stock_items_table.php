<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 60)->unique();
            $table->string('nom', 180);
            $table->string('unite', 30)->default('u');
            $table->string('categorie', 100)->nullable();
            $table->boolean('suit_serial')->default(false);
            $table->boolean('suit_lot')->default(false);
            $table->decimal('dernier_cout', 18, 2)->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->index(['actif', 'categorie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
