<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('personnel')) {
            return;
        }

        Schema::table('personnel', function (Blueprint $table) {
            if (!Schema::hasColumn('personnel', 'camera_person_id')) {
                $table->string('camera_person_id', 100)->nullable()->after('updated_by');
                $table->index('camera_person_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('personnel') || !Schema::hasColumn('personnel', 'camera_person_id')) {
            return;
        }

        Schema::table('personnel', function (Blueprint $table) {
            $table->dropIndex(['camera_person_id']);
            $table->dropColumn('camera_person_id');
        });
    }
};
