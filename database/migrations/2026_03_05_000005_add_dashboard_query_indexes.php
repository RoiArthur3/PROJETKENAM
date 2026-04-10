<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add indexes for better dashboard query performance
     */
    public function up(): void
    {
        // Index sur factures.statut
        if (Schema::hasTable('factures')) {
            try {
                Schema::table('factures', function (Blueprint $table) {
                    $table->index('statut');
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }

        // Index sur personnel.statut et fin_periode_essai
        if (Schema::hasTable('personnel')) {
            try {
                Schema::table('personnel', function (Blueprint $table) {
                    $table->index(['statut', 'fin_periode_essai']);
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }

        // Index sur fournisseurs.est_actif
        if (Schema::hasTable('fournisseurs')) {
            try {
                Schema::table('fournisseurs', function (Blueprint $table) {
                    $table->index('est_actif');
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }

        // Index sur vehicules.disponible
        if (Schema::hasTable('vehicules')) {
            try {
                Schema::table('vehicules', function (Blueprint $table) {
                    $table->index('disponible');
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }

        // Index sur operations.statut_courant
        if (Schema::hasTable('operations')) {
            try {
                Schema::table('operations', function (Blueprint $table) {
                    $table->index('statut_courant');
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('factures')) {
            try {
                Schema::table('factures', function (Blueprint $table) {
                    $table->dropIndex('factures_statut_index');
                });
            } catch (\Exception $e) {
                // Index might not exist
            }
        }

        if (Schema::hasTable('personnel')) {
            try {
                Schema::table('personnel', function (Blueprint $table) {
                    $table->dropIndex('personnel_statut_fin_periode_essai_index');
                });
            } catch (\Exception $e) {
                // Index might not exist
            }
        }

        if (Schema::hasTable('fournisseurs')) {
            try {
                Schema::table('fournisseurs', function (Blueprint $table) {
                    $table->dropIndex('fournisseurs_est_actif_index');
                });
            } catch (\Exception $e) {
                // Index might not exist
            }
        }

        if (Schema::hasTable('vehicules')) {
            try {
                Schema::table('vehicules', function (Blueprint $table) {
                    $table->dropIndex('vehicules_disponible_index');
                });
            } catch (\Exception $e) {
                // Index might not exist
            }
        }

        if (Schema::hasTable('operations')) {
            try {
                Schema::table('operations', function (Blueprint $table) {
                    $table->dropIndex('operations_statut_courant_index');
                });
            } catch (\Exception $e) {
                // Index might not exist
            }
        }
    }
};
