<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentFournisseursTable extends Migration
{
    public function up()
    {
        Schema::create('document_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 50);
            
            // Références
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('cascade');
            
            // Informations du document
            $table->string('titre', 255);
            $table->text('description')->nullable();
            $table->enum('type_document', [
                'facture', 'devis', 'bon_commande', 'bon_livraison', 'contrat', 
                'kbis', 'rib', 'assurance', 'certificat', 'attestation', 
                'carte_identite', 'permis_conduire', 'carte_grise', 
                'certificat_immatriculation', 'visite_technique', 
                'autorisation', 'autre'
            ]);
            
            $table->string('categorie', 100)->nullable();
            $table->string('numero_document', 100)->nullable();
            
            // Fichier
            $table->string('nom_fichier', 255);
            $table->string('chemin_fichier', 255);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('taille')->nullable()->comment('Taille en octets');
            
            // Période de validité
            $table->date('date_emission')->nullable();
            $table->date('date_expiration')->nullable();
            $table->date('date_notification')->nullable()->comment('Date de notification d\'expiration');
            
            // Statut et validation
            $table->enum('statut', [
                'brouillon', 'valide', 'a_renouveler', 'expire', 'rejete', 'en_attente', 'archive'
            ])->default('brouillon');
            
            $table->boolean('est_obligatoire')->default(false);
            $table->boolean('est_verifie')->default(false);
            $table->dateTime('date_verification')->nullable();
            $table->foreignId('verificateur_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Métadonnées
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            
            // Relations
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index('reference');
            $table->index('fournisseur_id');
            $table->index('type_document');
            $table->index('date_emission');
            $table->index('date_expiration');
            $table->index('statut');
            $table->index('est_obligatoire');
            $table->index('est_verifie');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_fournisseurs');
    }
}
