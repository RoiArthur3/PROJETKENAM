<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('operations')) {
            return;
        }

        Schema::table('operations', function (Blueprint $table) {
            if (! Schema::hasColumn('operations', 'service')) {
                $table->string('service')->nullable();
            }
            if (! Schema::hasColumn('operations', 'responsable_name')) {
                $table->string('responsable_name')->nullable();
            }
            if (! Schema::hasColumn('operations', 'responsable_email')) {
                $table->string('responsable_email')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropColumn(['service', 'responsable_name', 'responsable_email']);
        });
    }
};
