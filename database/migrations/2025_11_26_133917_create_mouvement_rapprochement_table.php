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
        Schema::create('mouvement_rapprochement', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mouvement_id');
            $table->foreignId('rapprochement_id')->constrained('rapprochements')->onDelete('cascade');

            // Indique si le mouvement a été vérifié lors du rapprochement
            $table->boolean('est_verifie')->default(false);

            // Commentaire éventuel sur le mouvement dans le cadre de ce rapprochement
            $table->text('commentaire')->nullable();

            // Utilisateur qui a effectué le rapprochement
            $table->foreignId('utilisateur_id')->constrained('users')->onDelete('restrict');

            $table->timestamps();

            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['mouvement_id', 'rapprochement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvement_rapprochement');
    }
};
