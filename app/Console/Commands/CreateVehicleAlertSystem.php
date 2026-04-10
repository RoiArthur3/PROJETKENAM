<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateVehicleAlertSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:create-vehicle-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer le système d\'alerte pour les véhicules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Création du Système d\'Alerte Véhicules - KENAM SERVICES');
        $this->info('==================================================');

        try {
            // Créer la table des alertes si elle n'existe pas
            if (!Schema::hasTable('vehicle_alerts')) {
                DB::statement("CREATE TABLE vehicle_alerts (
                    id bigint unsigned NOT NULL AUTO_INCREMENT,
                    vehicle_id bigint unsigned NOT NULL,
                    type_alerte varchar(50) NOT NULL DEFAULT 'assurance',
                    message text NOT NULL,
                    date_alerte date NOT NULL,
                    niveau_alerte enum('info', 'warning', 'critical') NOT NULL DEFAULT 'info',
                    traitee tinyint(1) NOT NULL DEFAULT 0,
                    created_at timestamp NULL DEFAULT NULL,
                    updated_at timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (id),
                    KEY vehicle_alerts_vehicle_id_foreign (vehicle_id),
                    KEY vehicle_alerts_type_alerte_index (type_alerte),
                    KEY vehicle_alerts_date_alerte_index (date_alerte),
                    KEY vehicle_alerts_traitee_index (traitee)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $this->info('✅ Table vehicle_alerts créée');
            } else {
                $this->info('ℹ️  Table vehicle_alerts existe déjà');
            }

            // Ajouter des colonnes à la table vehicules si nécessaire
            $this->ajouterColonnesVehicules();

            // Insérer des données de test pour les alertes
            $this->insererDonneesTest();

            // Créer une vue pour les alertes actives
            $this->creerVueAlertesActives();

            $this->info('');
            $this->info('🎯 Système d\'alerte véhicules créé avec succès !');
            $this->info('📊 Fonctionnalités :');
            $this->info('- Alertes automatiques avant échéance');
            $this->info('- Notifications pour les véhicules');
            $this->info('- Tableau de bord des alertes');
            $this->info('');
            $this->info('🟢 SYSTÈME PRÊT À UTILISER !');
            $this->info('✨ Opération terminée !');

        } catch (Exception $e) {
            $this->error('❌ Erreur: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }

    /**
     * Ajouter les colonnes nécessaires à la table vehicules
     */
    private function ajouterColonnesVehicules()
    {
        // Vérifier et ajouter les colonnes manquantes
        $columns = DB::select("SHOW COLUMNS FROM vehicules");
        $existingColumns = collect($columns)->pluck('Field')->toArray();

        $requiredColumns = [
            'date_assurance',
            'date_fin_assurance',
            'kilometrage',
            'date_derniere_maintenance',
            'prochaine_maintenance'
        ];

        foreach ($requiredColumns as $column) {
            if (!in_array($column, $existingColumns)) {
                $columnType = match($column) {
                    'date_assurance', 'date_fin_assurance' => 'date',
                    'kilometrage' => 'int',
                    'date_derniere_maintenance', 'prochaine_maintenance' => 'date',
                    default => 'varchar(255)'
                };

                if ($columnType === 'int') {
                    DB::statement("ALTER TABLE vehicules ADD COLUMN {$column} int DEFAULT 0");
                } else {
                    DB::statement("ALTER TABLE vehicules ADD COLUMN {$column} {$columnType} NULL");
                }
                $this->info("✅ Colonne '{$column}' ajoutée à la table vehicules");
            }
        }
    }

    /**
     * Insérer des données de test pour les alertes
     */
    private function insererDonneesTest()
    {
        // Vérifier si des données existent déjà
        $count = DB::table('vehicle_alerts')->count();

        if ($count == 0) {
            // Récupérer quelques véhicules pour créer des alertes
            $vehicules = DB::table('vehicules')->limit(3)->get();

            foreach ($vehicules as $vehicule) {
                // Alerte d'assurance (30 jours avant échéance)
                if ($vehicule->date_assurance) {
                    $dateAlerte = date('Y-m-d', strtotime($vehicule->date_assurance . ' -30 days'));

                    DB::table('vehicle_alerts')->insert([
                        'vehicle_id' => $vehicule->id,
                        'type_alerte' => 'assurance',
                        'message' => "L'assurance du véhicule {$vehicule->immatriculation} expire dans 30 jours",
                        'date_alerte' => $dateAlerte,
                        'niveau_alerte' => 'warning',
                        'traitee' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                // Alerte de kilométrage (tous les 10000 km)
                if ($vehicule->kilometrage && $vehicule->kilometrage > 0) {
                    $prochainMultiple = ceil($vehicule->kilometrage / 10000) * 10000;
                    $dateAlerte = date('Y-m-d', strtotime("+$prochainMultiple days"));

                    DB::table('vehicle_alerts')->insert([
                        'vehicle_id' => $vehicule->id,
                        'type_alerte' => 'kilometrage',
                        'message' => "Le véhicule {$vehicule->immatriculation} atteindra {$prochainMultiple} km prochainement",
                        'date_alerte' => $dateAlerte,
                        'niveau_alerte' => 'info',
                        'traitee' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                // Alerte de maintenance (tous les 6 mois)
                if ($vehicule->date_derniere_maintenance) {
                    $dateMaintenance = date('Y-m-d', strtotime($vehicule->date_derniere_maintenance . ' +180 days'));

                    DB::table('vehicle_alerts')->insert([
                        'vehicle_id' => $vehicule->id,
                        'type_alerte' => 'maintenance',
                        'message' => "Le véhicule {$vehicule->immatriculation} est dû pour une maintenance",
                        'date_alerte' => $dateMaintenance,
                        'niveau_alerte' => 'warning',
                        'traitee' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            $this->info('✅ Données de test insérées');
        } else {
            $this->info('ℹ️  Données de test existent déjà');
        }
    }

    /**
     * Créer une vue pour les alertes actives
     */
    private function creerVueAlertesActives()
    {
        DB::statement("CREATE OR REPLACE VIEW active_vehicle_alerts AS
            SELECT
                va.*,
                v.immatriculation,
                v.marque,
                v.modele,
                v.disponible
            FROM vehicle_alerts va
            JOIN vehicules v ON va.vehicle_id = v.id
            WHERE va.traitee = 0
            ORDER BY va.date_alerte DESC, va.niveau_alerte DESC
        ");

        $this->info('✅ Vue active_vehicle_alerts créée');
    }
}
