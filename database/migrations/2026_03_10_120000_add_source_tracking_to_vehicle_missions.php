<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_missions', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicle_missions', 'source_type')) {
                $table->string('source_type')->nullable()->after('reference');
            }

            if (!Schema::hasColumn('vehicle_missions', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            }

            if (!Schema::hasColumn('vehicle_missions', 'source_reference')) {
                $table->string('source_reference')->nullable()->after('source_id');
            }
        });

        Schema::table('vehicle_missions', function (Blueprint $table) {
            $table->index(['source_type', 'source_id'], 'vehicle_missions_source_index');
            $table->index('source_reference', 'vehicle_missions_source_reference_index');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_missions', function (Blueprint $table) {
            $table->dropIndex('vehicle_missions_source_index');
            $table->dropIndex('vehicle_missions_source_reference_index');

            if (Schema::hasColumn('vehicle_missions', 'source_reference')) {
                $table->dropColumn('source_reference');
            }

            if (Schema::hasColumn('vehicle_missions', 'source_id')) {
                $table->dropColumn('source_id');
            }

            if (Schema::hasColumn('vehicle_missions', 'source_type')) {
                $table->dropColumn('source_type');
            }
        });
    }
};