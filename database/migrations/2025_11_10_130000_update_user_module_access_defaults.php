<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_access_dashboard')->default(false)->change();
            $table->boolean('can_access_operations')->default(false)->change();
            $table->boolean('can_access_hr')->default(false)->change();
            $table->boolean('can_access_fleet')->default(false)->change();
            $table->boolean('can_access_suppliers')->default(false)->change();
            $table->boolean('can_access_warehouse')->default(false)->change();
            $table->boolean('can_access_accounting')->default(false)->change();
            $table->boolean('can_access_invoicing')->default(false)->change();
            $table->boolean('can_access_reporting')->default(false)->change();
            $table->boolean('can_access_commercial')->default(false)->change();
            $table->boolean('can_access_prospection')->default(false)->change();
            $table->boolean('can_access_ateliers')->default(false)->change();
        });

        // Backfill: ensure null values are set to false (0) without changing explicit values
        DB::table('users')->whereNull('can_access_dashboard')->update(['can_access_dashboard' => 0]);
        DB::table('users')->whereNull('can_access_operations')->update(['can_access_operations' => 0]);
        DB::table('users')->whereNull('can_access_hr')->update(['can_access_hr' => 0]);
        DB::table('users')->whereNull('can_access_fleet')->update(['can_access_fleet' => 0]);
        DB::table('users')->whereNull('can_access_suppliers')->update(['can_access_suppliers' => 0]);
        DB::table('users')->whereNull('can_access_warehouse')->update(['can_access_warehouse' => 0]);
        DB::table('users')->whereNull('can_access_accounting')->update(['can_access_accounting' => 0]);
        DB::table('users')->whereNull('can_access_invoicing')->update(['can_access_invoicing' => 0]);
        DB::table('users')->whereNull('can_access_reporting')->update(['can_access_reporting' => 0]);
        DB::table('users')->whereNull('can_access_commercial')->update(['can_access_commercial' => 0]);
        DB::table('users')->whereNull('can_access_prospection')->update(['can_access_prospection' => 0]);
        DB::table('users')->whereNull('can_access_ateliers')->update(['can_access_ateliers' => 0]);
    }

    public function down(): void
    {
        // No-op: reverting defaults intentionally omitted to keep secure-by-default behavior
    }
};
