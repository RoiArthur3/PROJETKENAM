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
        // Correction pour operation_costs
        Schema::table('operation_costs', function (Blueprint $table) {
            if (Schema::hasColumn('operation_costs', 'description')) {
                $table->text('description')->nullable()->change();
            }
        });

        // Correction pour operations
        Schema::table('operations', function (Blueprint $table) {
            if (Schema::hasColumn('operations', 'description')) {
                $table->text('description')->nullable()->change();
            }
        });

        // Correction pour project_expenses
        Schema::table('project_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('project_expenses', 'description')) {
                $table->text('description')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir pour operation_costs
        Schema::table('operation_costs', function (Blueprint $table) {
            if (Schema::hasColumn('operation_costs', 'description')) {
                $table->string('description')->nullable(false)->change();
            }
        });

        // Revenir pour operations
        Schema::table('operations', function (Blueprint $table) {
            if (Schema::hasColumn('operations', 'description')) {
                $table->string('description')->nullable(false)->change();
            }
        });

        // Revenir pour project_expenses
        Schema::table('project_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('project_expenses', 'description')) {
                $table->string('description')->nullable(false)->change();
            }
        });
    }
};
