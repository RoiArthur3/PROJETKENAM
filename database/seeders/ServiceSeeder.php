<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'nom' => 'Logistique',
                'email' => 'logistique@kenam.ci',
                'telephone' => '+225 01 02 03 04',
                'description' => 'Service de gestion logistique et transport',
                'actif' => true,
            ],
            [
                'nom' => 'Comptabilité',
                'email' => 'compta@kenam.ci',
                'telephone' => '+225 01 02 03 05',
                'description' => 'Service de gestion comptable et financière',
                'actif' => true,
            ],
            [
                'nom' => 'Ressources Humaines',
                'email' => 'rh@kenam.ci',
                'telephone' => '+225 01 02 03 06',
                'description' => 'Service de gestion du personnel',
                'actif' => true,
            ],
            [
                'nom' => 'Commercial',
                'email' => 'commercial@kenam.ci',
                'telephone' => '+225 01 02 03 07',
                'description' => 'Service commercial et prospection',
                'actif' => true,
            ],
            [
                'nom' => 'Maintenance',
                'email' => 'maintenance@kenam.ci',
                'telephone' => '+225 01 02 03 08',
                'description' => 'Service de maintenance et entretien',
                'actif' => true,
            ],
            [
                'nom' => 'Contrôle Qualité',
                'email' => 'qualite@kenam.ci',
                'telephone' => '+225 01 02 03 09',
                'description' => 'Service de contrôle qualité et audit',
                'actif' => true,
            ],
        ];

        foreach ($services as $service) {
            \App\Models\Service::create($service);
        }
    }
}
