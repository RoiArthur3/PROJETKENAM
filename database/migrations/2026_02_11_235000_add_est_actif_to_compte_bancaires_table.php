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
        if (Schema::hasTable('compte_bancaires') && !Schema::hasColumn('compte_bancaires', 'est_actif')) {
            Schema::table('compte_bancaires', function (Blueprint $table) {
                // On ajoute simplement la colonne sans contrainte de position,
                // car la structure exacte peut varier entre environnements.
                $table->boolean('est_actif')->default(true);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('compte_bancaires') && Schema::hasColumn('compte_bancaires', 'est_actif')) {
            Schema::table('compte_bancaires', function (Blueprint $table) {
                $table->dropColumn('est_actif');
            });
        }
    }
};
