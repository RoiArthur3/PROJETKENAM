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
        // On transforme l'ENUM en VARCHAR pour accepter tous les statuts sans erreur d'accents
        // Cela évite l'erreur "Data truncated" sur les serveurs en mode strict
        DB::statement("ALTER TABLE operations MODIFY COLUMN statut_courant VARCHAR(255) DEFAULT 'en_attente_de_validation'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // On ne revient pas en arrière car le VARCHAR est plus flexible et sûr pour la prod
    }
};
