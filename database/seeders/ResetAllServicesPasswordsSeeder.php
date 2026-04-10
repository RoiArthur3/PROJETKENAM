<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;

class ResetAllServicesPasswordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('=== Réinitialisation des mots de passe de tous les services ===');
        $this->command->info('');

        // Récupérer tous les services
        $services = Service::all();

        if ($services->isEmpty()) {
            $this->command->error('Aucun service trouvé dans la base de données!');
            return;
        }

        $defaultPassword = 'password';
        $passwordHash = Hash::make($defaultPassword);

        foreach ($services as $service) {
            // Mettre à jour le mot de passe
            $service->password = $passwordHash;
            $service->save();

            $this->command->info('Service: ' . $service->nom);
            $this->command->info('  Email: ' . $service->email);
            $this->command->info('  Mot de passe: ' . $defaultPassword);
            $this->command->info('  Actif: ' . ($service->actif ? 'Oui' : 'Non'));
            $this->command->info('');
        }

        $this->command->info('=== Instructions de connexion ===');
        $this->command->info('1. Allez sur: /services/login');
        $this->command->info('2. Utilisez l\'email et le mot de passe "password" pour n\'importe quel service');
        $this->command->info('3. Accédez à votre profil pour changer le mot de passe');
        $this->command->info('');
        $this->command->info('Services disponibles:');
        foreach ($services as $service) {
            $this->command->line('- ' . $service->nom . ' (' . $service->email . ')');
        }
    }
}
