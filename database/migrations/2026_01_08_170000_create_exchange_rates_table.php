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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency', 3); // EUR, USD, XOF, etc.
            $table->string('to_currency', 3);   // EUR, USD, XOF, etc.
            $table->decimal('rate', 10, 6);     // Taux de change
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['from_currency', 'to_currency']);
            $table->index(['from_currency', 'to_currency', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
