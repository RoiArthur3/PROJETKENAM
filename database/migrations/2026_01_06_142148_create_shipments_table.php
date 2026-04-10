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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->enum('transport_mode', ['air_normal', 'air_express', 'sea']);
            $table->string('origin_country');
            $table->string('origin_warehouse');
            $table->string('destination_country');
            $table->string('destination_warehouse')->nullable();
            $table->enum('status', ['pending', 'in_transit', 'arrived', 'completed'])->default('pending');
            $table->date('departure_date')->nullable();
            $table->date('arrival_date')->nullable();
            $table->date('estimated_arrival_date')->nullable();
            $table->string('carrier_name')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
