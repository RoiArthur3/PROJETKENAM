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
        if (!Schema::hasTable('depense_caisses')) {
            return;
        }

        Schema::table('depense_caisses', function (Blueprint $table) {
            // Vérification et ajout des colonnes manquantes
            $columnsToAdd = [
                'reference' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'reference')) {
                        $table->string('reference')->after('id');
                    }
                },
                'caisse_id' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'caisse_id')) {
                        $table->foreignId('caisse_id')->constrained('caisses')->onDelete('restrict')->after('reference');
                    }
                },
                'approvisionnement_id' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'approvisionnement_id')) {
                        $table->unsignedBigInteger('approvisionnement_id')->nullable()->after('caisse_id');
                    }
                },
                'type_depense_id' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'type_depense_id')) {
                        $table->unsignedBigInteger('type_depense_id')->nullable()->after('approvisionnement_id');
                    }
                },
                'libelle' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'libelle')) {
                        $table->string('libelle')->after('type_depense_id');
                    }
                },
                'description' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'description')) {
                        $table->text('description')->nullable()->after('libelle');
                    }
                },
                'montant' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'montant')) {
                        $table->decimal('montant', 15, 2)->after('description');
                    }
                },
                'devise' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'devise')) {
                        $table->string('devise', 3)->default('XOF')->after('montant');
                    }
                },
                'date_depense' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'date_depense')) {
                        $table->date('date_depense')->after('devise');
                    }
                },
                'mode_paiement' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'mode_paiement')) {
                        $table->enum('mode_paiement', ['especes', 'cheque', 'virement', 'carte', 'autre'])->default('especes')->after('date_depense');
                    }
                },
                'numero_cheque' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'numero_cheque')) {
                        $table->string('numero_cheque')->nullable()->after('mode_paiement');
                    }
                },
                'banque' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'banque')) {
                        $table->string('banque')->nullable()->after('numero_cheque');
                    }
                },
                'statut' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'statut')) {
                        $table->enum('statut', ['brouillon', 'soumis', 'valide', 'rejete', 'paye', 'annule'])->default('brouillon')->after('banque');
                    }
                },
                'createur_id' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'createur_id')) {
                        $table->foreignId('createur_id')->constrained('users')->onDelete('restrict')->after('statut');
                    }
                },
                'valideur_id' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'valideur_id')) {
                        $table->foreignId('valideur_id')->nullable()->constrained('users')->onDelete('set null')->after('createur_id');
                    }
                },
                'date_validation' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'date_validation')) {
                        $table->dateTime('date_validation')->nullable()->after('valideur_id');
                    }
                },
                'motif_rejet' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'motif_rejet')) {
                        $table->text('motif_rejet')->nullable()->after('date_validation');
                    }
                },
                'tiers_id' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'tiers_id')) {
                        $table->unsignedBigInteger('tiers_id')->nullable()->after('motif_rejet');
                    }
                },
                'tiers_type' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'tiers_type')) {
                        $table->string('tiers_type')->nullable()->after('tiers_id');
                    }
                },
                'projet_id' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'projet_id')) {
                        $table->unsignedBigInteger('projet_id')->nullable()->after('tiers_type');
                    }
                },
                'compte_comptable_id' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'compte_comptable_id')) {
                        if (Schema::hasTable('comptes_comptables')) {
                            $table->unsignedBigInteger('compte_comptable_id')->nullable()->after('projet_id');
                            // Ajouter la contrainte plus tard si nécessaire
                        } else {
                            $table->unsignedBigInteger('compte_comptable_id')->nullable()->after('projet_id');
                        }
                    }
                },
                'pieces_jointes' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'pieces_jointes')) {
                        $table->json('pieces_jointes')->nullable()->after('compte_comptable_id');
                    }
                },
                'notes' => function() use ($table) {
                    if (!Schema::hasColumn('depense_caisses', 'notes')) {
                        $table->text('notes')->nullable()->after('pieces_jointes');
                    }
                },
            ];

            foreach ($columnsToAdd as $column => $callback) {
                $callback();
            }

            // Ajout de la suppression logique si elle n'existe pas
            if (!Schema::hasColumn('depense_caisses', 'deleted_at')) {
                $table->softDeletes();
            }

            // Ajout des index manquants
            if (!Schema::hasIndex('depense_caisses', ['statut', 'date_validation'])) {
                $table->index(['statut', 'date_validation']);
            }
            if (!Schema::hasIndex('depense_caisses', ['date_depense'])) {
                $table->index('date_depense');
            }
            if (!Schema::hasIndex('depense_caisses', ['mode_paiement'])) {
                $table->index('mode_paiement');
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
