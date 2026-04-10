<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@kenamservices.net'],
            [
                'name' => 'Super Administrateur',
                'email' => 'admin@kenamservices.net',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super Admin créé: admin@kenamservices.net / admin123');
    }
}
