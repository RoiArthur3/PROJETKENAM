<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('materiel_roulant', function (Blueprint $table) {
            $table->id();
            $table->string('immatriculation')->unique();
            $table->string('marque');
            $table->string('modele');
            $table->string('type_materiel')->default('vehicule');
            $table->string('categorie')->default('utilitaire');
            $table->year('annee');
            $table->string('couleur');
            $table->string('numero_serie')->nullable();
            $table->decimal('valeur_achat', 10, 2);
            $table->decimal('valeur_actuelle', 10, 2);
            $table->decimal('kilometrage', 10, 2)->default(0);
            $table->decimal('kilometrage_annuel', 10, 2)->default(0);
            $table->date('date_achat');
            $table->date('date_mise_en_service');
            $table->date('date_fin_service')->nullable();
            $table->date('date_derniere_maintenance')->nullable();
            $table->date('date_prochaine_maintenance')->nullable();
            $table->enum('statut', ['actif', 'en_maintenance', 'en_reparation', 'hors_service', 'vendu'])->default('actif');
            $table->string('localisation')->nullable();
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->enum('proprietaire', ['entreprise', 'personnel', 'loue'])->default('entreprise');
            $table->foreignId('responsable_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Index
            $table->index('immatriculation');
            $table->index('statut');
            $table->index('type_materiel');
            $table->index('categorie');
            $table->index('proprietaire');
            $table->index('date_achat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('materiel_roulant');
    }
};
