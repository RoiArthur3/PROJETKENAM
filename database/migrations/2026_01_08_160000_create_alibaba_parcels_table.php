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
        Schema::create('alibaba_parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tracking_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('transport_mode'); // air, sea
            $table->string('destination_country');
            $table->string('phone1');
            $table->string('phone2')->nullable();
            $table->boolean('fragile')->default(false);
            $table->string('alibaba_order_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, received, processed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alibaba_parcels');
    }
};
