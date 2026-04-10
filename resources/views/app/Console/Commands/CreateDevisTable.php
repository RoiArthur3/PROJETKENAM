<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDevisTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tables:create-devis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer la table devis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Création de la Table Devis - KENAM SERVICES');
        $this->info('==================================================');

        try {
            // Table devis
            if (!Schema::hasTable('devis')) {
                DB::statement("CREATE TABLE devis (
                    id bigint unsigned NOT NULL AUTO_INCREMENT,
                    numero varchar(255) NOT NULL UNIQUE,
                    client_id bigint unsigned NOT NULL,
                    date_devis date NOT NULL,
                    date_validite date NULL,
                    montant_ht decimal(10,2) NOT NULL,
                    tva decimal(5,2) NOT NULL DEFAULT 20.00,
                    montant_ttc decimal(10,2) NOT NULL,
                    statut enum('brouillon','envoye','accepte','refuse','expire') NOT NULL DEFAULT 'brouillon',
                    description text NULL,
                    conditions text NULL,
                    created_by bigint unsigned NULL,
                    created_at timestamp NULL DEFAULT NULL,
                    updated_at timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (id),
                    KEY devis_client_id_foreign (client_id),
                    KEY devis_date_devis_index (date_devis),
                    KEY devis_statut_index (statut)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $this->info('✅ Table devis créée');
            } else {
                $this->info('ℹ️  Table devis existe déjà');
            }

            // Ajouter quelques données de test
            if (Schema::hasTable('devis') && DB::table('devis')->count() == 0) {
                $this->info('Ajout de données de test...');

                DB::table('devis')->insert([
                    [
                        'numero' => 'DEV-2025-001',
                        'client_id' => 1,
                        'date_devis' => now()->format('Y-m-d'),
                        'date_validite' => now()->addMonth(1)->format('Y-m-d'),
                        'montant_ht' => 1500.00,
                        'tva' => 20.00,
                        'montant_ttc' => 1800.00,
                        'statut' => 'brouillon',
                        'description' => 'Devis de test pour services commerciaux',
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'numero' => 'DEV-2025-002',
                        'client_id' => 2,
                        'date_devis' => now()->format('Y-m-d'),
                        'date_validite' => now()->addMonth(1)->format('Y-m-d'),
                        'montant_ht' => 2500.00,
                        'tva' => 20.00,
                        'montant_ttc' => 3000.00,
                        'statut' => 'envoye',
                        'description' => 'Devis de test pour prestations techniques',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]);

                $this->info('✅ 2 devis de test ajoutés');
            }

            $this->info('');
            $this->info('🎯 Vérification finale:');
            $this->info('- devis: ' . (Schema::hasTable('devis') ? '✅' : '❌'));
            $this->info('- enregistrements: ' . DB::table('devis')->count());

            $this->info('');
            $this->info('🟢 TABLE DEVIS CRÉÉE AVEC SUCCÈS !');
            $this->info('✨ Opération terminée !');

        } catch (Exception $e) {
            $this->error('❌ Erreur: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
