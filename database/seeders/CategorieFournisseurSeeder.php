<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategorieFournisseur;

class CategorieFournisseurSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'nom' => 'Transport de Marchandises',
                'description' => 'Fournisseurs de services de transport routier, fret et livraison.',
                'couleur' => '#0d6efd'
            ],
            [
                'nom' => 'Location d\'Engins Lourds',
                'description' => 'Location de pelles, bulldozers et autres engins de chantier.',
                'couleur' => '#ffc107'
            ],
            [
                'nom' => 'Maintenance & Garage',
                'description' => 'Entretien, réparation et suivi technique des véhicules.',
                'couleur' => '#198754'
            ],
            [
                'nom' => 'Carburants & Lubrifiants',
                'description' => 'Stations-service et grossistes en produits pétroliers.',
                'couleur' => '#dc3545'
            ],
            [
                'nom' => 'Transit & Douane',
                'description' => 'Commissionnaires en douane et agents de transit.',
                'couleur' => '#6610f2'
            ],
            [
                'nom' => 'Pneumatiques & Accessoires',
                'description' => 'Vente et montage de pneus et accessoires automobiles.',
                'couleur' => '#212529'
            ],
            [
                'nom' => 'Assurances Transport',
                'description' => 'Compagnies d\'assurance pour flotte et marchandises.',
                'couleur' => '#0dcaf0'
            ],
            [
                'nom' => 'Pièces de Rechange',
                'description' => 'Vente de pièces détachées neuves ou d\'occasion.',
                'couleur' => '#fd7e14'
            ],
            [
                'nom' => 'Logistique & Entreposage',
                'description' => 'Gestion de stocks et location d\'entrepôts.',
                'couleur' => '#6f42c1'
            ],
            [
                'nom' => 'Sécurité & Géolocalisation',
                'description' => 'Solutions de tracking et sécurité des convois.',
                'couleur' => '#20c997'
            ],
        ];

        foreach ($categories as $cat) {
            CategorieFournisseur::updateOrCreate(['nom' => $cat['nom']], $cat);
        }
    }
}
