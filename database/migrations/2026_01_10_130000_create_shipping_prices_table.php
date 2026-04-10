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
        Schema::create('shipping_prices', function (Blueprint $table) {
            $table->id();
            $table->enum('transport_mode', ['air_normal', 'air_express', 'sea']);
            $table->decimal('price_per_kg', 10, 2);
            $table->decimal('minimum_price', 10, 2)->default(0);
            $table->decimal('insurance_rate', 5, 2)->default(0); // Pourcentage
            $table->decimal('customs_rate', 5, 2)->default(0); // Pourcentage
            $table->decimal('handling_fee', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_prices');
    }
};
