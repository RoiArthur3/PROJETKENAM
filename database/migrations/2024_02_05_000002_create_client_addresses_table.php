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
        Schema::create('client_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->enum('address_type', ['billing', 'shipping', 'pickup', 'return'])->default('shipping');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state_province')->nullable();
            $table->string('country');
            $table->string('postal_code')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('special_instructions')->nullable();
            $table->decimal('coordinates_lat', 8, 6)->nullable();
            $table->decimal('coordinates_lng', 9, 6)->nullable();
            $table->timestamps();

            $table->index(['client_id', 'address_type']);
            $table->index(['client_id', 'is_primary']);
            $table->index(['country']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_addresses');
    }
};
