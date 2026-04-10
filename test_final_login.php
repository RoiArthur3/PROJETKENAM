<?php

use Illuminate\Support\Facades\Auth;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=========================================\n";
echo "TEST FINAL - LOGIN ADMIN\n";
echo "=========================================\n\n";

echo "1. CONFIGURATION AUTH ACTUELLE\n";
echo "--------------------------------\n";
echo "Username field: " . config('auth.providers.users.username') . "\n";
echo "Status: " . (config('auth.providers.users.username') === 'email' ? '✓ OK' : '✗ ERREUR') . "\n";

echo "\n2. TEST LOGIN ADMIN\n";
echo "--------------------------------\n";

$testAccounts = [
    [
        'email' => 'phone@kenam.com',
        'password' => 'Admin@123456',
        'description' => 'Admin (Utilisateur Téléphone)'
    ],
    [
        'email' => 'superadmin@kenamservices.net',
        'password' => 'Admin@123456',
        'description' => 'SuperAdmin'
    ],
    [
        'email' => 'test@kenam.local',
        'password' => 'test123456',
        'description' => 'Test User (créé pour test)'
    ],
];

foreach ($testAccounts as $account) {
    $credentials = [
        'email' => $account['email'],
        'password' => $account['password'],
    ];
    
    $success = Auth::guard('web')->attempt($credentials);
    
    echo "\n  {$account['description']}\n";
    echo "  Email: {$account['email']}\n";
    echo "  Password: " . str_repeat('*', strlen($account['password'])) . "\n";
    
    if ($success) {
        $user = Auth::user();
        echo "  Status: ✓ LOGIN SUCCESS\n";
        echo "  User ID: {$user->id}\n";
        echo "  Role: {$user->role}\n";
        Auth::logout();
    } else {
        echo "  Status: ✗ LOGIN FAILED\n";
    }
}

echo "\n3. COMPTES DISPONIBLES\n";
echo "--------------------------------\n";

$admins = \App\Models\User::whereIn('role', ['admin', 'superadmin'])->get();
echo "Total comptes admin: " . count($admins) . "\n";
foreach ($admins as $admin) {
    echo "  - {$admin->email} ({$admin->role})\n";
}

echo "\n=========================================\n";
echo "✓ DIAGNOSTIC TERMINE - PRET POUR TEST\n";
echo "=========================================\n";
