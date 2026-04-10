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
        Schema::table('devis', function (Blueprint $table) {
            // Ajouter la colonne reference si elle n'existe pas
            if (!Schema::hasColumn('devis', 'reference')) {
                $table->string('reference')->nullable()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            if (Schema::hasColumn('devis', 'reference')) {
                $table->dropColumn('reference');
            }
        });
    }
};
