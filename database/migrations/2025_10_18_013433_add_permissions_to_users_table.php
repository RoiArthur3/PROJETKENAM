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
            // Vérifier si les colonnes existent avant de les ajouter
            if (!Schema::hasColumn('users', 'permissions')) {
                $table->json('permissions')->nullable(); // JSON array of allowed modules
            }
            if (!Schema::hasColumn('users', 'can_access_dashboard')) {
                $table->boolean('can_access_dashboard')->default(true);
            }
            if (!Schema::hasColumn('users', 'can_access_operations')) {
                $table->boolean('can_access_operations')->default(false);
            }
            if (!Schema::hasColumn('users', 'can_access_hr')) {
                $table->boolean('can_access_hr')->default(false);
            }
            if (!Schema::hasColumn('users', 'can_access_fleet')) {
                $table->boolean('can_access_fleet')->default(false);
            }
            if (!Schema::hasColumn('users', 'can_access_suppliers')) {
                $table->boolean('can_access_suppliers')->default(false);
            }
            if (!Schema::hasColumn('users', 'can_access_warehouse')) {
                $table->boolean('can_access_warehouse')->default(false);
            }
            if (!Schema::hasColumn('users', 'can_access_accounting')) {
                $table->boolean('can_access_accounting')->default(false);
            }
            if (!Schema::hasColumn('users', 'can_access_invoicing')) {
                $table->boolean('can_access_invoicing')->default(false);
            }
            if (!Schema::hasColumn('users', 'can_access_reporting')) {
                $table->boolean('can_access_reporting')->default(false);
            }
            if (!Schema::hasColumn('users', 'can_access_commercial')) {
                $table->boolean('can_access_commercial')->default(false);
            }
            if (!Schema::hasColumn('users', 'can_access_prospection')) {
                $table->boolean('can_access_prospection')->default(false);
            }
            if (!Schema::hasColumn('users', 'can_access_ateliers')) {
                $table->boolean('can_access_ateliers')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [
                'permissions',
                'can_access_dashboard',
                'can_access_operations',
                'can_access_hr',
                'can_access_fleet',
                'can_access_suppliers',
                'can_access_warehouse',
                'can_access_accounting',
                'can_access_invoicing',
                'can_access_reporting',
                'can_access_commercial',
                'can_access_prospection',
                'can_access_ateliers',
            ];

            // Vérifier si les colonnes existent avant de les supprimer
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
