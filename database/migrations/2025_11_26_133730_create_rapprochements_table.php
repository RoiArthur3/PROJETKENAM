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
        Schema::create('rapprochements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caisse_id')->constrained('caisses')->onDelete('restrict');
            $table->string('reference')->unique();

            $table->dateTime('date_rapprochement')->nullable();

            // Période de rapprochement
            $table->date('date_debut');
            $table->date('date_fin');

            // Solde théorique et réel
            $table->decimal('solde_theorique', 15, 2);
            $table->decimal('solde_reel', 15, 2);
            $table->decimal('ecart', 15, 2);

            // Statut du rapprochement
            $table->enum('statut', ['brouillon', 'en_cours', 'valide', 'cloture'])->default('brouillon');

            // Informations de validation
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('date_validation')->nullable();
            $table->text('commentaire_validation')->nullable();

            // Informations de clôture
            $table->foreignId('cloture_par')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('date_cloture')->nullable();
            $table->text('commentaire_cloture')->nullable();

            // Informations de suivi
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Index pour les recherches fréquentes
            $table->index(['caisse_id', 'statut', 'date_debut', 'date_fin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapprochements');
    }
};
