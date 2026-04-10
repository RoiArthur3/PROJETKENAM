<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
                $table->enum('type', ['entry', 'exit', 'transfer', 'adjust', 'inventory']);
                $table->decimal('qty', 18, 3);
                $table->string('from_location_type')->nullable();
                $table->unsignedBigInteger('from_location_id')->nullable();
                $table->string('to_location_type')->nullable();
                $table->unsignedBigInteger('to_location_id')->nullable();
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('occurred_at')->useCurrent();
                $table->timestamps();
                $table->index(['occurred_at']);
                $table->index(['type']);
                $table->index(['stock_item_id', 'occurred_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
