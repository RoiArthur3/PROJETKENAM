<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('requetes')) {
            Schema::create('requetes', function (Blueprint $table) {
                $table->id();
                $table->string('reference')->unique();
                $table->string('objet');
                $table->text('description')->nullable();
                $table->enum('statut', [
                    'ENREGISTREE',
                    'EN_ATTENTE_ENVOI',
                    'ENVOYEE',
                    'EN_COURS_DE_TRAITEMENT',
                    'TRANSFERE',
                    'CLOTUREE',
                    'REJETEE'
                ])->default('ENREGISTREE');

                // Services
                $table->foreignId('service_emetteur_id')->constrained('services')->onDelete('cascade');
                $table->foreignId('service_destinataire_id')->constrained('services')->onDelete('cascade');

                // Personnes
                $table->foreignId('demandeur_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('destinataire_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('personne_ressource_id')->nullable()->constrained('users')->onDelete('set null');

                // Module source
                $table->string('module_source')->nullable();
                $table->foreignId('operation_id')->nullable()->constrained('operations')->onDelete('set null');

                // Dates
                $table->timestamp('date_envoi')->nullable();
                $table->timestamp('date_cloture')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['statut', 'service_destinataire_id']);
                $table->index(['demandeur_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('requetes');
    }
};
