<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('operations')) return;
        Schema::table('operations', function (Blueprint $table) {
            if (!Schema::hasColumn('operations', 'operational_service_id')) {
                $table->foreignId('operational_service_id')->nullable()->constrained('operational_services')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('operations')) return;
        Schema::table('operations', function (Blueprint $table) {
            if (Schema::hasColumn('operations', 'operational_service_id')) {
                $table->dropConstrainedForeignId('operational_service_id');
            }
        });
    }
};
