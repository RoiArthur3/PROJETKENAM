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
        Schema::create('approvisionnement_caisses', function (Blueprint $table) {
            $table->id();
            $table->string('numero_operation')->unique();
            $table->foreignId('caisse_source_id')->constrained('caisses')->onDelete('restrict');
            $table->foreignId('caisse_destination_id')->constrained('caisses')->onDelete('restrict');
            $table->decimal('montant', 15, 2);
            $table->string('devise', 3)->default('XOF');
            $table->text('motif');
            $table->enum('statut', ['brouillon', 'en_attente', 'valide', 'rejete', 'decaisser', 'cloture'])->default('brouillon');
            $table->foreignId('demandeur_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('valideur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('date_validation')->nullable();
            $table->text('commentaire_validation')->nullable();
            $table->dateTime('date_decaissement')->nullable();
            $table->dateTime('date_cloture')->nullable();
            $table->text('notes')->nullable();
            $table->json('pieces_jointes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['statut', 'date_validation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvisionnement_caisses');
    }
};
