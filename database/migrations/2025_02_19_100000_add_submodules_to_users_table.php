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
        if (!Schema::hasTable('users') || Schema::hasColumn('users', 'submodules')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $column = $table->json('submodules')->nullable()->comment('Sous-modules autorisés pour les modérateurs');

            if (Schema::hasColumn('users', 'modules')) {
                $column->after('modules');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'submodules')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('submodules');
        });
    }
};
