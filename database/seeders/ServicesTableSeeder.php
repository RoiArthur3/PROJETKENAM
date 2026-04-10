<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicesTableSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        $services = [
            [
                'nom' => 'Service Technique',
                'email' => 'technique@kenamservices.com',
                'telephone' => '+221 77 123 45 67',
                'description' => 'Service en charge de la maintenance et des réparations techniques',
                'actif' => true,
            ],
            [
                'nom' => 'Service Qualité',
                'email' => 'qualite@kenamservices.com',
                'telephone' => '+221 77 234 56 78',
                'description' => 'Service en charge du contrôle qualité et de la conformité',
                'actif' => true,
            ],
            [
                'nom' => 'Service Logistique',
                'email' => 'logistique@kenamservices.com',
                'telephone' => '+221 77 345 67 89',
                'description' => 'Service en charge de la gestion des flux et des approvisionnements',
                'actif' => true,
            ],
            [
                'nom' => 'Service Achats',
                'email' => 'achats@kenamservices.com',
                'telephone' => '+221 77 456 78 90',
                'description' => 'Service en charge des achats et des fournisseurs',
                'actif' => true,
            ],
            [
                'nom' => 'Direction Générale',
                'email' => 'direction@kenamservices.com',
                'telephone' => '+221 77 567 89 01',
                'description' => 'Direction générale de l\'entreprise',
                'actif' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['email' => $service['email']],
                $service
            );
        }
    }
}
