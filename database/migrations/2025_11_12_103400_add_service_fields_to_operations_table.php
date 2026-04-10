<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            // Ajouter les champs pour le système de requêtes entre services
            if (!Schema::hasColumn('operations', 'service_emetteur_id')) {
                $table->foreignId('service_emetteur_id')->nullable()->constrained('services')->onDelete('set null');
            }
            if (!Schema::hasColumn('operations', 'service_destinataire_id')) {
                $table->foreignId('service_destinataire_id')->nullable()->constrained('services')->onDelete('set null');
            }
            if (!Schema::hasColumn('operations', 'destinataire_id')) {
                $table->foreignId('destinataire_id')->nullable()->constrained('users')->onDelete('set null');
            }

            // Statut de la requête
            if (!Schema::hasColumn('operations', 'statut_requete')) {
                $table->enum('statut_requete', [
                    'ENREGISTREE',
                    'EN_ATTENTE_ENVOI',
                    'ENVOYEE',
                    'EN_COURS_DE_TRAITEMENT',
                    'TRANSFERE',
                    'CLOTUREE',
                    'REJETEE'
                ])->default('ENREGISTREE');
            }

            // Dates de suivi
            if (!Schema::hasColumn('operations', 'date_envoi')) {
                $table->timestamp('date_envoi')->nullable();
            }
            if (!Schema::hasColumn('operations', 'date_cloture')) {
                $table->timestamp('date_cloture')->nullable();
            }

            // Type de requête
            if (!Schema::hasColumn('operations', 'type_requete')) {
                $table->string('type_requete')->nullable();
            }
            if (!Schema::hasColumn('operations', 'description_requete')) {
                $table->text('description_requete')->nullable();
            }

            // Priorité
            if (!Schema::hasColumn('operations', 'priorite')) {
                $table->enum('priorite', ['BASSE', 'MOYENNE', 'HAUTE', 'URGENTE'])->default('MOYENNE');
            }

            // Référence unique
            if (!Schema::hasColumn('operations', 'reference_requete')) {
                $table->string('reference_requete')->nullable()->unique();
            }

            // Ajout des index nécessaires
            // On ne vérifie pas s'ils existent déjà pour simplifier
            // S'ils existent déjà, une erreur sera levée mais ignorée en production
            try {
                $table->index(['statut_requete', 'service_destinataire_id']);
                $table->index(['service_emetteur_id', 'created_at']);
            } catch (\Exception $e) {
                // Ignorer les erreurs d'index existants
                if (!str_contains($e->getMessage(), 'Duplicate key')) {
                    throw $e;
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropForeign(['service_emetteur_id']);
            $table->dropForeign(['service_destinataire_id']);
            $table->dropForeign(['destinataire_id']);

            $table->dropColumn([
                'service_emetteur_id',
                'service_destinataire_id',
                'destinataire_id',
                'statut_requete',
                'date_envoi',
                'date_cloture',
                'type_requete',
                'description_requete',
                'priorite',
                'reference_requete'
            ]);
        });
    }
};
