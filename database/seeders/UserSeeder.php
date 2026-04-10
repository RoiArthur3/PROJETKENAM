<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Compte client simple
        $client = User::updateOrCreate(
            ['email' => 'client@simple.com'],
            [
                'name' => 'Client Simple',
                'password' => Hash::make('password123'),
                'role' => 'client',
                'account_status' => 'active',
                'client_type' => 'individual',
                'notification_preference' => 'email',
                'auto_billing' => false,
                'is_active' => true,
            ]
        );

        // Compte administrateur
        $admin = User::updateOrCreate(
            ['email' => 'admin@groupage.com'],
            [
                'name' => 'Admin Groupage',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'account_status' => 'active',
                'client_type' => 'business',
                'notification_preference' => 'email',
                'auto_billing' => false,
                'is_active' => true,
            ]
        );

        // Compte agent (backoffice)
        $agent = User::updateOrCreate(
            ['email' => 'agent@groupage.com'],
            [
                'name' => 'Agent Backoffice',
                'password' => Hash::make('agent123'),
                'role' => 'agent',
                'account_status' => 'active',
                'client_type' => 'business',
                'notification_preference' => 'email',
                'auto_billing' => false,
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Utilisateurs créés avec succès:');
        $this->command->info('👤 Client: client@simple.com / password123');
        $this->command->info('👨‍💼 Admin: admin@groupage.com / admin123');
        $this->command->info('🧑‍💼 Agent: agent@groupage.com / agent123');
    }
}
