<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('clients') || Schema::hasColumn('clients', 'compte_comptable_id')) {
            return;
        }

        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasTable('comptes_comptables')) {
                $table->foreignId('compte_comptable_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('comptes_comptables')
                    ->nullOnDelete();
            } else {
                $table->unsignedBigInteger('compte_comptable_id')
                    ->nullable()
                    ->after('id');
                $table->index('compte_comptable_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('clients') || !Schema::hasColumn('clients', 'compte_comptable_id')) {
            return;
        }

        Schema::table('clients', function (Blueprint $table) {
            try {
                $table->dropForeign(['compte_comptable_id']);
            } catch (\Throwable $e) {
                // noop
            }

            try {
                $table->dropIndex(['compte_comptable_id']);
            } catch (\Throwable $e) {
                // noop
            }

            $table->dropColumn('compte_comptable_id');
        });
    }
};