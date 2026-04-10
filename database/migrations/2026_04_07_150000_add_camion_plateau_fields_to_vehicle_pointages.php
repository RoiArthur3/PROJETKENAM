<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['vehicle_pointages', 'pointages'] as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'submodule')) {
                    $table->string('submodule', 50)->default('engin');
                }
                if (!Schema::hasColumn($tableName, 'billing_mode')) {
                    $table->string('billing_mode', 50)->default('standard');
                }
                if (!Schema::hasColumn($tableName, 'task_label')) {
                    $table->string('task_label')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'trip_count')) {
                    $table->decimal('trip_count', 8, 2)->default(0);
                }
                if (!Schema::hasColumn($tableName, 'monthly_trip_threshold')) {
                    $table->decimal('monthly_trip_threshold', 8, 2)->default(0);
                }
                if (!Schema::hasColumn($tableName, 'monthly_flat_rate')) {
                    $table->decimal('monthly_flat_rate', 15, 2)->default(0);
                }
                if (!Schema::hasColumn($tableName, 'extra_trip_unit_price')) {
                    $table->decimal('extra_trip_unit_price', 15, 2)->default(0);
                }
                if (!Schema::hasColumn($tableName, 'departure_location')) {
                    $table->string('departure_location')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'arrival_location')) {
                    $table->string('arrival_location')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'distance_km')) {
                    $table->decimal('distance_km', 10, 2)->default(0);
                }
                if (!Schema::hasColumn($tableName, 'fuel_amount')) {
                    $table->decimal('fuel_amount', 15, 2)->default(0);
                }
                if (!Schema::hasColumn($tableName, 'delivery_note_number')) {
                    $table->string('delivery_note_number')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'road_fees')) {
                    $table->decimal('road_fees', 15, 2)->default(0);
                }
                if (!Schema::hasColumn($tableName, 'toll_fees')) {
                    $table->decimal('toll_fees', 15, 2)->default(0);
                }
                if (!Schema::hasColumn($tableName, 'other_fees')) {
                    $table->decimal('other_fees', 15, 2)->default(0);
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['vehicle_pointages', 'pointages'] as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $columns = [
                    'submodule',
                    'billing_mode',
                    'task_label',
                    'trip_count',
                    'monthly_trip_threshold',
                    'monthly_flat_rate',
                    'extra_trip_unit_price',
                    'departure_location',
                    'arrival_location',
                    'distance_km',
                    'fuel_amount',
                    'delivery_note_number',
                    'road_fees',
                    'toll_fees',
                    'other_fees',
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};