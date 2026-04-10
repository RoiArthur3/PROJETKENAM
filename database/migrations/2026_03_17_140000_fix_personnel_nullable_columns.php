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
            // Rendre les colonnes problématiques nullable
            $table->string('lien_parente', 50)->nullable()->change();
            $table->string('groupe_sanguin', 3)->nullable()->change();
            $table->string('banque', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            $table->string('lien_parente', 50)->nullable(false)->change();
            $table->enum('groupe_sanguin', ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])->nullable(false)->change();
            $table->string('banque', 100)->nullable(false)->change();
        });
    }
};
