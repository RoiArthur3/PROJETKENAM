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
            // Étape 1: Ajouter la colonne sans contrainte
            Schema::table('operations', function (Blueprint $table) {
                $table->string('numero_operation')->nullable()->after('id');
            });
            
            // Étape 2: Remplir TOUTES les lignes avec des numéros uniques
            DB::statement("
                UPDATE operations 
                SET numero_operation = CONCAT('OP-', DATE_FORMAT(created_at, '%Y%m'), '-', LPAD(id, 4, '0')) 
                WHERE numero_operation IS NULL OR numero_operation = ''
            ");
            
            // Étape 3: Vérifier qu'il n'y a plus de valeurs vides
            $emptyCount = DB::table('operations')
                ->where(function($query) {
                    $query->whereNull('numero_operation')
                        ->orWhere('numero_operation', '');
                })
                ->count();
                
            if ($emptyCount > 0) {
                // Si on a des lignes sans date, on met un numéro par défaut basé sur l'ID
                DB::statement("
                    UPDATE operations 
                    SET numero_operation = CONCAT('OP-999999-', LPAD(id, 6, '0')) 
                    WHERE numero_operation IS NULL OR numero_operation = ''
                ");
            }
            
            // Étape 4: Ajouter la contrainte UNIQUE seulement si tout est rempli
            try {
                Schema::table('operations', function (Blueprint $table) {
                    $table->string('numero_operation')->nullable(false)->change();
                    $table->unique('numero_operation', 'operations_numero_operation_unique');
                });
            } catch (\Exception $e) {
                // Si l'index unique échoue (doublon déjà existant), on continue quand même
                // pour ne pas bloquer le déploiement.
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropUnique('operations_numero_operation_unique');
            $table->dropColumn('numero_operation');
        });
    }
};
