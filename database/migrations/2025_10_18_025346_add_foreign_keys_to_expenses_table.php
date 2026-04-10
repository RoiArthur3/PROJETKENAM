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
        if (! Schema::hasTable('expenses')) {
            // expenses table not present yet; skip and let the migration runner continue.
            return;
        }

        Schema::table('expenses', function (Blueprint $table) {
            // Ajouter la contrainte de clé étrangère pour operation_id (si la table operations existe)
            if (Schema::hasTable('operations')) {
                $table->foreign('operation_id')->references('id')->on('operations')->onDelete('cascade');
            }

            // La contrainte pour user_id et approuve_par est déjà définie dans la migration principale
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('expenses')) {
            return;
        }

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['operation_id']);
        });
    }
};
