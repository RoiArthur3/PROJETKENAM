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
        Schema::create('quality_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained()->onDelete('cascade');
            $table->foreignId('stock_movement_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('inspector_id')->constrained('users')->onDelete('cascade');
            $table->integer('qty_ordered');
            $table->integer('qty_received');
            $table->integer('qty_accepted');
            $table->integer('qty_rejected');
            $table->enum('status', ['pending', 'passed', 'failed', 'partial'])->default('pending');
            $table->text('observations')->nullable();
            $table->json('photos')->nullable();
            $table->timestamp('inspected_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_controls');
    }
};
