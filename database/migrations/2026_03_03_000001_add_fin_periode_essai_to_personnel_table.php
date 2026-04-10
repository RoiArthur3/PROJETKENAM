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
        Schema::table('personnel', function (Blueprint $table) {
            if (!Schema::hasColumn('personnel', 'fin_periode_essai')) {
                $table->date('fin_periode_essai')->nullable()->after('duree_essai_jours');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            if (Schema::hasColumn('personnel', 'fin_periode_essai')) {
                $table->dropColumn('fin_periode_essai');
            }
        });
    }
};
