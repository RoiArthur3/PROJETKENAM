<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateValidationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('validations', function (Blueprint $table) {
            // Vérification et ajout des colonnes manquantes
            if (!Schema::hasColumn('validations', 'titre')) {
                $table->string('titre')->after('module_source');
            }
            
            if (!Schema::hasColumn('validations', 'description')) {
                $table->text('description')->nullable()->after('titre');
            }
            
            if (!Schema::hasColumn('validations', 'initiateur_id')) {
                $table->foreignId('initiateur_id')->after('description')
                      ->constrained('users')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('validations', 'validateur_id')) {
                $table->foreignId('validateur_id')->nullable()->after('initiateur_id')
                      ->constrained('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('validations', 'statut')) {
                $table->enum('statut', ['en_attente', 'en_cours', 'approuve', 'rejete'])
                      ->default('en_attente')
                      ->after('validateur_id');
            }
            
            if (!Schema::hasColumn('validations', 'commentaire')) {
                $table->text('commentaire')->nullable()->after('statut');
            }
            
            if (!Schema::hasColumn('validations', 'date_validation')) {
                $table->timestamp('date_validation')->nullable()->after('commentaire');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Ne supprimez pas les colonnes pour éviter de perdre des données
        // Si nécessaire, créez une nouvelle migration pour les supprimer
    }
}