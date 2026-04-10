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
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shipment_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('transport_mode', ['air_normal', 'air_express', 'sea']);
            $table->enum('status', ['announced', 'received', 'inspected', 'rejected', 'grouped', 'in_transit', 'arrived', 'fees_calculated', 'paid', 'delivered'])->default('announced');
            $table->string('origin_country');
            $table->string('destination_country');
            $table->text('content_description')->nullable();
            $table->decimal('declared_value', 10, 2)->nullable();
            $table->decimal('actual_weight', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('volumetric_weight', 8, 2)->nullable();
            $table->decimal('chargeable_weight', 8, 2)->nullable();
            $table->date('received_at_warehouse_date')->nullable();
            $table->date('inspection_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcels');
    }
};
