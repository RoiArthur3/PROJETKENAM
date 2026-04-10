<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMissingTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tables:create-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer les tables manquantes pour le dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Création des Tables Manquantes - KENAM SERVICES');
        $this->info('==================================================');

        try {
            // Table pointages
            if (!Schema::hasTable('pointages')) {
                DB::statement("CREATE TABLE pointages (
                    id bigint unsigned NOT NULL AUTO_INCREMENT,
                    user_id bigint unsigned NOT NULL,
                    date_pointage date NOT NULL,
                    heure_arrivee time NULL,
                    heure_depart time NULL,
                    statut varchar(50) NOT NULL DEFAULT 'present',
                    notes text NULL,
                    created_at timestamp NULL DEFAULT NULL,
                    updated_at timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (id),
                    KEY pointages_user_id_foreign (user_id),
                    KEY pointages_date_pointage_index (date_pointage)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $this->info('✅ Table pointages créée');
            } else {
                $this->info('ℹ️  Table pointages existe déjà');
            }

            // Table factures
            if (!Schema::hasTable('factures')) {
                DB::statement("CREATE TABLE factures (
                    id bigint unsigned NOT NULL AUTO_INCREMENT,
                    numero varchar(255) NOT NULL UNIQUE,
                    client_id bigint unsigned NOT NULL,
                    date_facture date NOT NULL,
                    montant_ht decimal(10,2) NOT NULL,
                    tva decimal(5,2) NOT NULL DEFAULT 20.00,
                    montant_ttc decimal(10,2) NOT NULL,
                    statut enum('en_attente','payee','en_retard','annulee') NOT NULL DEFAULT 'en_attente',
                    description text NULL,
                    created_by bigint unsigned NULL,
                    created_at timestamp NULL DEFAULT NULL,
                    updated_at timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (id),
                    KEY factures_client_id_foreign (client_id),
                    KEY factures_date_facture_index (date_facture)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $this->info('✅ Table factures créée');
            } else {
                $this->info('ℹ️  Table factures existe déjà');
            }

            // Table notifications
            if (!Schema::hasTable('notifications')) {
                DB::statement("CREATE TABLE notifications (
                    id bigint unsigned NOT NULL AUTO_INCREMENT,
                    user_id bigint unsigned NOT NULL,
                    title varchar(255) NOT NULL,
                    message text NOT NULL,
                    type varchar(50) NOT NULL DEFAULT 'info',
                    is_read tinyint(1) NOT NULL DEFAULT 0,
                    created_at timestamp NULL DEFAULT NULL,
                    updated_at timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (id),
                    KEY notifications_user_id_foreign (user_id),
                    KEY notifications_is_read_index (is_read)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $this->info('✅ Table notifications créée');
            } else {
                $this->info('ℹ️  Table notifications existe déjà');
            }

            // Table produits (pour le dashboard)
            if (!Schema::hasTable('produits')) {
                DB::statement("CREATE TABLE produits (
                    id bigint unsigned NOT NULL AUTO_INCREMENT,
                    nom varchar(255) NOT NULL,
                    reference varchar(100) NOT NULL,
                    description text NULL,
                    stock_actuel decimal(10,2) NOT NULL DEFAULT 0,
                    stock_min decimal(10,2) NOT NULL DEFAULT 0,
                    prix_unitaire decimal(10,2) NOT NULL DEFAULT 0,
                    created_at timestamp NULL DEFAULT NULL,
                    updated_at timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (id),
                    KEY produits_reference_index (reference),
                    KEY produits_stock_actuel_index (stock_actuel)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $this->info('✅ Table produits créée');
            } else {
                $this->info('ℹ️  Table produits existe déjà');
            }

            $this->info('');
            $this->info('🎯 Vérification finale:');
            $this->info('- operations: ' . (Schema::hasTable('operations') ? '✅' : '❌'));
            $this->info('- vehicules: ' . (Schema::hasTable('vehicules') ? '✅' : '❌'));
            $this->info('- pointages: ' . (Schema::hasTable('pointages') ? '✅' : '❌'));
            $this->info('- factures: ' . (Schema::hasTable('factures') ? '✅' : '❌'));
            $this->info('- notifications: ' . (Schema::hasTable('notifications') ? '✅' : '❌'));
            $this->info('- produits: ' . (Schema::hasTable('produits') ? '✅' : '❌'));

            $this->info('');
            $this->info('🟢 TABLES CRÉÉES AVEC SUCCÈS !');
            $this->info('✨ Opération terminée !');

        } catch (Exception $e) {
            $this->error('❌ Erreur: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
