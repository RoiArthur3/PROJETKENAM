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
        Schema::table('checkings', function (Blueprint $table) {
            if (!Schema::hasColumn('checkings', 'checkable_type')) {
                $table->string('checkable_type')->nullable()->after('checklist_id');
            }
            if (!Schema::hasColumn('checkings', 'checkable_id')) {
                $table->unsignedBigInteger('checkable_id')->nullable()->after('checkable_type');
            }

            if (Schema::hasColumn('checkings', 'checkable_type') && Schema::hasColumn('checkings', 'checkable_id')) {
                $table->index(['checkable_type', 'checkable_id'], 'checkings_checkable_type_id_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkings', function (Blueprint $table) {
            if (Schema::hasColumn('checkings', 'checkable_type') && Schema::hasColumn('checkings', 'checkable_id')) {
                try {
                    $table->dropIndex('checkings_checkable_type_id_index');
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            if (Schema::hasColumn('checkings', 'checkable_id')) {
                $table->dropColumn('checkable_id');
            }
            if (Schema::hasColumn('checkings', 'checkable_type')) {
                $table->dropColumn('checkable_type');
            }
        });
    }
};
