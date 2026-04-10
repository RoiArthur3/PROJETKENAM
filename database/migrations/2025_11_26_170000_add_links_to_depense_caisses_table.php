<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('depense_caisses')) {
            return;
        }

        Schema::table('depense_caisses', function (Blueprint $table) {
            // Lien vers un paiement fournisseur éventuel
            if (!Schema::hasColumn('depense_caisses', 'paiement_fournisseur_id')) {
                $table->foreignId('paiement_fournisseur_id')
                    ->nullable()
                    ->after('compte_comptable_id')
                    ->constrained('paiement_fournisseurs')
                    ->nullOnDelete();
            }

            // Lien direct éventuel vers une facture fournisseur
            if (!Schema::hasColumn('depense_caisses', 'facture_fournisseur_id')) {
                $table->foreignId('facture_fournisseur_id')
                    ->nullable()
                    ->after('paiement_fournisseur_id')
                    ->constrained('facture_fournisseurs')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('depense_caisses')) {
            return;
        }

        Schema::table('depense_caisses', function (Blueprint $table) {
            if (Schema::hasColumn('depense_caisses', 'paiement_fournisseur_id')) {
                $table->dropForeign(['paiement_fournisseur_id']);
                $table->dropColumn('paiement_fournisseur_id');
            }

            if (Schema::hasColumn('depense_caisses', 'facture_fournisseur_id')) {
                $table->dropForeign(['facture_fournisseur_id']);
                $table->dropColumn('facture_fournisseur_id');
            }
        });
    }
};
