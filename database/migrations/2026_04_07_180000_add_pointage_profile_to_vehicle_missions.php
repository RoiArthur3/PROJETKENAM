<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vehicle_missions')) {
            return;
        }

        Schema::table('vehicle_missions', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicle_missions', 'pointage_submodule')) {
                $table->string('pointage_submodule', 50)->default('engin')->after('source_reference');
            }

            if (!Schema::hasColumn('vehicle_missions', 'billing_mode')) {
                $table->string('billing_mode', 50)->default('standard')->after('pointage_submodule');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('vehicle_missions')) {
            return;
        }

        Schema::table('vehicle_missions', function (Blueprint $table) {
            if (Schema::hasColumn('vehicle_missions', 'billing_mode')) {
                $table->dropColumn('billing_mode');
            }

            if (Schema::hasColumn('vehicle_missions', 'pointage_submodule')) {
                $table->dropColumn('pointage_submodule');
            }
        });
    }
};
