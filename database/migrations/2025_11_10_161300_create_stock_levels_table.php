<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_levels', function (Blueprint $table) {
            $table->id();
            
            // Relations
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            
            // Quantités actuelles
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->decimal('reserved_stock', 12, 2)->default(0); // réservé pour opérations
            $table->decimal('available_stock', 12, 2)->default(0); // current - reserved
            
            // Valeur du stock
            $table->decimal('average_cost', 12, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            
            // Dates de suivi
            $table->date('last_entry_date')->nullable();
            $table->date('last_exit_date')->nullable();
            $table->datetime('last_updated');
            
            // Alertes
            $table->boolean('low_stock_alert')->default(false);
            $table->boolean('critical_stock_alert')->default(false);
            $table->boolean('overstock_alert')->default(false);
            
            $table->timestamps();
            
            // Contrainte unique et index
            $table->unique(['product_id', 'warehouse_id']);
            $table->index(['warehouse_id', 'low_stock_alert']);
            $table->index(['warehouse_id', 'critical_stock_alert']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_levels');
    }
};
