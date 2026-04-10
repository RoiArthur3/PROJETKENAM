<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFournisseursTable extends Migration
{
    public function up()
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 50)->unique();
            $table->string('raison_sociale', 255);
            $table->string('forme_juridique', 100)->nullable();
            $table->string('siret', 20)->nullable()->unique();
            $table->string('tva_intracom', 20)->nullable();
            
            $table->foreignId('categorie_id')->nullable()->constrained('categorie_fournisseurs')->onDelete('set null');
            
            // Adresse
            $table->string('adresse', 255)->nullable();
            $table->string('code_postal', 10)->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('pays', 100)->default('France');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Coordonnées
            $table->string('telephone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('site_web', 255)->nullable();
            
            // Contacts (stockés en JSON)
            $table->json('contacts')->nullable();
            
            // Informations complémentaires
            $table->string('code_comptable', 50)->nullable();
            $table->string('mode_reglement', 50)->nullable();
            $table->string('condition_reglement', 100)->nullable();
            $table->decimal('delai_livraison', 5, 2)->nullable()->comment('En jours');
            
            // Évaluation
            $table->decimal('evaluation_moyenne', 3, 2)->nullable();
            $table->string('classement', 20)->nullable()->comment('Or, Argent, Bronze, etc.');
            
            // Statut
            $table->boolean('est_actif')->default(true);
            $table->date('date_derniere_commande')->nullable();
            $table->decimal('chiffre_affaires', 15, 2)->default(0);
            
            // Métadonnées
            $table->json('metadata')->nullable();
            $table->json('horaires')->nullable();
            $table->text('notes')->nullable();
            
            // Compteurs
            $table->unsignedInteger('nombre_commandes')->default(0);
            $table->unsignedInteger('nombre_contrats')->default(0);
            $table->unsignedInteger('nombre_incidents')->default(0);
            
            // Relations
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index('raison_sociale');
            $table->index('siret');
            $table->index('ville');
            $table->index('est_actif');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fournisseurs');
    }
}
