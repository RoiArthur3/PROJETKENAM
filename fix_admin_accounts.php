<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=========================================\n";
echo "SCRIPT DE FIX - AUTHENTIFICATION ADMIN\n";
echo "=========================================\n\n";

// FIX 1: Réinitialiser les mots de passe des comptes admin
echo "1. REINITIALISER LES MOTS DE PASSE ADMIN\n";
echo "--------------------------------\n";

$password = 'Admin@123456'; // Mot de passe par défaut
$hashedPassword = Hash::make($password);

// Réinitialiser pour tous les admins
$admins = DB::table('users')
    ->whereIn('role', ['admin', 'superadmin'])
    ->update(['password' => $hashedPassword]);

echo "✓ {$admins} comptes admin mis à jour\n";
echo "  Nouveau mot de passe: $password\n";

// FIX 2: Afficher les mots de passe réinitialisés
echo "\n2. COMPTES ADMIN A UTILISER\n";
echo "--------------------------------\n";

$admins = DB::table('users')
    ->whereIn('role', ['admin', 'superadmin'])
    ->get(['id', 'email', 'telephone', 'name', 'role']);

foreach ($admins as $admin) {
    echo "\n  Name: {$admin->name}\n";
    echo "  Email: {$admin->email}\n";
    echo "  Telephone: {$admin->telephone}\n";
    echo "  Role: {$admin->role}\n";
}

// FIX 3: Créer un utilisateur test simple
echo "\n3. CREER UTILISATEUR TEST\n";
echo "--------------------------------\n";

$testUser = DB::table('users')
    ->where('email', 'test@kenam.local')
    ->first();

if (!$testUser) {
    DB::table('users')->insert([
        'name' => 'Test Admin',
        'email' => 'test@kenam.local',
        'telephone' => '9999999999',
        'password' => Hash::make('test123456'),
        'role' => 'superadmin',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✓ Utilisateur test créé: test@kenam.local\n";
    echo "  Mot de passe: test123456\n";
} else {
    echo "Utilisateur test existe déjà\n";
}

// FIX 4: Vérifier configuration auth
echo "\n4. CONFIGURATION AUTH\n";
echo "--------------------------------\n";
echo "Username field: " . config('auth.providers.users.username') . "\n";
if (config('auth.providers.users.username') === 'telephone') {
    echo "⚠️  ATTENTION: 'username' est configuré comme 'telephone'\n";
    echo "   Changez dans config/auth.php:\n";
    echo "   'username' => 'email',  // Changez de 'telephone' à 'email'\n";
}

echo "\n=========================================\n";
echo "SCRIPT TERMINE\n";
echo "=========================================\n";
