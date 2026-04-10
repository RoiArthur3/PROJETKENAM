<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('factures')) {
            return;
        }

        Schema::table('factures', function (Blueprint $table) {
            if (!Schema::hasColumn('factures', 'numero_fne')) {
                $table->string('numero_fne')->nullable();
            }

            if (!Schema::hasColumn('factures', 'vehicle_mission_id')) {
                $table->unsignedBigInteger('vehicle_mission_id')->nullable()->after('client_id');
                $table->index('vehicle_mission_id', 'factures_vehicle_mission_id_index');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('factures')) {
            return;
        }

        Schema::table('factures', function (Blueprint $table) {
            if (Schema::hasColumn('factures', 'vehicle_mission_id')) {
                $table->dropIndex('factures_vehicle_mission_id_index');
                $table->dropColumn('vehicle_mission_id');
            }

            if (Schema::hasColumn('factures', 'numero_fne')) {
                $table->dropColumn('numero_fne');
            }
        });
    }
};
