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
        if (!Schema::hasTable('justificatif_depenses')) {
            Schema::create('justificatif_depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depense_id')->constrained('depense_caisses')->onDelete('cascade');
            $table->string('nom_fichier');
            $table->string('chemin_fichier');
            $table->string('type_mime');
            $table->unsignedBigInteger('taille'); // Taille en octets
            $table->string('extension', 10);

            // Métadonnées
            $table->string('titre')->nullable();
            $table->text('description')->nullable();
            $table->string('categorie')->nullable(); // Facture, bon de commande, etc.

            // Validation
            $table->boolean('est_valide')->nullable();

                // Informations de suivi
                $table->foreignId('uploaded_by')->constrained('users')->onDelete('restrict');
                $table->timestamps();
                $table->softDeletes();

                // Index pour les recherches fréquentes
                $table->index(['depense_id', 'est_valide']);
            });
        } else {
            // Si la table existe déjà, on ajoute les colonnes manquantes
            Schema::table('justificatif_depenses', function (Blueprint $table) {
                $columnsToAdd = [
                    'depense_id' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'depense_id')) {
                            $table->foreignId('depense_id')->constrained('depense_caisses')->onDelete('cascade')->after('id');
                        }
                    },
                    'nom_fichier' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'nom_fichier')) {
                            $table->string('nom_fichier')->after('depense_id');
                        }
                    },
                    'chemin_fichier' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'chemin_fichier')) {
                            $table->string('chemin_fichier')->after('nom_fichier');
                        }
                    },
                    'type_mime' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'type_mime')) {
                            $table->string('type_mime')->after('chemin_fichier');
                        }
                    },
                    'taille' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'taille')) {
                            $table->unsignedBigInteger('taille')->after('type_mime');
                        }
                    },
                    'extension' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'extension')) {
                            $table->string('extension', 10)->after('taille');
                        }
                    },
                    'titre' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'titre')) {
                            $table->string('titre')->nullable()->after('extension');
                        }
                    },
                    'description' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'description')) {
                            $table->text('description')->nullable()->after('titre');
                        }
                    },
                    'categorie' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'categorie')) {
                            $table->string('categorie')->nullable()->after('description');
                        }
                    },
                    'est_valide' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'est_valide')) {
                            $table->boolean('est_valide')->nullable()->after('categorie');
                        }
                    },
                    'valideur_id' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'valideur_id')) {
                            $table->foreignId('valideur_id')->nullable()->constrained('users')->onDelete('set null')->after('est_valide');
                        }
                    },
                    'date_validation' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'date_validation')) {
                            $table->dateTime('date_validation')->nullable()->after('valideur_id');
                        }
                    },
                    'commentaire_validation' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'commentaire_validation')) {
                            $table->text('commentaire_validation')->nullable()->after('date_validation');
                        }
                    },
                    'uploaded_by' => function() use ($table) {
                        if (!Schema::hasColumn('justificatif_depenses', 'uploaded_by')) {
                            $table->foreignId('uploaded_by')->constrained('users')->onDelete('restrict')->after('commentaire_validation');
                        }
                    },
                ];

                foreach ($columnsToAdd as $column => $callback) {
                    $callback();
                }

                // Ajout de la suppression logique si elle n'existe pas
                if (!Schema::hasColumn('justificatif_depenses', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne supprimez pas la table si elle existe déjà
        // car elle pourrait être utilisée par d'autres fonctionnalités
        if (Schema::hasTable('justificatif_depenses')) {
            Schema::table('justificatif_depenses', function (Blueprint $table) {
                // Supprimer les contraintes de clé étrangère
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $tableName = $table->getTable();
                $foreignKeys = $sm->listTableForeignKeys($tableName);

                foreach ($foreignKeys as $fk) {
                    $table->dropForeign($fk->getName());
                }
            });

            Schema::dropIfExists('justificatif_depenses');
        }
    }
};
