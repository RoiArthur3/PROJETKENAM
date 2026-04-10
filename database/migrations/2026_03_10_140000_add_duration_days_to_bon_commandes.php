<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bon_commandes')) {
            return;
        }

        Schema::table('bon_commandes', function (Blueprint $table) {
            if (!Schema::hasColumn('bon_commandes', 'duration_days')) {
                $table->unsignedInteger('duration_days')->nullable()->after('date_livraison_prevue');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('bon_commandes')) {
            return;
        }

        Schema::table('bon_commandes', function (Blueprint $table) {
            if (Schema::hasColumn('bon_commandes', 'duration_days')) {
                $table->dropColumn('duration_days');
            }
        });
    }
};
