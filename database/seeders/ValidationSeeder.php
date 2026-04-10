<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Validation;
use App\Models\User;

class ValidationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        
        if ($users->count() < 2) {
            $this->command->warn('Pas assez d\'utilisateurs pour créer des validations. Créez au moins 2 utilisateurs.');
            return;
        }

        $initiateur = $users->first();
        $validateur = $users->skip(1)->first();

        // Validation en attente
        Validation::create([
            'titre' => 'Demande de matériel informatique',
            'description' => 'Achat de 5 ordinateurs portables pour le service commercial',
            'module_source' => 'operations',
            'record_id' => 1, // ID fictif pour test
            'type' => 'achat',
            'initiateur_id' => $initiateur->id,
            'validateur_id' => null, // Non assigné
            'statut' => 'en_attente',
        ]);

        // Validation en cours
        Validation::create([
            'titre' => 'Réparation véhicule #VH-001',
            'description' => 'Réparation moteur et changement de pneus',
            'module_source' => 'parc_auto',
            'record_id' => 2,
            'type' => 'maintenance',
            'initiateur_id' => $initiateur->id,
            'validateur_id' => $validateur->id,
            'statut' => 'en_cours',
        ]);

        // Validation validée
        Validation::create([
            'titre' => 'Demande de congé - Agent Kouassi',
            'description' => 'Congé annuel du 15/11 au 30/11',
            'module_source' => 'rh',
            'record_id' => 3,
            'type' => 'conge',
            'initiateur_id' => $initiateur->id,
            'validateur_id' => $validateur->id,
            'statut' => 'valide',
            'date_validation' => now(),
        ]);

        // Validation rejetée
        Validation::create([
            'titre' => 'Sortie stock - 100 unités',
            'description' => 'Sortie de matériel pour chantier externe',
            'module_source' => 'stock',
            'record_id' => 4,
            'type' => 'sortie',
            'initiateur_id' => $initiateur->id,
            'validateur_id' => $validateur->id,
            'statut' => 'rejete',
            'date_validation' => now()->subDays(2),
            'commentaire' => 'Stock insuffisant pour cette quantité',
        ]);

        $this->command->info('4 validations de test créées avec succès.');
    }
}
