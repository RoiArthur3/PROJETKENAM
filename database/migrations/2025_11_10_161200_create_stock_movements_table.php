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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            
            // Référence unique
            $table->string('reference')->unique();
            
            // Relations
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('operation_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Type de mouvement
            $table->enum('type', ['entree', 'sortie', 'transfert', 'retour', 'ajustement', 'perte']);
            
            // Quantités
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->decimal('total_value', 15, 2)->nullable();
            
            // Dates
            $table->date('movement_date');
            $table->text('reason')->nullable();
            
            // Documents associés
            $table->string('document_reference')->nullable(); // bon de livraison, facture, etc.
            $table->string('document_type')->nullable(); // BL, FC, etc.
            
            // Validation
            $table->boolean('is_validated')->default(false);
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('validated_at')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['product_id', 'movement_date']);
            $table->index(['warehouse_id', 'type']);
            $table->index(['operation_id']);
            $table->index(['supplier_id']);
            $table->index(['type', 'is_validated']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
