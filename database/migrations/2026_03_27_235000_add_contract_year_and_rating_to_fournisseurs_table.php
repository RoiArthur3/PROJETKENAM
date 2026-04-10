<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fournisseurs', function (Blueprint $table) {
            if (!Schema::hasColumn('fournisseurs', 'annee_contractuelle')) {
                $table->unsignedSmallInteger('annee_contractuelle')->nullable()->after('condition_reglement');
            }

            if (!Schema::hasColumn('fournisseurs', 'note_appreciation')) {
                $table->string('note_appreciation', 30)->nullable()->after('annee_contractuelle');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fournisseurs', function (Blueprint $table) {
            if (Schema::hasColumn('fournisseurs', 'note_appreciation')) {
                $table->dropColumn('note_appreciation');
            }

            if (Schema::hasColumn('fournisseurs', 'annee_contractuelle')) {
                $table->dropColumn('annee_contractuelle');
            }
        });
    }
};
