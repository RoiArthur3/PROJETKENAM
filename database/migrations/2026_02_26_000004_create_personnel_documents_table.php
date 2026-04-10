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
        Schema::create('personnel_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personnel_id')->constrained('personnel')->onDelete('cascade');

            // Informations du fichier
            $table->string('nom_fichier');
            $table->string('chemin_fichier');
            $table->enum('type_document', [
                'CV', 'CONTRAT', 'DIPLOME', 'ATTESTATION',
                'CASIER_JUDICIAIRE', 'CERTIFICAT_MEDICAL', 'PHOTO',
                'CNI', 'PASSEPORT', 'PERMIS', 'AUTRE'
            ]);
            $table->string('taille_fichier')->nullable();

            // Gestion de l'expiration
            $table->date('date_expiration')->nullable();
            $table->text('description')->nullable();

            // Workflow de validation
            $table->enum('statut', ['EN_ATTENTE', 'VALIDE', 'REJETE', 'EXPIRE'])->default('EN_ATTENTE');
            $table->foreignId('upload_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_validation')->nullable();
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            $table->text('motif_decision')->nullable();

            $table->timestamps();

            // Index pour optimisation
            $table->index(['personnel_id', 'type_document']);
            $table->index(['personnel_id', 'statut']);
            $table->index(['statut', 'date_expiration']);
            $table->index(['type_document']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_documents');
    }
};
