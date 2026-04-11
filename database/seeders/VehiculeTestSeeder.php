<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicule;
use Illuminate\Support\Facades\DB;

class VehiculeTestSeeder extends Seeder
{
    public function run()
    {
        // Vérifier si la table vehicules existe
        if (!DB::getSchemaBuilder()->hasTable('vehicules')) {
            $this->command->info('La table vehicules n\'existe pas.');
            return;
        }

        // Créer des véhicules de test
        $vehicules = [
            [
                'immatriculation' => 'CI-123-AB',
                'marque' => 'Toyota',
                'modele' => 'Hilux',
                'type_materiel' => 'Camion',
                'prix_location' => 25000,
                'prix_achat' => 20000,
                'statut' => 'actif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'immatriculation' => 'CI-456-CD',
                'marque' => 'JCB',
                'modele' => '3CX',
                'type_materiel' => 'Engin',
                'prix_location' => 35000,
                'prix_achat' => 28000,
                'statut' => 'actif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'immatriculation' => 'CI-789-EF',
                'marque' => 'Caterpillar',
                'modele' => '320D',
                'type_materiel' => 'Machine',
                'prix_location' => 45000,
                'prix_achat' => 35000,
                'statut' => 'actif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'immatriculation' => 'CI-101-GH',
                'marque' => 'Mercedes',
                'modele' => 'Actros',
                'type_materiel' => 'Camion',
                'prix_location' => 40000,
                'prix_achat' => 32000,
                'statut' => 'actif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($vehicules as $vehicule) {
            Vehicule::updateOrCreate(
                ['immatriculation' => $vehicule['immatriculation']],
                $vehicule
            );
        }

        $this->command->info('Véhicules de test créés avec succès!');
    }
}
