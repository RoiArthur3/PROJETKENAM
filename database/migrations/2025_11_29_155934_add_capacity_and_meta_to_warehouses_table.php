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
        Schema::table('warehouses', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouses', 'capacity')) {
                $table->decimal('capacity', 15, 2)->nullable();
            }

            if (!Schema::hasColumn('warehouses', 'country')) {
                $table->string('country', 100)->nullable();
            }

            if (!Schema::hasColumn('warehouses', 'email')) {
                $table->string('email', 255)->nullable();
            }

            if (!Schema::hasColumn('warehouses', 'opening_hours')) {
                $table->string('opening_hours', 255)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            if (Schema::hasColumn('warehouses', 'opening_hours')) {
                $table->dropColumn('opening_hours');
            }
            if (Schema::hasColumn('warehouses', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('warehouses', 'country')) {
                $table->dropColumn('country');
            }
            if (Schema::hasColumn('warehouses', 'capacity')) {
                $table->dropColumn('capacity');
            }
        });
    }
};
