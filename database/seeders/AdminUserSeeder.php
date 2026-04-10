<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@kenam.ci',
            'password' => Hash::make('Kenam@2025!'),
            'role' => 'admin',
            'can_access_dashboard' => 1,
            'can_access_operations' => 1,
            'can_access_hr' => 1,
            'can_access_fleet' => 1,
            'can_access_suppliers' => 1,
            'can_access_warehouse' => 1,
            'can_access_accounting' => 1,
            'can_access_invoicing' => 1,
            'can_access_reporting' => 1,
            'can_access_commercial' => 1,
            'can_access_prospection' => 1,
            'can_access_ateliers' => 1,
            'email_verified_at' => now(),
        ]);
    }
}