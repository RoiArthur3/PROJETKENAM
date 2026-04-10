<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;

class FixCommercialPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Chercher le service Commercial
        $commercial = Service::where('nom', 'like', '%Commercial%')->first();

        if (!$commercial) {
            // Chercher avec d'autres variations
            $commercial = Service::where('nom', 'like', '%comm%')->first();
        }

        if ($commercial) {
            $this->command->info('Service trouvé: ' . $commercial->nom);
            $this->command->info('Email: ' . $commercial->email);

            // Vérifier s'il a un mot de passe
            if (empty($commercial->password)) {
                // Assigner un mot de passe par défaut
                $commercial->password = Hash::make('commercial123');
                $commercial->save();

                $this->command->info('Mot de passe assigné: commercial123');
                $this->command->info('Le service peut maintenant se connecter et modifier son mot de passe.');
            } else {
                $this->command->info('Le service a déjà un mot de passe défini.');
            }
        } else {
            $this->command->error('Aucun service "Commercial" trouvé dans la base de données.');

            // Afficher tous les services disponibles
            $services = Service::all(['id', 'nom', 'email']);
            $this->command->info('Services disponibles:');
            foreach ($services as $service) {
                $this->command->line('- ' . $service->nom . ' (' . $service->email . ')');
            }
        }
    }
}
