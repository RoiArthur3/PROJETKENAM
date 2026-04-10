<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Améliorer la table projects existante
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                // Ajouter les colonnes manquantes si elles n'existent pas
                if (!Schema::hasColumn('projects', 'client_id')) {
                    $table->unsignedBigInteger('client_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('projects', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('client_id');
                }
                if (!Schema::hasColumn('projects', 'responsable_id')) {
                    $table->unsignedBigInteger('responsable_id')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('projects', 'type')) {
                    $table->enum('type', ['transport', 'location', 'chantier', 'livraison_reguliere', 'autre'])->default('autre')->after('nom');
                }
                if (!Schema::hasColumn('projects', 'budget_estime')) {
                    $table->decimal('budget_estime', 15, 2)->nullable()->after('description');
                }
                if (!Schema::hasColumn('projects', 'budget_reel')) {
                    $table->decimal('budget_reel', 15, 2)->default(0)->after('budget_estime');
                }
                if (!Schema::hasColumn('projects', 'date_debut')) {
                    $table->date('date_debut')->nullable()->after('budget_reel');
                }
                if (!Schema::hasColumn('projects', 'date_fin_prevue')) {
                    $table->date('date_fin_prevue')->nullable()->after('date_debut');
                }
                if (!Schema::hasColumn('projects', 'date_fin_reelle')) {
                    $table->date('date_fin_reelle')->nullable()->after('date_fin_prevue');
                }
                if (!Schema::hasColumn('projects', 'pourcentage_avancement')) {
                    $table->integer('pourcentage_avancement')->default(0)->after('date_fin_reelle');
                }
                if (!Schema::hasColumn('projects', 'statut')) {
                    // La colonne existe déjà, on peut la modifier
                    $table->enum('statut', ['brouillon', 'valide', 'en_cours', 'termine', 'clotured'])->change();
                } else {
                    // Si elle existe mais n'est pas enum
                    try {
                        $table->enum('statut', ['brouillon', 'valide', 'en_cours', 'termine', 'clotured'])->change();
                    } catch (\Exception $e) {
                        // Ignorer si on ne peut pas la changer
                    }
                }
                if (!Schema::hasColumn('projects', 'notes')) {
                    $table->text('notes')->nullable()->after('statut');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Ne pas supprimer la table, juste les colonnes ajoutées
            $columnsToRemove = ['client_id', 'responsable_id', 'type', 'budget_estime', 'budget_reel', 
                                'date_debut', 'date_fin_prevue', 'date_fin_reelle', 'pourcentage_avancement', 'notes'];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
