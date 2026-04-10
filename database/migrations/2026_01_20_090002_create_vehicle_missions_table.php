<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('vehicle_missions')) {
            Schema::create('vehicle_missions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vehicle_id')->constrained('vehicules')->cascadeOnDelete();
                $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('supplier_id')->nullable()->constrained('fournisseurs')->nullOnDelete();
                $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
                $table->string('reference')->unique();
                $table->string('destination')->nullable();
                $table->dateTime('start_at')->nullable();
                $table->dateTime('end_at')->nullable();
                $table->integer('duration_days')->default(1);
                
                $table->decimal('daily_supplier_price', 15, 2)->default(0);
                $table->decimal('daily_client_price', 15, 2)->default(0);
                $table->decimal('total_supplier_cost', 15, 2)->default(0);
                $table->decimal('total_client_amount', 15, 2)->default(0);
                $table->decimal('gross_margin', 15, 2)->default(0);

                $table->string('status')->default('planned'); // planned, ongoing, done, canceled
                $table->integer('start_km')->nullable();
                $table->integer('end_km')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['vehicle_id', 'driver_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_missions');
    }
};
