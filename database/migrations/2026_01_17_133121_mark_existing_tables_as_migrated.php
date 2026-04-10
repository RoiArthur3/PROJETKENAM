<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Récupérer le prochain numéro de lot
        $batch = DB::table('migrations')->max('batch') + 1;

        // Obtenir toutes les migrations déjà effectuées
        $migrated = DB::table('migrations')->pluck('migration')->toArray();

        // Parcourir tous les fichiers de migration
        $migrationFiles = File::files(database_path('migrations'));

        foreach ($migrationFiles as $file) {
            $migrationName = pathinfo($file->getFilename(), PATHINFO_FILENAME);

            // Si la migration n'est pas déjà dans la table des migrations
            if (!in_array($migrationName, $migrated)) {
                // Vérifier si la migration crée une table
                $content = File::get($file->getPathname());

                // Si c'est une migration de création de table
                if (str_contains($content, 'Schema::create')) {
                    // Extraire le nom de la table
                    preg_match("/Schema::create\\(['\"]([^'\"]+)['\"]/", $content, $matches);

                    if (isset($matches[1])) {
                        $tableName = $matches[1];

                        // Vérifier si la table existe dans la base de données
                        try {
                            $tableExists = Schema::hasTable($tableName);

                            if ($tableExists) {
                                // Marquer la migration comme effectuée
                                DB::table('migrations')->insert([
                                    'migration' => $migrationName,
                                    'batch' => $batch,
                                ]);

                                echo "Migration marquée comme effectuée : $migrationName\n";
                            }
                        } catch (\Exception $e) {
                            // En cas d'erreur, on continue avec la migration suivante
                            continue;
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration ne peut pas être annulée car elle ne fait que marquer des migrations comme effectuées
    }
};
