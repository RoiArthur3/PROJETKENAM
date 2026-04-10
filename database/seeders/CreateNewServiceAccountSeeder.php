<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;

class CreateNewServiceAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('=== Création d\'un nouveau compte service ===');

        // Créer un nouveau service "Support Technique"
        $service = Service::create([
            'nom' => 'Support Technique',
            'email' => 'support@kenamservices.net',
            'password' => Hash::make('support123'),
            'phone' => '+221 33 987 65 43',
            'actif' => true,
            'description' => 'Service support technique pour l\'assistance aux utilisateurs et maintenance des systèmes'
        ]);

        $this->command->info('Service créé avec succès !');
        $this->command->info('');
        $this->command->info('=== Identifiants de connexion ===');
        $this->command->info('URL de connexion: http://127.0.0.1:8000/login');
        $this->command->info('Email: support@kenamservices.com');
        $this->command->info('Mot de passe: support123');
        $this->command->info('');
        $this->command->info('=== Informations du service ===');
        $this->command->info('Nom: ' . $service->nom);
        $this->command->info('Email: ' . $service->email);
        $this->command->info('Téléphone: ' . $service->phone);
        $this->command->info('Actif: ' . ($service->actif ? 'Oui' : 'Non'));
        $this->command->info('');
        $this->command->info('=== Instructions ===');
        $this->command->info('1. Allez sur: http://127.0.0.1:8000/login');
        $this->command->info('2. Entrez l\'email: support@kenamservices.com');
        $this->command->info('3. Entrez le mot de passe: support123');
        $this->command->info('4. Cliquez sur "Se connecter"');
        $this->command->info('5. Vous serez redirigé vers le tableau de bord du service');
        $this->command->info('6. Vous pourrez accéder à votre profil pour changer le mot de passe');
    }
}
