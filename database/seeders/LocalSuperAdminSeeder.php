<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LocalSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'telephone' => '0000000000'
            ],
            [
                'name' => 'Super Admin Local',
                'email' => 'superadmin@local.com',
                'password' => Hash::make('0000000000'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
        $this->command->info('Super administrateur local créé : 0000000000 / 0000000000');
    }
}
