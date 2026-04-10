<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création de l'utilisateur administrateur
        $user = User::firstOrCreate(
            ['email' => 'admin@kenam.ci'],
            [
                'name' => 'Super Admin',
                'telephone' => '+22507070000000',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Assigner tous les rôles à l'administrateur
        $user->syncRoles(['admin', 'super-admin']);

        $this->command->info('Super administrateur créé avec succès !');
        $this->command->info('Email: admin@kenam.ci');
        $this->command->info('Téléphone: +22507070000000');
        $this->command->info('Mot de passe: admin123');
    }
}
