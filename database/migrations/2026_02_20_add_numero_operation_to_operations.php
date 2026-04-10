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
        if (!Schema::hasColumn('operations', 'numero_operation')) {
            Schema::table('operations', function (Blueprint $table) {
                // Ajouter la colonne pour le numéro d'opération formaté (nullable d'abord)
                $table->string('numero_operation')->nullable()->after('id');

                // Index pour la recherche rapide
                $table->index('numero_operation');
            });

            // Mettre à jour les opérations existantes avec les nouveaux numéros
            DB::statement('UPDATE operations SET numero_operation = CONCAT("OP-", DATE_FORMAT(created_at, "%Y%m"), "-", LPAD(id, 4, "0")) WHERE numero_operation IS NULL OR numero_operation = ""');

            // Maintenant que toutes les opérations ont un numéro unique, rendre la colonne NOT NULL et UNIQUE
            Schema::table('operations', function (Blueprint $table) {
                $table->string('numero_operation')->nullable(false)->change();
                $table->unique('numero_operation', 'operations_numero_operation_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropUnique('operations_numero_operation_unique');
            $table->dropIndex(['numero_operation']);
            $table->dropColumn('numero_operation');
        });
    }
};
