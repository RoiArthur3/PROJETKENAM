<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('approvisionnement_caisses') || !Schema::hasColumn('approvisionnement_caisses', 'caisse_source_id')) {
            return;
        }

        Schema::table('approvisionnement_caisses', function (Blueprint $table) {
            $table->unsignedBigInteger('caisse_source_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Not reversible safely
    }
};
