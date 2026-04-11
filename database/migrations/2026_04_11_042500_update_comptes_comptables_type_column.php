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
        Schema::table('comptes_comptables', function (Blueprint $table) {
            $table->string('type', 50)->change(); // Augmenter la taille du champ type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comptes_comptables', function (Blueprint $table) {
            $table->string('type', 20)->change(); // Revenir à la taille originale
        });
    }
};
