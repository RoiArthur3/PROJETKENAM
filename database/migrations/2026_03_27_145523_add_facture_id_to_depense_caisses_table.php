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
        // Vérifier si la colonne existe déjà pour éviter l'erreur en production
        if (Schema::hasColumn('depense_caisses', 'facture_id')) {
            return; // La colonne existe déjà, ne pas l'ajouter
        }

        Schema::table('depense_caisses', function (Blueprint $table) {
            $table->unsignedBigInteger('facture_id')->nullable()->after('operation_id');
            $table->foreign('facture_id')->references('id')->on('factures')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('depense_caisses', 'facture_id')) {
            Schema::table('depense_caisses', function (Blueprint $table) {
                $table->dropForeign(['facture_id']);
                $table->dropColumn('facture_id');
            });
        }
    }
};
