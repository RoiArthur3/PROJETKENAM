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
        Schema::table('operation_status_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('operation_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_status_logs', function (Blueprint $table) {
            $table->dropForeignId(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
