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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20);
            $table->string('template', 100);
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending');
            $table->string('provider', 50)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('operation_reference', 50)->nullable();
            $table->text('message')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('template');
            $table->index('created_at');
            $table->index('user_id');

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
