<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCommandeSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commande:create-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creer le systeme de gestion des commandes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creation du Systeme de Commandes - KENAM SERVICES');
        $this->info('==================================================');

        try {
            // Creer les tables si elles n'existent pas
            $this->creerTables();

            // Inserer des donnees de test
            $this->insererDonneesTest();

            // Creer les vues pour les requetes complexes
            $this->creerVues();

            $this->info('');
            $this->info('Systeme de commandes cree avec succes !');
            $this->info('Fonctionnalites :');
            $this->info('- Gestion du materiel roulant');
            $this->info('- Gestion des chauffeurs');
            $this->info('- Affectation chauffeurs-vehicules');
            $this->info('- Historique des commandes');
            $this->info('');
            $this->info('SYSTEME PRET A UTILISER !');
            $this->info('Operation terminee !');

        } catch (Exception $e) {
            $this->error('Erreur: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }

    /**
     * Creer les tables necessaires
     */
    private function creerTables()
    {
        $tables = [
            'materiel_roulant',
            'chauffeurs',
            'affectations',
            'historique_commandes'
        ];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $this->info("Creation de la table {$table}...");
                $this->{"runMigrationFor" . ucfirst($table)}();
            } else {
                $this->info("Table {$table} existe deja");
            }
        }
    }

    /**
     * Creer la table materiel_roulant
     */
    private function runMigrationForMaterielRoulant()
    {
        DB::statement("CREATE TABLE materiel_roulant (
            id bigint unsigned NOT NULL AUTO_INCREMENT,
            immatriculation varchar(255) NOT NULL UNIQUE,
            marque varchar(255) NOT NULL,
            modele varchar(255) NOT NULL,
            type_materiel varchar(50) NOT NULL DEFAULT 'vehicule',
            categorie varchar(50) NOT NULL DEFAULT 'utilitaire',
            annee int NOT NULL,
            couleur varchar(50) NULL,
            numero_serie varchar(255) NULL,
            valeur_achat decimal(10,2) NOT NULL,
            valeur_actuelle decimal(10,2) NOT NULL,
            kilometrage decimal(10,2) NOT NULL DEFAULT 0,
            kilometrage_annuel decimal(10,2) NOT NULL DEFAULT 0,
            date_achat date NOT NULL,
            date_mise_en_service date NOT NULL,
            date_fin_service date NULL,
            date_derniere_maintenance date NULL,
            date_prochaine_maintenance date NULL,
            statut enum('actif','en_maintenance','en_reparation','hors_service','vendu') NOT NULL DEFAULT 'actif',
            localisation varchar(255) NULL,
            description text NULL,
            photo varchar(255) NULL,
            proprietaire enum('entreprise','personnel','loue') NOT NULL DEFAULT 'entreprise',
            responsable_id bigint unsigned NULL,
            created_by bigint unsigned NULL,
            updated_by bigint unsigned NULL,
            created_at timestamp NULL DEFAULT NULL,
            updated_at timestamp NULL DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY materiel_roulant_immatriculation_unique (immatriculation),
            KEY materiel_roulant_statut_index (statut),
            KEY materiel_roulant_type_materiel_index (type_materiel),
            KEY materiel_roulant_categorie_index (categorie),
            KEY materiel_roulant_proprietaire_index (proprietaire),
            KEY materiel_roulant_date_achat_index (date_achat),
            KEY materiel_roulant_responsable_id_foreign (responsable_id),
            KEY materiel_roulant_created_by_foreign (created_by),
            KEY materiel_roulant_updated_by_foreign (updated_by)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    /**
     * Creer la table chauffeurs
     */
    private function runMigrationForChauffeurs()
    {
        DB::statement("CREATE TABLE chauffeurs (
            id bigint unsigned NOT NULL AUTO_INCREMENT,
            nom varchar(255) NOT NULL,
            prenom varchar(255) NOT NULL,
            telephone varchar(255) NOT NULL,
            email varchar(255) NOT NULL UNIQUE,
            adresse varchar(255) NOT NULL,
            numero_permis varchar(255) NOT NULL UNIQUE,
            date_embauche date NOT NULL,
            statut enum('actif','en_conge','suspendu','licencie') NOT NULL DEFAULT 'actif',
            categorie_permis varchar(10) NOT NULL DEFAULT 'B',
            photo varchar(255) NULL,
            notes text NULL,
            created_by bigint unsigned NULL,
            updated_by bigint unsigned NULL,
            created_at timestamp NULL DEFAULT NULL,
            updated_at timestamp NULL DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY chauffeurs_email_unique (email),
            UNIQUE KEY chauffeurs_numero_permis_unique (numero_permis),
            KEY chauffeurs_statut_index (statut),
            KEY chauffeurs_categorie_permis_index (categorie_permis),
            KEY chauffeurs_created_by_foreign (created_by),
            KEY chauffeurs_updated_by_foreign (updated_by)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    /**
     * Creer la table affectations
     */
    private function runMigrationForAffectations()
    {
        DB::statement("CREATE TABLE affectations (
            id bigint unsigned NOT NULL AUTO_INCREMENT,
            reference_affectation varchar(255) NOT NULL UNIQUE,
            chauffeur_id bigint unsigned NOT NULL,
            materiel_roulant_id bigint unsigned NOT NULL,
            mission_id bigint unsigned NULL,
            date_debut date NOT NULL,
            date_fin date NULL,
            statut varchar(50) NOT NULL DEFAULT 'en_attente',
            observations text NULL,
            notes text NULL,
            created_by bigint unsigned NULL,
            updated_by bigint unsigned NULL,
            created_at timestamp NULL DEFAULT NULL,
            updated_at timestamp NULL DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY affectations_reference_affectation_unique (reference_affectation),
            KEY affectations_chauffeur_id_foreign (chauffeur_id),
            KEY affectations_materiel_roulant_id_foreign (materiel_roulant_id),
            KEY affectations_mission_id_foreign (mission_id),
            KEY affectations_statut_index (statut),
            KEY affectations_date_debut_index (date_debut),
            KEY affectations_created_by_foreign (created_by),
            KEY affectations_updated_by_foreign (updated_by)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    /**
     * Creer la table historique_commandes
     */
    private function runMigrationForHistoriqueCommandes()
    {
        DB::statement("CREATE TABLE historique_commandes (
            id bigint unsigned NOT NULL AUTO_INCREMENT,
            reference_commande varchar(255) NOT NULL UNIQUE,
            type_commande varchar(100) NOT NULL,
            description text NULL,
            statut enum('en_attente','en_cours','termine','annule') NOT NULL DEFAULT 'en_attente',
            date_commande date NOT NULL,
            date_debut date NULL,
            date_fin date NULL,
            lieu_depart varchar(255) NULL,
            lieu_arrivee varchar(255) NULL,
            lieu_retour varchar(255) NULL,
            kilometrage_depart decimal(10,2) DEFAULT 0,
            kilometrage_retour decimal(10,2) DEFAULT 0,
            observations text NULL,
            notes text NULL,
            chauffeur_id bigint unsigned NULL,
            materiel_roulant_id bigint unsigned NULL,
            client_id bigint unsigned NULL,
            created_by bigint unsigned NULL,
            updated_by bigint unsigned NULL,
            created_at timestamp NULL DEFAULT NULL,
            updated_at timestamp NULL DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY historique_commandes_reference_commande_unique (reference_commande),
            KEY historique_commandes_statut_index (statut),
            KEY historique_commandes_date_commande_index (date_commande),
            KEY historique_commandes_chauffeur_id_foreign (chauffeur_id),
            KEY historique_commandes_materiel_roulant_id_foreign (materiel_roulant_id),
            KEY historique_commandes_client_id_foreign (client_id),
            KEY historique_commandes_created_by_foreign (created_by),
            KEY historique_commandes_updated_by_foreign (updated_by)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    /**
     * Inserer des donnees de test
     */
    private function insererDonneesTest()
    {
        $this->info('Insertion des donnees de test...');

        // Inserer du materiel roulant
        if (DB::table('materiel_roulant')->count() == 0) {
            DB::table('materiel_roulant')->insert([
                [
                    'immatriculation' => 'ABC-123-CD',
                    'marque' => 'Toyota',
                    'modele' => 'Hiace',
                    'type_materiel' => 'vehicule',
                    'categorie' => 'utilitaire',
                    'annee' => 2020,
                    'couleur' => 'Blanc',
                    'valeur_achat' => 15000000,
                    'valeur_actuelle' => 12000000,
                    'kilometrage' => 45000,
                    'kilometrage_annuel' => 15000,
                    'date_achat' => '2020-01-15',
                    'date_mise_en_service' => '2020-02-01',
                    'statut' => 'actif',
                    'localisation' => 'Siège social',
                    'proprietaire' => 'entreprise',
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'immatriculation' => 'XYZ-789-EF',
                    'marque' => 'Mercedes',
                    'modele' => 'Sprinter',
                    'type_materiel' => 'vehicule',
                    'categorie' => 'utilitaire',
                    'annee' => 2021,
                    'couleur' => 'Gris',
                    'valeur_achat' => 20000000,
                    'valeur_actuelle' => 18000000,
                    'kilometrage' => 25000,
                    'kilometrage_annuel' => 12000,
                    'date_achat' => '2021-03-10',
                    'date_mise_en_service' => '2021-04-01',
                    'statut' => 'actif',
                    'localisation' => 'Dépôt principal',
                    'proprietaire' => 'entreprise',
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
            $this->info('2 vehicules inseres');
        }

        // Inserer des chauffeurs
        if (DB::table('chauffeurs')->count() == 0) {
            DB::table('chauffeurs')->insert([
                [
                    'nom' => 'DUPONT',
                    'prenom' => 'Jean',
                    'telephone' => '77123456789',
                    'email' => 'jean.dupont@kenamservices.net',
                    'adresse' => '123 Rue de la République, Paris',
                    'numero_permis' => '123456789',
                    'date_embauche' => '2020-01-01',
                    'statut' => 'actif',
                    'categorie_permis' => 'B',
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'nom' => 'MARTIN',
                    'prenom' => 'Pierre',
                    'telephone' => '77198765432',
                    'email' => 'pierre.martin@kenamservices.net',
                    'adresse' => '456 Avenue des Champs-Élysées, Lyon',
                    'numero_permis' => '987654321',
                    'date_embauche' => '2021-06-15',
                    'statut' => 'actif',
                    'categorie_permis' => 'B',
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
            $this->info('2 chauffeurs inseres');
        }

        // Inserer des affectations
        if (DB::table('affectations')->count() == 0) {
            $vehicules = DB::table('materiel_roulant')->get();
            $chauffeurs = DB::table('chauffeurs')->get();

            foreach ($vehicules as $index => $vehicule) {
                $chauffeur = $chauffeurs[$index % 2] ?? null;

                DB::table('affectations')->insert([
                    'reference_affectation' => 'AFF-' . str_pad(($index + 1), 4, '0'),
                    'chauffeur_id' => $chauffeur ? $chauffeur->id : null,
                    'materiel_roulant_id' => $vehicule->id,
                    'date_debut' => now()->format('Y-m-d'),
                    'date_fin' => null,
                    'statut' => $chauffeur ? 'en_cours' : 'disponible',
                    'observations' => $chauffeur ? "Affectation a {$chauffeur->nom} {$chauffeur->prenom}" : 'Véhicule disponible',
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            $this->info('2 affectations inseres');
        }

        // Inserer des commandes
        if (DB::table('historique_commandes')->count() == 0) {
            DB::table('historique_commandes')->insert([
                [
                    'reference_commande' => 'CMD-2025-001',
                    'type_commande' => 'Transport de marchandises',
                    'description' => 'Livraison de matériel vers le client A',
                    'statut' => 'en_cours',
                    'date_commande' => now()->format('Y-m-d'),
                    'date_debut' => now()->format('Y-m-d'),
                    'date_fin' => now()->addDays(2)->format('Y-m-d'),
                    'lieu_depart' => 'Siège social',
                    'lieu_arrivee' => 'Entrepôt Client A',
                    'lieu_retour' => 'Siège social',
                    'kilometrage_depart' => 45000,
                    'kilometrage_retour' => 46000,
                    'chauffeur_id' => 1,
                    'materiel_roulant_id' => 1,
                    'client_id' => 1,
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'reference_commande' => 'CMD-2025-002',
                    'type_commande' => 'Transport de personnel',
                    'description' => 'Transport équipe vers le site B',
                    'statut' => 'termine',
                    'date_commande' => now()->subDays(5)->format('Y-m-d'),
                    'date_debut' => now()->subDays(5)->format('Y-m-d'),
                    'date_fin' => now()->subDays(4)->format('Y-m-d'),
                    'lieu_depart' => 'Siège social',
                    'lieu_arrivee' => 'Site B',
                    'lieu_retour' => 'Siège social',
                    'kilometrage_depart' => 25000,
                    'kilometrage_retour' => 26000,
                    'chauffeur_id' => 2,
                    'materiel_roulant_id' => 2,
                    'client_id' => 2,
                    'created_by' => 1,
                    'created_at' => now()->subDays(5),
                    'updated_at' => now()->subDays(4)
                ]
            ]);
            $this->info('2 commandes inseres');
        }
    }

    /**
     * Creer les vues pour les requetes complexes
     */
    private function creerVues()
    {
        // Vue des affectations actives
        DB::statement("CREATE OR REPLACE VIEW affectations_actives AS
            SELECT
                a.*,
                v.immatriculation,
                v.marque,
                v.modele,
                c.nom as chauffeur_nom,
                c.prenom as chauffeur_prenom,
                c.telephone as chauffeur_telephone
            FROM affectations a
            LEFT JOIN materiel_roulant v ON a.materiel_roulant_id = v.id
            LEFT JOIN chauffeurs c ON a.chauffeur_id = c.id
            WHERE a.date_fin IS NULL OR a.date_fin > CURDATE()
            ORDER BY a.date_debut DESC
        ");

        // Vue des commandes récentes
        DB::statement("CREATE OR REPLACE VIEW commandes_recentes AS
            SELECT
                hc.*,
                v.immatriculation,
                v.marque,
                v.modele,
                c.nom as chauffeur_nom,
                c.prenom as chauffeur_prenom,
                cl.nom as client_nom
            FROM historique_commandes hc
            LEFT JOIN materiel_roulant v ON hc.materiel_roulant_id = v.id
            LEFT JOIN chauffeurs c ON hc.chauffeur_id = c.id
            LEFT JOIN clients cl ON hc.client_id = cl.id
            ORDER BY hc.date_commande DESC
            LIMIT 10
        ");

        $this->info('Vues creees avec succes');
    }
}
