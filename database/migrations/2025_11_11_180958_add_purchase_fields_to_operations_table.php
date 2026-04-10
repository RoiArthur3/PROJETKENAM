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
        Schema::table('operations', function (Blueprint $table) {
            // Informations fournisseur et achat
            $table->foreignId('fournisseur_id')->nullable()->after('operational_service_id')->constrained('suppliers')->nullOnDelete();
            $table->decimal('montant_total', 15, 2)->nullable()->after('fournisseur_id');
            $table->integer('quantite')->nullable()->after('montant_total');
            $table->string('produit_nom')->nullable()->after('quantite');
            $table->date('date_livraison_prevue')->nullable()->after('produit_nom');
            $table->string('bon_commande_numero')->nullable()->after('date_livraison_prevue');
            
            // Statut livraison
            $table->enum('statut_livraison', ['en_attente', 'partielle', 'complete', 'annulee'])->default('en_attente')->after('statut_courant');
            $table->date('date_livraison_effective')->nullable()->after('statut_livraison');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropForeign(['fournisseur_id']);
            $table->dropColumn([
                'fournisseur_id',
                'montant_total',
                'quantite',
                'produit_nom',
                'date_livraison_prevue',
                'bon_commande_numero',
                'statut_livraison',
                'date_livraison_effective'
            ]);
        });
    }
};
