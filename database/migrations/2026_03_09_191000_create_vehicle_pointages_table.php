<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vehicle_pointages')) {
            return;
        }

        Schema::create('vehicle_pointages', function (Blueprint $table) {
            $table->id();
            if (Schema::hasTable('vehicle_missions')) {
                $table->foreignId('vehicle_mission_id')->nullable()->constrained('vehicle_missions')->nullOnDelete();
            } else {
                $table->unsignedBigInteger('vehicle_mission_id')->nullable();
            }
            $table->foreignId('operation_id')->nullable()->constrained('operations')->nullOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicules')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date_pointage');
            $table->enum('unit_type', ['heure', 'jour'])->default('heure');
            $table->decimal('quantity', 8, 2)->default(0);
            $table->decimal('supplier_unit_cost', 15, 2)->default(0);
            $table->decimal('client_unit_price', 15, 2)->default(0);
            $table->decimal('total_supplier_cost', 15, 2)->default(0);
            $table->decimal('total_client_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['date_pointage', 'vehicle_id']);
            $table->index(['vehicle_mission_id', 'date_pointage']);
            $table->index(['operation_id', 'date_pointage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_pointages');
    }
};
