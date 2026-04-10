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
        Schema::create('syscohada_plan_comptable', function (Blueprint $table) {
            $table->id();
            $table->string('classe'); // Classes 1 à 8 du SYSCOHADA
            $table->string('numero_compte')->unique(); // Numéro de compte SYSCOHADA
            $table->string('nom_compte');
            $table->text('description')->nullable();
            $table->string('type_compte'); // Actif, Passif, Charges, Produits
            $table->boolean('est_actif')->default(true);
            $table->timestamps();

            $table->index(['classe', 'numero_compte']);
            $table->index('type_compte');
        });

        // Insertion des comptes SYSCOHADA de base
        DB::table('syscohada_plan_comptable')->insert([
            // Classe 1 - Comptes de capitaux
            ['classe' => '1', 'numero_compte' => '10', 'nom_compte' => 'Capital Social', 'description' => 'Capital social de l\'entreprise', 'type_compte' => 'Passif'],
            ['classe' => '1', 'numero_compte' => '11', 'nom_compte' => 'Réserves', 'description' => 'Réserves et bénéfices', 'type_compte' => 'Passif'],
            ['classe' => '1', 'numero_compte' => '12', 'nom_compte' => 'Report à Nouveau', 'description' => 'Report des bénéfices non distribués', 'type_compte' => 'Passif'],

            // Classe 2 - Comptes d'immobilisations
            ['classe' => '2', 'numero_compte' => '21', 'nom_compte' => 'Immobilisations Corporelles', 'description' => 'Biens matériels durables', 'type_compte' => 'Actif'],
            ['classe' => '2', 'numero_compte' => '22', 'nom_compte' => 'Immobilisations Incorporelles', 'description' => 'Fonds commercial, brevets', 'type_compte' => 'Actif'],
            ['classe' => '2', 'numero_compte' => '23', 'nom_compte' => 'Immobilisations Financières', 'description' => 'Placements financiers', 'type_compte' => 'Actif'],
            ['classe' => '2', 'numero_compte' => '28', 'nom_compte' => 'Amortissements', 'description' => 'Amortissements cumulés', 'type_compte' => 'Actif'],

            // Classe 3 - Comptes de stocks
            ['classe' => '3', 'numero_compte' => '31', 'nom_compte' => 'Marchandises', 'description' => 'Stocks de marchandises', 'type_compte' => 'Actif'],
            ['classe' => '3', 'numero_compte' => '32', 'nom_compte' => 'Matières Premières', 'description' => 'Stocks de matières premières', 'type_compte' => 'Actif'],
            ['classe' => '3', 'numero_compte' => '33', 'nom_compte' => 'Produits Finis', 'description' => 'Stocks de produits finis', 'type_compte' => 'Actif'],

            // Classe 4 - Comptes de tiers
            ['classe' => '4', 'numero_compte' => '40', 'nom_compte' => 'Fournisseurs', 'description' => 'Dettes fournisseurs', 'type_compte' => 'Passif'],
            ['classe' => '4', 'numero_compte' => '41', 'nom_compte' => 'Clients', 'description' => 'Créances clients', 'type_compte' => 'Actif'],
            ['classe' => '4', 'numero_compte' => '42', 'nom_compte' => 'Personnel', 'description' => 'Dettes personnel', 'type_compte' => 'Passif'],
            ['classe' => '4', 'numero_compte' => '43', 'nom_compte' => 'État et Collectivités', 'description' => 'Dettes fiscales et sociales', 'type_compte' => 'Passif'],
            ['classe' => '4', 'numero_compte' => '44', 'nom_compte' => 'Débiteurs et Créditeurs Divers', 'description' => 'Autres débiteurs et créditeurs', 'type_compte' => 'Actif'],

            // Classe 5 - Comptes de trésorerie
            ['classe' => '5', 'numero_compte' => '51', 'nom_compte' => 'Banques', 'description' => 'Comptes bancaires', 'type_compte' => 'Actif'],
            ['classe' => '5', 'numero_compte' => '52', 'nom_compte' => 'Caisse', 'description' => 'Fonds en caisse', 'type_compte' => 'Actif'],
            ['classe' => '5', 'numero_compte' => '53', 'nom_compte' => 'CCP', 'description' => 'Comptes courants postaux', 'type_compte' => 'Actif'],

            // Classe 6 - Comptes de charges
            ['classe' => '6', 'numero_compte' => '60', 'nom_compte' => 'Achats', 'description' => 'Achats de marchandises', 'type_compte' => 'Charges'],
            ['classe' => '6', 'numero_compte' => '61', 'nom_compte' => 'Services Extérieurs', 'description' => 'Sous-traitance et services', 'type_compte' => 'Charges'],
            ['classe' => '6', 'numero_compte' => '62', 'nom_compte' => 'Autres Services Extérieurs', 'description' => 'Autres services externes', 'type_compte' => 'Charges'],
            ['classe' => '6', 'numero_compte' => '63', 'nom_compte' => 'Impôts et Taxes', 'description' => 'Fiscalité et impôts', 'type_compte' => 'Charges'],
            ['classe' => '6', 'numero_compte' => '64', 'nom_compte' => 'Charges de Personnel', 'description' => 'Salaires et charges sociales', 'type_compte' => 'Charges'],
            ['classe' => '6', 'numero_compte' => '65', 'nom_compte' => 'Autres Charges', 'description' => 'Charges diverses', 'type_compte' => 'Charges'],
            ['classe' => '6', 'numero_compte' => '66', 'nom_compte' => 'Charges Financières', 'description' => 'Intérêts et frais financiers', 'type_compte' => 'Charges'],
            ['classe' => '6', 'numero_compte' => '67', 'nom_compte' => 'Dotations aux Amortissements', 'description' => 'Amortissements de l\'exercice', 'type_compte' => 'Charges'],
            ['classe' => '6', 'numero_compte' => '68', 'nom_compte' => 'Dotations aux Provisions', 'description' => 'Provisions pour risques', 'type_compte' => 'Charges'],

            // Classe 7 - Comptes de produits
            ['classe' => '7', 'numero_compte' => '70', 'nom_compte' => 'Ventes', 'description' => 'Ventes de marchandises', 'type_compte' => 'Produits'],
            ['classe' => '7', 'numero_compte' => '71', 'nom_compte' => 'Prestations de Services', 'description' => 'Services rendus', 'type_compte' => 'Produits'],
            ['classe' => '7', 'numero_compte' => '72', 'nom_compte' => 'Production Stockée', 'description' => 'Production immobilisée', 'type_compte' => 'Produits'],
            ['classe' => '7', 'numero_compte' => '73', 'nom_compte' => 'Produits Accessoires', 'description' => 'Revenus annexes', 'type_compte' => 'Produits'],
            ['classe' => '7', 'numero_compte' => '75', 'nom_compte' => 'Autres Produits', 'description' => 'Produits divers', 'type_compte' => 'Produits'],
            ['classe' => '7', 'numero_compte' => '76', 'nom_compte' => 'Produits Financiers', 'description' => 'Revenus financiers', 'type_compte' => 'Produits'],
            ['classe' => '7', 'numero_compte' => '77', 'nom_compte' => 'Reprises sur Amortissements', 'description' => 'Reprises d\'amortissements', 'type_compte' => 'Produits'],
            ['classe' => '7', 'numero_compte' => '78', 'nom_compte' => 'Reprises sur Provisions', 'description' => 'Reprises de provisions', 'type_compte' => 'Produits'],

            // Classe 8 - Comptes spéciaux
            ['classe' => '8', 'numero_compte' => '80', 'nom_compte' => 'Engagements Hors Bilan', 'description' => 'Engagements donnés', 'type_compte' => 'Hors Bilan'],
            ['classe' => '8', 'numero_compte' => '81', 'nom_compte' => 'Engagements Reçus', 'description' => 'Engagements reçus', 'type_compte' => 'Hors Bilan'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syscohada_plan_comptable');
    }
};
