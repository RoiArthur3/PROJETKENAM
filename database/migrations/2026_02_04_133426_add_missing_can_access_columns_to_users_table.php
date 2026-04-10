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
            // Ajouter les colonnes manquantes
            if (!Schema::hasColumn('users', 'can_access_validations')) {
                $table->boolean('can_access_validations')->default(false)->after('can_access_operations');
            }

            // Ajouter les autres colonnes manquantes si nécessaire
            $missingColumns = [
                'can_access_projects' => 'can_access_ateliers',
                'can_access_treasury' => 'can_access_ateliers',
                'can_access_audit' => 'can_access_ateliers',
                'can_access_services' => 'can_access_ateliers',
                'can_access_comptes' => 'can_access_ateliers',
                'can_access_system' => 'can_access_ateliers',
            ];

            foreach ($missingColumns as $column => $afterColumn) {
                if (!Schema::hasColumn('users', $column)) {
                    $table->boolean($column)->default(false)->after($afterColumn);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'can_access_validations',
                'can_access_projects',
                'can_access_treasury',
                'can_access_audit',
                'can_access_services',
                'can_access_comptes',
                'can_access_system',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
