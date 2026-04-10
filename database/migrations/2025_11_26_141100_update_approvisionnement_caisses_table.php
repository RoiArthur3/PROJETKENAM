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
        if (!Schema::hasTable('approvisionnement_caisses')) {
            return;
        }

        Schema::table('approvisionnement_caisses', function (Blueprint $table) {
            // Vérification et ajout des colonnes manquantes
            $columnsToAdd = [
                'numero_operation' => function() use ($table) {
                    if (!Schema::hasColumn('approvisionnement_caisses', 'numero_operation')) {
                        $table->string('numero_operation')->unique()->after('id');
                    }
                },
                'caisse_source_id' => function() use ($table) {
                    if (!Schema::hasColumn('approvisionnement_caisses', 'caisse_source_id')) {
                        $table->foreignId('caisse_source_id')->constrained('caisses')->onDelete('restrict')->after('numero_operation');
                    }
                },
                // Ajoutez d'autres colonnes de la même manière
            ];

            foreach ($columnsToAdd as $column => $callback) {
                $callback();
            }

            // Vérifier que la colonne statut existe avant d'ajouter l'index
            if (Schema::hasColumn('approvisionnement_caisses', 'statut') &&
                Schema::hasColumn('approvisionnement_caisses', 'date_validation') &&
                !Schema::hasIndex('approvisionnement_caisses', ['statut', 'date_validation'])) {
                $table->index(['statut', 'date_validation']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration ne doit pas être annulée car elle modifie une table existante
    }
};
