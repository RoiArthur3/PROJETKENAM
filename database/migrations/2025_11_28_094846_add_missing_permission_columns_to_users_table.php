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
            if (!Schema::hasColumn('users', 'can_access_projects')) {
                $table->boolean('can_access_projects')->default(false)->after('can_access_ateliers');
            }
            if (!Schema::hasColumn('users', 'can_access_audit')) {
                $table->boolean('can_access_audit')->default(false)->after('can_access_projects');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'can_access_projects')) {
                $table->dropColumn('can_access_projects');
            }
            if (Schema::hasColumn('users', 'can_access_audit')) {
                $table->dropColumn('can_access_audit');
            }
        });
    }
};
