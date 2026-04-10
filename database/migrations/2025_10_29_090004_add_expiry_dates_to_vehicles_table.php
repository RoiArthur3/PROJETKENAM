<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'assurance_expiry')) {
                $table->date('assurance_expiry')->nullable()->after('date_achat');
            }
            if (!Schema::hasColumn('vehicles', 'visite_tech_expiry')) {
                $table->date('visite_tech_expiry')->nullable()->after('assurance_expiry');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'assurance_expiry')) {
                $table->dropColumn('assurance_expiry');
            }
            if (Schema::hasColumn('vehicles', 'visite_tech_expiry')) {
                $table->dropColumn('visite_tech_expiry');
            }
        });
    }
};
