<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ServiceOperationnelSeeder extends Seeder
{
    public function run()
    {
        // Services à créer ou mettre à jour
        $services = [
            [
                'nom' => 'Direction Générale',
                'code' => 'DG-001',
                'description' => 'Direction générale de l\'entreprise',
                'email' => 'dg@kenamservices.net',
                'telephone' => '+228 22 00 00 00',
                'responsable' => 'Directeur Général',
                'couleur' => '#dc3545',
                'icone' => 'fas fa-crown',
                'actif' => true,
                'ordre' => 4,
            ],
            [
                'nom' => 'Informatique',
                'code' => 'IT-005',
                'description' => 'Gestion des systèmes d\'information et du support technique',
                'email' => 'it@kenamservices.net',
                'telephone' => '+228 22 00 00 04',
                'responsable' => 'Responsable IT',
                'couleur' => '#6f42c1',
                'icone' => 'fas fa-laptop',
                'actif' => true,
                'ordre' => 5,
            ],
            [
                'nom' => 'Marketing et Communication',
                'code' => 'MKT-006',
                'description' => 'Marketing, communication et relations publiques',
                'email' => 'marketing@kenamservices.net',
                'telephone' => '+228 22 00 00 05',
                'responsable' => 'Responsable Marketing',
                'couleur' => '#e83e8c',
                'icone' => 'fas fa-bullhorn',
                'actif' => true,
                'ordre' => 6,
            ],
            [
                'nom' => 'Juridique et Conformité',
                'code' => 'JUR-007',
                'description' => 'Affaires juridiques et conformité réglementaire',
                'email' => 'juridique@kenamservices.net',
                'telephone' => '+228 22 00 00 06',
                'responsable' => 'Responsable Juridique',
                'couleur' => '#6c757d',
                'icone' => 'fas fa-gavel',
                'actif' => true,
                'ordre' => 7,
            ],
            [
                'nom' => 'Qualité et Sécurité',
                'code' => 'QLT-008',
                'description' => 'Contrôle qualité, sécurité et environnement',
                'email' => 'qualite@kenamservices.net',
                'telephone' => '+228 22 00 00 07',
                'responsable' => 'Responsable Qualité',
                'couleur' => '#17a2b8',
                'icone' => 'fas fa-shield-alt',
                'actif' => true,
                'ordre' => 8,
            ],
            [
                'nom' => 'Commercial et Ventes',
                'code' => 'COM-009',
                'description' => 'Développement commercial et gestion des ventes',
                'email' => 'commercial@kenamservices.net',
                'telephone' => '+228 22 00 00 08',
                'responsable' => 'Responsable Commercial',
                'couleur' => '#fd7e14',
                'icone' => 'fas fa-handshake',
                'actif' => true,
                'ordre' => 9,
            ],
            [
                'nom' => 'Maintenance Technique',
                'code' => 'MAINT-010',
                'description' => 'Maintenance des équipements et infrastructures',
                'email' => 'maintenance@kenamservices.net',
                'telephone' => '+228 22 00 00 09',
                'responsable' => 'Responsable Maintenance',
                'couleur' => '#20c997',
                'icone' => 'fas fa-tools',
                'actif' => true,
                'ordre' => 10,
            ],
        ];

        // Insérer ou mettre à jour les services
        foreach ($services as $service) {
            \App\Models\ServiceOperationnel::updateOrCreate(
                ['nom' => $service['nom']], // Critère de recherche
                $service // Données à insérer/mettre à jour
            );
        }

        $this->command->info('Services opérationnels créés ou mis à jour avec succès !');
    }
}
