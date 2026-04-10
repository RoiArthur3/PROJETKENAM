<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des opérations de test pour le dashboard
        \App\Models\Operation::create([
            'titre' => 'Projet Test 1',
            'description' => 'Description projet test 1',
            'type' => 'transport',
            'montant' => 500000,
            'date_operation' => now(),
            'statut_courant' => 'en_cours',
            'priorite' => 'haute',
            'echeance' => now()->addDays(30)
        ]);

        \App\Models\Operation::create([
            'titre' => 'Projet Test 2',
            'description' => 'Description projet test 2',
            'type' => 'logistique',
            'montant' => 750000,
            'date_operation' => now()->subDays(15),
            'statut_courant' => 'terminee',
            'priorite' => 'moyenne',
            'echeance' => now()->subDays(5)
        ]);

        \App\Models\Operation::create([
            'titre' => 'Projet Test 3',
            'description' => 'Description projet test 3',
            'type' => 'transport',
            'montant' => 300000,
            'date_operation' => now()->subDays(10),
            'statut_courant' => 'en_cours',
            'priorite' => 'basse',
            'echeance' => now()->subDays(2) // En retard
        ]);

        \App\Models\Operation::create([
            'titre' => 'Projet Test 4',
            'description' => 'Description projet test 4',
            'type' => 'logistique',
            'montant' => 1200000,
            'date_operation' => now()->subDays(5),
            'statut_courant' => 'terminee',
            'priorite' => 'haute',
            'echeance' => now()->addDays(25)
        ]);

        $this->command->info('Opérations de test créées avec succès!');
    }
}
