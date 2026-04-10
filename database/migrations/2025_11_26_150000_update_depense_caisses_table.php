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
            Schema::create('depense_caisses', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('caisse_id')->constrained('caisses')->onDelete('restrict');
            $table->unsignedBigInteger('approvisionnement_id')->nullable();
            $table->unsignedBigInteger('type_depense_id')->nullable();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->decimal('montant', 15, 2);
            $table->string('devise', 3)->default('XOF');

            // Informations sur la dépense
            $table->date('date_depense');
            $table->enum('mode_paiement', ['especes', 'cheque', 'virement', 'carte', 'autre'])->default('especes');
            $table->string('numero_cheque')->nullable();
            $table->string('banque')->nullable();

            // Statut et validation
            $table->enum('statut', ['brouillon', 'soumis', 'valide', 'rejete', 'paye', 'annule'])->default('brouillon');
            $table->foreignId('createur_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('valideur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('date_validation')->nullable();
            $table->text('motif_rejet')->nullable();

            // Informations complémentaires
            $table->foreignId('tiers_id')->nullable()->constrained('tiers')->onDelete('set null'); // Fournisseur, client, etc.
            $table->string('tiers_type')->nullable(); // Type de tiers (fournisseur, client, etc.)
            $table->unsignedBigInteger('projet_id')->nullable()->after('tiers_type');
            $table->foreignId('compte_comptable_id')->nullable()->constrained('comptes_comptables')->onDelete('set null');

                // Pièces justificatives
                $table->json('pieces_jointes')->nullable();

                // Suivi
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Index pour les recherches fréquentes
                $table->index(['statut', 'date_depense']);
                $table->index(['caisse_id', 'approvisionnement_id']);
            });
        } else {
            // Si la table existe déjà, on ajoute les colonnes manquantes
            Schema::table('depense_caisses', function (Blueprint $table) {
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
                            $table->foreignId('tiers_id')->nullable()->constrained('tiers')->onDelete('set null')->after('motif_rejet');
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
                            $table->foreignId('compte_comptable_id')->nullable()->constrained('comptes_comptables')->onDelete('set null')->after('projet_id');
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
            });
        }

        // Index pour les recherches fréquentes
        Schema::table('depense_caisses', function (Blueprint $table) {
            if (!Schema::hasColumn('depense_caisses', 'statut') || !Schema::hasColumn('depense_caisses', 'date_depense')) {
                $table->index(['statut', 'date_depense']);
            }
            if (!Schema::hasColumn('depense_caisses', 'caisse_id') || !Schema::hasColumn('depense_caisses', 'approvisionnement_id')) {
                $table->index(['caisse_id', 'approvisionnement_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne supprimez pas la table si elle existe déjà
        // car elle pourrait être utilisée par d'autres fonctionnalités
        if (Schema::hasTable('depense_caisses')) {
            Schema::table('depense_caisses', function (Blueprint $table) {
                // Supprimer les contraintes de clé étrangère
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $tableName = $table->getTable();
                $foreignKeys = $sm->listTableForeignKeys($tableName);

                foreach ($foreignKeys as $fk) {
                    $table->dropForeign($fk->getName());
                }
            });

            Schema::dropIfExists('depense_caisses');
        }
    }
};
