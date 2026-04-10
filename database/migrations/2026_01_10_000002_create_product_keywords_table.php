<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('keyword');
            $table->enum('status', ['allowed', 'forbidden'])->default('allowed');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['category_id', 'keyword']);
            $table->index(['status', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_keywords');
    }
};
