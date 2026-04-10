<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shipping_labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('receiving_address_id')->nullable()->constrained()->onDelete('set null');
            $table->string('label_number')->unique();
            $table->string('sender_name');
            $table->string('sender_address');
            $table->string('sender_city');
            $table->string('sender_postal_code');
            $table->string('sender_country');
            $table->string('sender_phone');
            $table->string('sender_email');
            $table->text('parcel_contents');
            $table->decimal('declared_value', 10, 2)->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('purchase_store')->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('status')->default('draft');
            $table->string('pdf_path')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_labels');
    }
};
