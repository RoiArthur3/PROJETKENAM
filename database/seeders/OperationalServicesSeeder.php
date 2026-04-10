<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OperationalServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Services opérationnels avec emails éditables
        $services = [
            [
                'nom' => 'Service Opérations',
                'code' => 'OPS',
                'description' => 'Gestion des opérations quotidiennes et requêtes',
                'email' => 'operations@kenamservices.com',
                'password' => '12345678', // Mot de passe par défaut
                'telephone' => '+225 27 20 30 40',
                'responsable' => 'Chef des Opérations',
                'couleur' => '#007bff',
                'icone' => 'fas fa-cogs',
                'actif' => true,
                'ordre' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Service Logistique',
                'code' => 'LOG',
                'description' => 'Gestion de la logistique et des transports',
                'email' => 'logistique@kenamservices.com',
                'password' => '12345678',
                'telephone' => '+225 27 20 30 41',
                'responsable' => 'Chef Logistique',
                'couleur' => '#fd7e14',
                'icone' => 'fas fa-truck',
                'actif' => true,
                'ordre' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Service Technique',
                'code' => 'TECH',
                'description' => 'Maintenance technique et support matériel',
                'email' => 'technique@kenamservices.com',
                'password' => '12345678',
                'telephone' => '+225 27 20 30 42',
                'responsable' => 'Chef Technique',
                'couleur' => '#6f42c1',
                'icone' => 'fas fa-tools',
                'actif' => true,
                'ordre' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Service Commercial',
                'code' => 'COMM',
                'description' => 'Relations clients et développement commercial',
                'email' => 'commercial@kenamservices.com',
                'password' => '12345678',
                'telephone' => '+225 27 20 30 43',
                'responsable' => 'Chef Commercial',
                'couleur' => '#ffc107',
                'icone' => 'fas fa-briefcase',
                'actif' => true,
                'ordre' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Service Financier',
                'code' => 'FIN',
                'description' => 'Gestion financière et comptabilité',
                'email' => 'finance@kenamservices.com',
                'password' => '12345678',
                'telephone' => '+225 27 20 30 44',
                'responsable' => 'Directeur Financier',
                'couleur' => '#17a2b8',
                'icone' => 'fas fa-calculator',
                'actif' => true,
                'ordre' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Service Ressources Humaines',
                'code' => 'RH',
                'description' => 'Gestion du personnel et administration RH',
                'email' => 'rh@kenamservices.com',
                'password' => '12345678',
                'telephone' => '+225 27 20 30 45',
                'responsable' => 'Directeur RH',
                'couleur' => '#dc3545',
                'icone' => 'fas fa-users-cog',
                'actif' => true,
                'ordre' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Service Informatique',
                'code' => 'IT',
                'description' => 'Support informatique et gestion des systèmes',
                'email' => 'informatique@kenamservices.com',
                'password' => '12345678',
                'telephone' => '+225 27 20 30 46',
                'responsable' => 'Directeur Informatique',
                'couleur' => '#28a745',
                'icone' => 'fas fa-laptop-code',
                'actif' => true,
                'ordre' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Service Qualité',
                'code' => 'QUAL',
                'description' => 'Contrôle qualité et amélioration continue',
                'email' => 'qualite@kenamservices.com',
                'password' => '12345678',
                'telephone' => '+225 27 20 30 47',
                'responsable' => 'Responsable Qualité',
                'couleur' => '#e83e8c',
                'icone' => 'fas fa-award',
                'actif' => true,
                'ordre' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Service Achats',
                'code' => 'ACH',
                'description' => 'Gestion des achats et fournisseurs',
                'email' => 'achats@kenamservices.com',
                'password' => '12345678',
                'telephone' => '+225 27 20 30 48',
                'responsable' => 'Chef des Achats',
                'couleur' => '#6c757d',
                'icone' => 'fas fa-shopping-cart',
                'actif' => true,
                'ordre' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Service Juridique',
                'code' => 'JUR',
                'description' => 'Conseil juridique et conformité',
                'email' => 'juridique@kenamservices.com',
                'password' => '12345678',
                'telephone' => '+225 27 20 30 49',
                'responsable' => 'Directeur Juridique',
                'couleur' => '#343a40',
                'icone' => 'fas fa-gavel',
                'actif' => true,
                'ordre' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insérer les services dans la table services_operationnels
        DB::table('services_operationnels')->insert($services);

        $this->command->info('Services opérationnels créés avec succès !');
        $this->command->info('Emails configurés avec mot de passe: 12345678');
    }
}
