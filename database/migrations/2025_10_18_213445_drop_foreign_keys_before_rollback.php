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
        if (!Schema::hasTable('controle_service') || !Schema::hasColumn('controle_service', 'controle_id')) {
            return;
        }
        Schema::table('controle_service', function (Blueprint $table) {
            try {
                $table->dropForeign(['controle_id']);
            } catch (\Throwable $e) {
                // Ignore if constraint doesn't exist (e.g., SQLite)
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('controle_service') || !Schema::hasColumn('controle_service', 'controle_id')) {
            return;
        }
        Schema::table('controle_service', function (Blueprint $table) {
            try {
                $table->foreign('controle_id')
                      ->references('id')
                      ->on('controles')
                      ->onDelete('cascade');
            } catch (\Throwable $e) {
                // Ignore on drivers without FK alteration support
            }
        });
    }
};
