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
        if (!Schema::hasTable('mouvement_caisses')) {
            Schema::create('mouvement_caisses', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('caisse_id')->constrained('caisses')->onDelete('restrict');

            // Informations sur le mouvement
            $table->enum('type_mouvement', ['entree', 'sortie', 'transfert_entrant', 'transfert_sortant']);
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->decimal('montant', 15, 2);
            $table->string('devise', 3)->default('XOF');
            $table->date('date_mouvement');

            // Référence à l'opération liée (approvisionnement, dépense, etc.)
            $table->string('source_type')->nullable(); // 'App\Models\ApprovisionnementCaisse', 'App\Models\DepenseCaisse', etc.
            $table->unsignedBigInteger('source_id')->nullable();

            // Pour les transferts entre caisses
            $table->foreignId('caisse_destination_id')->nullable()->constrained('caisses')->onDelete('set null');
            $table->foreignId('transfert_id')->nullable()->constrained('mouvement_caisses')->onDelete('set null');

            // Informations de suivi
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            // Écriture comptable associée

                // Statut et validation
                $table->enum('statut', ['brouillon', 'comptabilise', 'annule'])->default('brouillon');
                $table->text('notes')->nullable();

                $table->timestamps();
                $table->softDeletes();

                // Index pour les recherches fréquentes
                $table->index(['caisse_id', 'type_mouvement', 'date_mouvement']);
                $table->index(['source_type', 'source_id']);
            });
        } else {
            // Si la table existe déjà, on ajoute les colonnes manquantes
            Schema::table('mouvement_caisses', function (Blueprint $table) {
                $columnsToAdd = [
                    'reference' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'reference')) {
                            $table->string('reference')->unique()->after('id');
                        }
                    },
                    'caisse_id' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'caisse_id')) {
                            $table->foreignId('caisse_id')->constrained('caisses')->onDelete('restrict')->after('reference');
                        }
                    },
                    'type_mouvement' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'type_mouvement')) {
                            $table->enum('type_mouvement', ['entree', 'sortie', 'transfert_entrant', 'transfert_sortant'])->after('caisse_id');
                        }
                    },
                    'libelle' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'libelle')) {
                            $table->string('libelle')->after('type_mouvement');
                        }
                    },
                    'description' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'description')) {
                            $table->text('description')->nullable()->after('libelle');
                        }
                    },
                    'montant' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'montant')) {
                            $table->decimal('montant', 15, 2)->after('description');
                        }
                    },
                    'devise' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'devise')) {
                            $table->string('devise', 3)->default('XOF')->after('montant');
                        }
                    },
                    'date_mouvement' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'date_mouvement')) {
                            $table->date('date_mouvement')->after('devise');
                        }
                    },
                    'source_type' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'source_type')) {
                            $table->string('source_type')->nullable()->after('date_mouvement');
                        }
                    },
                    'source_id' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'source_id')) {
                            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
                        }
                    },
                    'caisse_destination_id' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'caisse_destination_id')) {
                            $table->foreignId('caisse_destination_id')->nullable()->constrained('caisses')->onDelete('set null')->after('source_id');
                        }
                    },
                    'transfert_id' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'transfert_id')) {
                            $table->foreignId('transfert_id')->nullable()->constrained('mouvement_caisses')->onDelete('set null')->after('caisse_destination_id');
                        }
                    },
                    'created_by' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'created_by')) {
                            $table->foreignId('created_by')->constrained('users')->onDelete('restrict')->after('transfert_id');
                        }
                    },
                    'updated_by' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'updated_by')) {
                            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->after('created_by');
                        }
                    },
                    'ecriture_comptable_id' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'ecriture_comptable_id')) {
                            $table->foreignId('ecriture_comptable_id')->nullable()->constrained('ecritures_comptables')->onDelete('set null')->after('updated_by');
                        }
                    },
                    'statut' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'statut')) {
                            $table->enum('statut', ['brouillon', 'comptabilise', 'annule'])->default('brouillon')->after('ecriture_comptable_id');
                        }
                    },
                    'notes' => function() use ($table) {
                        if (!Schema::hasColumn('mouvement_caisses', 'notes')) {
                            $table->text('notes')->nullable()->after('statut');
                        }
                    },
                ];

                foreach ($columnsToAdd as $column => $callback) {
                    $callback();
                }

                // Ajout de la suppression logique si elle n'existe pas
                if (!Schema::hasColumn('mouvement_caisses', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        // Index pour les recherches fréquentes
        Schema::table('mouvement_caisses', function (Blueprint $table) {
            if (!Schema::hasColumn('mouvement_caisses', 'caisse_id')) {
                $table->index(['caisse_id', 'type_mouvement', 'date_mouvement']);
            }
            if (!Schema::hasColumn('mouvement_caisses', 'source_type')) {
                $table->index(['source_type', 'source_id']);
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
        if (Schema::hasTable('mouvement_caisses')) {
            Schema::table('mouvement_caisses', function (Blueprint $table) {
                // Supprimer les contraintes de clé étrangère
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $tableName = $table->getTable();
                $foreignKeys = $sm->listTableForeignKeys($tableName);

                foreach ($foreignKeys as $fk) {
                    $table->dropForeign($fk->getName());
                }
            });

            Schema::dropIfExists('mouvement_caisses');
        }
    }
};
