<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ServicePasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ajouter des mots de passe aux services existants pour la démo
        $services = Service::whereNull('password')->get();

        foreach ($services as $service) {
            // Utiliser un mot de passe par défaut basé sur le nom du service
            $defaultPassword = 'service' . $service->id . '2025';

            $service->update([
                'password' => Hash::make($defaultPassword)
            ]);

            $this->command->info("Mot de passe ajouté pour le service: {$service->nom} (Email: {$service->email}, Mot de passe: {$defaultPassword})");
        }

        // Créer un service de test si aucun n'existe
        if (Service::count() === 0) {
            $testService = Service::create([
                'nom' => 'Service Test',
                'email' => 'service.test@kenamservices.com',
                'password' => Hash::make('test123'),
                'phone' => '+221 77 123 45 67',
                'description' => 'Service de test pour démonstration',
                'actif' => true
            ]);

            $this->command->info("Service de test créé: {$testService->email} avec mot de passe: test123");
        }

        $this->command->info('Seeder ServicePasswordSeeder terminé avec succès!');
    }
}
