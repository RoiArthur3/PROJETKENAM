<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\User;
use App\Models\ServicePersonneRessource;

class CreatePersonneRessourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('=== Création des personnes ressources pour les services ===');

        // Obtenir tous les services et utilisateurs actifs
        $services = Service::where('actif', true)->get();
        $users = User::all(); // Pas de filtre actif pour la table users

        if ($services->isEmpty()) {
            $this->command->error('Aucun service actif trouvé!');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->error('Aucun utilisateur actif trouvé!');
            return;
        }

        $this->command->info('Services trouvés: ' . $services->count());
        $this->command->info('Utilisateurs trouvés: ' . $users->count());
        $this->command->info('');

        // Créer des personnes ressources pour quelques services
        $assignments = [
            // Service Commercial - Admin comme chef, User 1 comme ressource
            [
                'service' => 'Commercial',
                'chef' => 'admin@kenamservices.com',
                'ressources' => ['user1@kenamservices.com']
            ],
            // Service Comptabilité - Admin comme chef, User 2 comme ressource
            [
                'service' => 'Comptabilité',
                'chef' => 'admin@kenamservices.com',
                'ressources' => ['user2@kenamservices.com']
            ],
            // Service RH - Admin comme chef, User 3 comme ressource
            [
                'service' => 'Ressources Humaines',
                'chef' => 'admin@kenamservices.com',
                'ressources' => ['user3@kenamservices.com']
            ],
        ];

        foreach ($assignments as $assignment) {
            $service = Service::where('nom', $assignment['service'])->first();

            if (!$service) {
                $this->command->warn('Service "' . $assignment['service'] . '" non trouvé, skip...');
                continue;
            }

            // Ajouter le chef de service
            $chefUser = User::where('email', $assignment['chef'])->first();
            if ($chefUser && !ServicePersonneRessource::where('service_id', $service->id)->where('user_id', $chefUser->id)->exists()) {
                ServicePersonneRessource::create([
                    'service_id' => $service->id,
                    'user_id' => $chefUser->id,
                    'role' => 'chef_service',
                    'recevoir_emails' => true
                ]);
                $this->command->info('Chef de service ajouté: ' . $chefUser->name . ' -> ' . $service->nom);
            }

            // Ajouter les personnes ressources
            foreach ($assignment['ressources'] as $resourceEmail) {
                $resourceUser = User::where('email', $resourceEmail)->first();
                if ($resourceUser && !ServicePersonneRessource::where('service_id', $service->id)->where('user_id', $resourceUser->id)->exists()) {
                    ServicePersonneRessource::create([
                        'service_id' => $service->id,
                        'user_id' => $resourceUser->id,
                        'role' => 'personne_ressource',
                        'recevoir_emails' => true
                    ]);
                    $this->command->info('Personne ressource ajoutée: ' . $resourceUser->name . ' -> ' . $service->nom);
                }
            }

            $this->command->info('');
        }

        // Afficher un résumé
        $this->command->info('=== Résumé des personnes ressources créées ===');
        $services->each(function ($service) {
            $count = ServicePersonneRessource::where('service_id', $service->id)->count();
            if ($count > 0) {
                $emails = $service->getEmailsPersonnesRessources();
                $this->command->line($service->nom . ': ' . $count . ' personne(s) ressource(s)');
                $this->command->line('  Emails: ' . implode(', ', $emails));
                $this->command->line('');
            }
        });

        $this->command->info('Les emails seront envoyés à ces personnes lors des notifications de service.');
    }
}
