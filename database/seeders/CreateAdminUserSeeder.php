<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserSeeder extends Seeder
{
    public function run()
    {
        // Créer un utilisateur admin
        User::updateOrCreate(
            ['email' => 'admin@kenamservices.net'],
            [
                'name' => 'Administrateur',
                'email' => 'admin@kenamservices.net',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Utilisateur admin créé: admin@kenamservices.net / admin123');
    }
}
