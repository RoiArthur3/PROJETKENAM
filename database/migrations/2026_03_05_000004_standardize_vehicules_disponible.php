<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Standardize Vehicules table - ensure disponible boolean column exists
     * and optionally add statut_detail for future fine-grained status tracking
     */
    public function up(): void
    {
        if (Schema::hasTable('vehicules')) {
            // Vérifier si la colonne disponible n'existe pas
            if (!Schema::hasColumn('vehicules', 'disponible')) {
                Schema::table('vehicules', function (Blueprint $table) {
                    $table->boolean('disponible')->default(true)->after('immatriculation');
                });
            }

            // Ajouter statut_detail pour un meilleur suivi des états (optionnel, pour amélioration future)
            if (!Schema::hasColumn('vehicules', 'statut_detail')) {
                Schema::table('vehicules', function (Blueprint $table) {
                    $table->enum('statut_detail', [
                        'disponible',
                        'en_mission',
                        'en_maintenance',
                        'en_reparation',
                        'accident',
                        'retire_du_service'
                    ])->default('disponible')->after('disponible');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('vehicules')) {
            Schema::table('vehicules', function (Blueprint $table) {
                if (Schema::hasColumn('vehicules', 'statut_detail')) {
                    $table->dropColumn('statut_detail');
                }
                if (Schema::hasColumn('vehicules', 'disponible')) {
                    $table->dropColumn('disponible');
                }
            });
        }
    }
};
