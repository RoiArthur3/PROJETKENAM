<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_product_keyword', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('product_keyword_id')->constrained('product_keywords')->onDelete('cascade');
            $table->enum('restriction_type', ['allowed', 'forbidden'])->default('allowed');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['country_id', 'product_keyword_id']);
            $table->index(['country_id', 'restriction_type']);
            $table->index(['product_keyword_id', 'restriction_type'], 'country_product_keyword_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_product_keyword');
    }
};
