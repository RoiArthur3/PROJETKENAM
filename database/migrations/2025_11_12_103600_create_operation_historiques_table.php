<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('operation_historiques')) {
            Schema::create('operation_historiques', function (Blueprint $table) {
                $table->id();
                $table->foreignId('operation_id')->constrained('operations')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('action', [
                    'CREATION',
                    'ENVOI',
                    'RECEPTION',
                    'COMMENTAIRE',
                    'CHANGEMENT_STATUT',
                    'TRANSFERT',
                    'CLOTURE',
                    'AJOUT_PIECE_JOINTE',
                    'VALIDATION'
                ]);
                $table->enum('ancien_statut', [
                    'ENREGISTREE',
                    'EN_ATTENTE_ENVOI',
                    'ENVOYEE',
                    'EN_COURS_DE_TRAITEMENT',
                    'TRANSFERE',
                    'CLOTUREE',
                    'REJETEE'
                ])->nullable();
                $table->enum('nouveau_statut', [
                    'ENREGISTREE',
                    'EN_ATTENTE_ENVOI',
                    'ENVOYEE',
                    'EN_COURS_DE_TRAITEMENT',
                    'TRANSFERE',
                    'CLOTUREE',
                    'REJETEE'
                ])->nullable();
                $table->text('commentaire')->nullable();
                $table->json('donnees_modifiees')->nullable();
                $table->timestamps();
                
                $table->index(['operation_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_historiques');
    }
};
