<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->string('location_type'); // App\Models\Warehouse | App\Models\Store
            $table->unsignedBigInteger('location_id');
            $table->decimal('qty_available', 18, 3)->default(0);
            $table->decimal('qty_reserved', 18, 3)->default(0);
            $table->decimal('min', 18, 3)->nullable();
            $table->decimal('max', 18, 3)->nullable();
            $table->timestamps();
            $table->unique(['stock_item_id', 'location_type', 'location_id'], 'sb_unique_item_location');
            $table->index(['location_type', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_balances');
    }
};
