<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateBonsTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tables:create-bons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer les tables pour les bons de commande et livraison';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Création des Tables pour Bons - KENAM SERVICES');
        $this->info('==================================================');

        try {
            // Table bons_commande
            if (!Schema::hasTable('bons_commande')) {
                DB::statement("CREATE TABLE bons_commande (
                    id bigint unsigned NOT NULL AUTO_INCREMENT,
                    numero varchar(255) NOT NULL UNIQUE,
                    client_id bigint unsigned NOT NULL,
                    contrat_id bigint unsigned NULL,
                    date_commande date NOT NULL,
                    date_livraison_prevue date NULL,
                    montant_ht decimal(10,2) NOT NULL,
                    tva decimal(5,2) NOT NULL DEFAULT 20.00,
                    montant_ttc decimal(10,2) NOT NULL,
                    statut enum('brouillon','envoye','valide','en_preparation','livre','annule') NOT NULL DEFAULT 'brouillon',
                    notes text NULL,
                    conditions_livraison text NULL,
                    created_by bigint unsigned NULL,
                    validated_by bigint unsigned NULL,
                    created_at timestamp NULL DEFAULT NULL,
                    updated_at timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (id),
                    KEY bons_commande_client_id_foreign (client_id),
                    KEY bons_commande_contrat_id_foreign (contrat_id),
                    KEY bons_commande_statut_index (statut),
                    KEY bons_commande_date_commande_index (date_commande)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $this->info('✅ Table bons_commande créée');
            } else {
                $this->info('ℹ️  Table bons_commande existe déjà');
            }

            // Table bons_livraison
            if (!Schema::hasTable('bons_livraison')) {
                DB::statement("CREATE TABLE bons_livraison (
                    id bigint unsigned NOT NULL AUTO_INCREMENT,
                    numero varchar(255) NOT NULL UNIQUE,
                    bon_commande_id bigint unsigned NOT NULL,
                    client_id bigint unsigned NOT NULL,
                    date_livraison date NOT NULL,
                    livreur varchar(255) NULL,
                    adresse_livraison text NULL,
                    statut enum('en_preparation','en_transit','livre','retourne','annule') NOT NULL DEFAULT 'en_preparation',
                    notes text NULL,
                    observations text NULL,
                    created_by bigint unsigned NULL,
                    delivered_by bigint unsigned NULL,
                    created_at timestamp NULL DEFAULT NULL,
                    updated_at timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (id),
                    KEY bons_livraison_bon_commande_id_foreign (bon_commande_id),
                    KEY bons_livraison_client_id_foreign (client_id),
                    KEY bons_livraison_statut_index (statut),
                    KEY bons_livraison_date_livraison_index (date_livraison)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $this->info('✅ Table bons_livraison créée');
            } else {
                $this->info('ℹ️  Table bons_livraison existe déjà');
            }

            // Table lignes_bons
            if (!Schema::hasTable('lignes_bons')) {
                DB::statement("CREATE TABLE lignes_bons (
                    id bigint unsigned NOT NULL AUTO_INCREMENT,
                    bon_commande_id bigint unsigned NOT NULL,
                    reference_produit varchar(255) NOT NULL,
                    designation varchar(255) NOT NULL,
                    description text NULL,
                    quantite int NOT NULL,
                    prix_unitaire_ht decimal(10,2) NOT NULL,
                    tva decimal(5,2) NOT NULL DEFAULT 20.00,
                    montant_ht decimal(10,2) NOT NULL,
                    montant_ttc decimal(10,2) NOT NULL,
                    unite enum('unite','kg','litre','metre','piece','heure') NOT NULL DEFAULT 'unite',
                    notes text NULL,
                    created_at timestamp NULL DEFAULT NULL,
                    updated_at timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (id),
                    KEY lignes_bons_bon_commande_id_foreign (bon_commande_id),
                    KEY lignes_bons_reference_produit_index (reference_produit)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $this->info('✅ Table lignes_bons créée');
            } else {
                $this->info('ℹ️  Table lignes_bons existe déjà');
            }

            // Ajouter des données de test si les tables sont vides
            if (Schema::hasTable('bons_commande') && DB::table('bons_commande')->count() == 0) {
                $this->info('Ajout de données de test pour bons_commande...');

                DB::table('bons_commande')->insert([
                    [
                        'numero' => 'BC-2025-001',
                        'client_id' => 1,
                        'contrat_id' => null,
                        'date_commande' => now()->format('Y-m-d'),
                        'date_livraison_prevue' => now()->addWeek(1)->format('Y-m-d'),
                        'montant_ht' => 1000.00,
                        'tva' => 20.00,
                        'montant_ttc' => 1200.00,
                        'statut' => 'brouillon',
                        'notes' => 'Bon de commande test',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]);

                $this->info('✅ 1 bon de commande test ajouté');
            }

            if (Schema::hasTable('bons_livraison') && DB::table('bons_livraison')->count() == 0) {
                $this->info('Ajout de données de test pour bons_livraison...');

                // Récupérer l'ID du bon de commande créé
                $bcId = DB::table('bons_commande')->first()->id;

                DB::table('bons_livraison')->insert([
                    [
                        'numero' => 'BL-2025-001',
                        'bon_commande_id' => $bcId,
                        'client_id' => 1,
                        'date_livraison' => now()->addWeek(2)->format('Y-m-d'),
                        'livreur' => 'Livreur Test',
                        'adresse_livraison' => 'Adresse de livraison test',
                        'statut' => 'en_preparation',
                        'notes' => 'Bon de livraison test',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]);

                $this->info('✅ 1 bon de livraison test ajouté');
            }

            $this->info('');
            $this->info('🎯 Vérification finale:');
            $this->info('- bons_commande: ' . (Schema::hasTable('bons_commande') ? '✅' : '❌') . ' (' . DB::table('bons_commande')->count() . ' enregistrements)');
            $this->info('- bons_livraison: ' . (Schema::hasTable('bons_livraison') ? '✅' : '❌') . ' (' . DB::table('bons_livraison')->count() . ' enregistrements)');
            $this->info('- lignes_bons: ' . (Schema::hasTable('lignes_bons') ? '✅' : '❌') . ' (' . DB::table('lignes_bons')->count() . ' enregistrements)');

            $this->info('');
            $this->info('🟢 TABLES POUR BONS CRÉÉES AVEC SUCCÈS !');
            $this->info('✨ Opération terminée !');

        } catch (Exception $e) {
            $this->error('❌ Erreur: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
