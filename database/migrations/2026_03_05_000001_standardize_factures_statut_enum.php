<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la table factures existe
        if (Schema::hasTable('factures')) {
            // D'abord modifier l'ENUM pour accepter TOUTES les valeurs (anciennes et nouvelles)
            DB::statement("
                ALTER TABLE factures 
                MODIFY COLUMN statut VARCHAR(50) NOT NULL DEFAULT 'impayée'
            ");

            // Migration des valeurs : sans accent vers avec accent
            DB::statement("UPDATE factures SET statut = 'payée' WHERE statut = 'payee'");
            DB::statement("UPDATE factures SET statut = 'annulée' WHERE statut = 'annulee'");
            DB::statement("UPDATE factures SET statut = 'impayée' WHERE statut IN ('en_attente', 'en_retard')");
            DB::statement("UPDATE factures SET statut = 'partiellement_payée' WHERE statut NOT IN ('payée', 'annulée', 'impayée')");

            // Ajouter un index pour les performances
            DB::statement("ALTER TABLE factures ADD INDEX idx_statut (statut)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('factures')) {
            // Restaurer les valeurs sans accent
            DB::statement("UPDATE factures SET statut = 'payee' WHERE statut = 'payée'");
            DB::statement("UPDATE factures SET statut = 'en_attente' WHERE statut = 'impayée' OR statut = 'partiellement_payée'");
            DB::statement("UPDATE factures SET statut = 'annulee' WHERE statut = 'annulée'");

            // Restaurer le type ENUM d'origine
            DB::statement("
                ALTER TABLE factures 
                MODIFY COLUMN statut ENUM(
                    'payee', 
                    'en_attente', 
                    'en_retard',
                    'annulee'
                ) DEFAULT 'en_attente'
            ");

            // Supprimer l'index
            DB::statement("ALTER TABLE factures DROP INDEX IF EXISTS idx_statut");
        }
    }
};
