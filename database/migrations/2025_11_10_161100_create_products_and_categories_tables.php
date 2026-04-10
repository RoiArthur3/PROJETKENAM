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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['code', 'is_active']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Catégorie
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('set null');
            
            // Stock et unités
            $table->string('unit', 20)->default('unité'); // unité, kg, litre, mètre, etc.
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->default(0);
            
            // Seuils de stock
            $table->decimal('min_stock_level', 12, 2)->default(0);
            $table->decimal('max_stock_level', 12, 2)->nullable();
            $table->decimal('safety_stock', 12, 2)->default(0);
            
            // Fournisseur
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            
            // Statut et suivi
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->string('location')->nullable(); // emplacement dans l'entrepôt
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['reference', 'status']);
            $table->index(['category_id']);
            $table->index(['supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
    }
};
