<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ajouter la nouvelle colonne responsable_id
        Schema::table('services_operationnels', function (Blueprint $table) {
            $table->unsignedBigInteger('responsable_id')->nullable()->after('telephone');
        });

        // 2. Mettre à jour les données existantes
        // Note: Cette étape nécessite une logique personnalisée pour faire correspondre
        // les noms des responsables aux ID d'utilisateurs existants
        // Pour l'instant, nous allons laisser cette colonne nullable

        // 3. Ajouter la clé étrangère
        Schema::table('services_operationnels', function (Blueprint $table) {
            $table->foreign('responsable_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la clé étrangère
        Schema::table('services_operationnels', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
        });

        // Supprimer la colonne responsable_id
        Schema::table('services_operationnels', function (Blueprint $table) {
            $table->dropColumn('responsable_id');
        });
    }
};
