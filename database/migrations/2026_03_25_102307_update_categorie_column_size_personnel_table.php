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
            // Augmenter la taille de la colonne categorie pour éviter "string data, right truncated"
            if (Schema::hasColumn('personnel', 'categorie')) {
                $table->string('categorie', 100)->change();
            }

            // Augmenter aussi la taille du service pour être cohérent
            if (Schema::hasColumn('personnel', 'service')) {
                $table->string('service', 255)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            // Revenir aux tailles d'origine
            if (Schema::hasColumn('personnel', 'categorie')) {
                $table->string('categorie', 50)->change();
            }

            if (Schema::hasColumn('personnel', 'service')) {
                $table->string('service', 100)->change();
            }
        });
    }
};
