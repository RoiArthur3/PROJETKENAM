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
        if (!Schema::hasTable('users') || Schema::hasColumn('users', 'password')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $column = $table->string('password')->nullable();

            if (Schema::hasColumn('users', 'email')) {
                $column->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'password')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
};
