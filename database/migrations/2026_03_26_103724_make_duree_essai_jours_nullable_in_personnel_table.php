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
            // Rendre la colonne duree_essai_jours nullable
            if (Schema::hasColumn('personnel', 'duree_essai_jours')) {
                $table->integer('duree_essai_jours')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            // Revenir à NOT NULL avec une valeur par défaut
            if (Schema::hasColumn('personnel', 'duree_essai_jours')) {
                $table->integer('duree_essai_jours')->default(90)->nullable(false)->change();
            }
        });
    }
};
