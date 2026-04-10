<?php
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Script à lancer avec: php artisan tinker ou en migration/seeder
DB::table('users')->insert([
    'name' => 'Super Admin',
    'email' => 'superadmin@kenam.local',
    'telephone' => '0554419341',
    'password' => Hash::make('00000000'),
    'role' => 'superadmin',
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "Superadmin créé avec le téléphone 0554419341 et mot de passe 00000000\n";
