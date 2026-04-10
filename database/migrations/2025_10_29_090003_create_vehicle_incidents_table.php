<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->string('type')->nullable(); // accident, panne, bris_de_glace, etc.
            $table->date('date_incident')->nullable();
            $table->string('severity')->nullable(); // low, medium, high
            $table->text('description')->nullable();
            $table->string('status')->default('open'); // open, in_progress, closed
            $table->string('attachment')->nullable();
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('final_cost', 12, 2)->nullable();
            $table->timestamps();
            $table->index(['vehicle_id','status','type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_incidents');
    }
};
