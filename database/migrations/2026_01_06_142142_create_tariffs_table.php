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
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('transport_mode', ['air_normal', 'air_express', 'sea']);
            $table->string('origin_country');
            $table->string('destination_country');
            $table->decimal('min_weight', 8, 2)->default(0);
            $table->decimal('max_weight', 8, 2)->nullable();
            $table->decimal('price_per_kg', 10, 2);
            $table->decimal('fixed_fee', 10, 2)->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->integer('volumetric_divisor')->default(5000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};
