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
        Schema::table('validation_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('validation_logs', 'validated_by')) {
                $table->string('validated_by')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('validation_logs', 'validation_date')) {
                $table->timestamp('validation_date')->nullable()->after('validated_by');
            }
            if (!Schema::hasColumn('validation_logs', 'decision')) {
                $table->enum('decision', ['approve', 'reject', 'correct'])->nullable()->after('action');
            }
            if (!Schema::hasColumn('validation_logs', 'processing_time_hours')) {
                $table->decimal('processing_time_hours', 8, 2)->nullable()->after('nouveau_statut');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('validation_logs', function (Blueprint $table) {
            $table->dropColumn(['validated_by', 'validation_date', 'decision', 'processing_time_hours']);
        });
    }
};
