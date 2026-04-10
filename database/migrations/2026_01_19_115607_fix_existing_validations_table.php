<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Vérifier si la table existe déjà
        if (Schema::hasTable('validations')) {
            // 1. Vérifier si la migration existe déjà dans la table des migrations
            $migrationExists = DB::table('migrations')
                ->where('migration', '2026_01_12_180030_create_validations_table')
                ->exists();

            // 2. Si la migration n'existe pas dans la table des migrations, l'ajouter
            if (!$migrationExists) {
                DB::table('migrations')->insert([
                    'migration' => '2026_01_12_180030_create_validations_table',
                    'batch' => DB::table('migrations')->max('batch') + 1
                ]);
                echo "Migration marquée comme complétée dans la table des migrations.\n";
            }

            // 3. Vérifier et ajouter les colonnes manquantes si nécessaire
            Schema::table('validations', function (Blueprint $table) {
                if (!Schema::hasColumn('validations', 'titre')) {
                    $table->string('titre')->after('id');
                }
                if (!Schema::hasColumn('validations', 'description')) {
                    $table->text('description')->nullable()->after('titre');
                }
                // Ajoutez d'autres colonnes si nécessaire
            });

            echo "La table 'validations' a été vérifiée et mise à jour si nécessaire.\n";
        } else {
            // Si la table n'existe pas, créer la table
            Schema::create('validations', function (Blueprint $table) {
                $table->id();
                $table->string('titre');
                $table->text('description')->nullable();
                $table->enum('statut', ['en_attente', 'en_cours', 'approuve', 'rejete'])->default('en_attente');
                $table->foreignId('initiateur_id')->constrained('users');
                $table->foreignId('validateur_id')->nullable()->constrained('users');
                $table->text('commentaire')->nullable();
                $table->timestamp('date_validation')->nullable();
                $table->timestamps();
            });
            echo "La table 'validations' a été créée avec succès.\n";
        }
    }

    public function down()
    {
        // Cette migration est sécuritaire et ne fait rien en rollback
        // pour éviter de supprimer des données existantes
        echo "Cette migration ne fera rien en rollback pour des raisons de sécurité.\n";
        echo "Utilisez php artisan migrate:fresh si vous souhaitez tout réinitialiser.\n";
    }
};