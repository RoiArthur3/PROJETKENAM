<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;

class TestCommercialAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Chercher le service Commercial
        $commercial = Service::where('nom', 'Commercial')->first();

        if ($commercial) {
            $this->command->info('=== Service Commercial Trouvé ===');
            $this->command->info('ID: ' . $commercial->id);
            $this->command->info('Nom: ' . $commercial->nom);
            $this->command->info('Email: ' . $commercial->email);
            $this->command->info('Actif: ' . ($commercial->actif ? 'Oui' : 'Non'));
            $this->command->info('A un mot de passe: ' . (empty($commercial->password) ? 'Non' : 'Oui'));

            // Réinitialiser le mot de passe si nécessaire
            if (empty($commercial->password)) {
                $commercial->password = Hash::make('commercial123');
                $commercial->save();
                $this->command->info('Mot de passe réinitialisé à: commercial123');
            } else {
                $this->command->info('Le service a déjà un mot de passe.');
                $this->command->info('Pour tester: utilisez commercial@kenamservices.com avec le mot de passe existant');
            }

            $this->command->info('');
            $this->command->info('=== Instructions d\'accès ===');
            $this->command->info('1. Allez sur: /services/login');
            $this->command->info('2. Email: commercial@kenamservices.com');
            $this->command->info('3. Mot de passe: commercial123 (si réinitialisé)');
            $this->command->info('4. Cliquez sur "Mon Profil" dans le menu');
            $this->command->info('5. Les champs de mot de passe devraient être visibles en bas du formulaire');

        } else {
            $this->command->error('Service "Commercial" non trouvé!');

            // Créer le service s'il n'existe pas
            $this->command->info('Création du service Commercial...');
            $commercial = Service::create([
                'nom' => 'Commercial',
                'email' => 'commercial@kenamservices.com',
                'password' => Hash::make('commercial123'),
                'phone' => '+221 33 123 45 67',
                'actif' => true,
                'description' => 'Service commercial de KENAM SERVICES'
            ]);

            $this->command->info('Service Commercial créé avec succès!');
            $this->command->info('Email: commercial@kenamservices.com');
            $this->command->info('Mot de passe: commercial123');
        }
    }
}
