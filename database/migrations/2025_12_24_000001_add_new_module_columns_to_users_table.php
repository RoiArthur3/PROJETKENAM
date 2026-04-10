<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ajouter les colonnes pour les nouveaux modules
            $modules = ['services', 'comptes', 'system'];

            foreach ($modules as $module) {
                $columnName = "can_access_{$module}";
                if (!Schema::hasColumn('users', $columnName)) {
                    $table->boolean($columnName)->default(false);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $modules = ['services', 'comptes', 'system'];

            foreach ($modules as $module) {
                $columnName = "can_access_{$module}";
                if (Schema::hasColumn('users', $columnName)) {
                    $table->dropColumn($columnName);
                }
            }
        });
    }
};
