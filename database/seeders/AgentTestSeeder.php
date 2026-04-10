<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AgentTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Créer ou récupérer le rôle agent
        $agentRole = Role::where('name', 'agent')->first();

        if (!$agentRole) {
            $agentRole = Role::create([
                'name' => 'agent',
                'guard_name' => 'web'
            ]);
            $this->command->info('Rôle "agent" créé avec succès.');
        }

        // Créer un utilisateur de test avec le rôle agent
        $existingAgent = User::where('email', 'agent.test@kenamservices.net')->first();

        if (!$existingAgent) {
            $agent = User::create([
                'name' => 'Agent Test',
                'email' => 'agent.test@kenamservices.net',
                'password' => Hash::make('agent123'),
                'phone' => '+221 77 123 45 67',
                'email_verified_at' => now()
            ]);

            // Assigner le rôle agent
            $agent->roles()->attach($agentRole);

            $this->command->info('Utilisateur agent créé avec succès!');
            $this->command->info('Email: agent.test@kenamservices.net');
            $this->command->info('Mot de passe: agent123');
        } else {
            $this->command->info('L\'utilisateur de test existe déjà.');
            $this->command->info('Email: agent.test@kenamservices.net');
            $this->command->info('Mot de passe: agent123');
        }

        $this->command->info("\nAccès à l\'interface agent:");
        $this->command->info("1. Connectez-vous avec agent.test@kenamservices.net / agent123");
        $this->command->info("2. Le menu 'Requêtes' dans la barre latérale vous donnera accès à:");
        $this->command->info("   - Tableau de bord agent");
        $this->command->info("   - Nouvelle requête");
        $this->command->info("   - Mes requêtes");
        $this->command->info("   - Mon profil");
    }
}
