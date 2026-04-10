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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'can_access_commercial')) {
                $table->boolean('can_access_commercial')->default(false)->after('modules');
            }
            if (!Schema::hasColumn('users', 'can_access_accounting')) {
                $table->boolean('can_access_accounting')->default(false)->after('can_access_commercial');
            }
            if (!Schema::hasColumn('users', 'can_access_fleet')) {
                $table->boolean('can_access_fleet')->default(false)->after('can_access_accounting');
            }
            if (!Schema::hasColumn('users', 'can_access_warehouse')) {
                $table->boolean('can_access_warehouse')->default(false)->after('can_access_fleet');
            }
            if (!Schema::hasColumn('users', 'can_access_hr')) {
                $table->boolean('can_access_hr')->default(false)->after('can_access_warehouse');
            }
            if (!Schema::hasColumn('users', 'can_access_operations')) {
                $table->boolean('can_access_operations')->default(false)->after('can_access_hr');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'can_access_commercial',
                'can_access_accounting',
                'can_access_fleet',
                'can_access_warehouse',
                'can_access_hr',
                'can_access_operations'
            ]);
        });
    }
};
