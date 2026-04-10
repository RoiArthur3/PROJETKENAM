<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'can_access_treasury')) {
                $table->boolean('can_access_treasury')->default(false)->after('can_access_projects');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'can_access_treasury')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('can_access_treasury');
            });
        }
    }
};
