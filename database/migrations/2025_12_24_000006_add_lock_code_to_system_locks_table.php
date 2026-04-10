<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('system_locks', 'lock_code')) {
            Schema::table('system_locks', function (Blueprint $table) {
                $table->string('lock_code', 32)->nullable()->after('reason');
                $table->timestamp('code_expires_at')->nullable()->after('lock_code');
                $table->index('lock_code');
                $table->index('code_expires_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('system_locks', function (Blueprint $table) {
            $table->dropIndex(['lock_code']);
            $table->dropIndex(['code_expires_at']);
            $table->dropColumn(['lock_code', 'code_expires_at']);
        });
    }
};
