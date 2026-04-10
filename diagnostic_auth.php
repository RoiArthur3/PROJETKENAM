<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=========================================\n";
echo "TEST D'AUTHENTIFICATION ADMIN\n";
echo "=========================================\n\n";

// 1. Vérifier les identifiants de login
echo "1. CONFIGURATION AUTH\n";
echo "--------------------------------\n";
echo "Username field: " . config('auth.providers.users.username') . "\n";
echo "Model: " . config('auth.providers.users.model') . "\n";
echo "Guard: " . config('auth.defaults.guard') . "\n";

// 2. Vérifier les utilisateurs et leurs téléphones/emails
echo "\n2. UTILISATEURS ADMIN DISPONIBLES\n";
echo "--------------------------------\n";
$admins = DB::table('users')
    ->whereIn('role', ['admin', 'superadmin'])
    ->get(['id', 'email', 'telephone', 'name', 'role']);

echo "Total comptes admin: " . count($admins) . "\n";
foreach ($admins as $admin) {
    echo "\n  Name: {$admin->name}\n";
    echo "  Email: {$admin->email}\n";
    echo "  Telephone: {$admin->telephone}\n";
    echo "  Role: {$admin->role}\n";
}

// 3. Test Login avec Email
echo "\n3. TEST LOGIN AVEC EMAIL\n";
echo "--------------------------------\n";
$user = DB::table('users')->where('email', 'admin@kenam.com')->first();
if ($user) {
    echo "User trouvé: {$user->email}\n";
    $credentials = [
        'email' => 'admin@kenam.com',
        'password' => 'password', // test password
    ];
    if (Auth::guard('web')->attempt($credentials)) {
        echo "✓ LOGIN AVEC EMAIL: SUCCESS\n";
    } else {
        echo "✗ LOGIN AVEC EMAIL: FAILED (password probablement incorrect)\n";
    }
} else {
    echo "Pas d'utilisateur avec email admin@kenam.com\n";
}

// 4. Test Login avec Telephone (config actuelle)
echo "\n4. TEST LOGIN AVEC TELEPHONE (config actuelle)\n";
echo "--------------------------------\n";
$user = DB::table('users')->where('role', 'admin')->first();
if ($user && $user->telephone) {
    echo "User trouvé avec telephone: {$user->telephone}\n";
    $credentials = [
        'telephone' => $user->telephone,
        'password' => 'password',
    ];
    if (Auth::guard('web')->attempt($credentials)) {
        echo "✓ LOGIN AVEC TELEPHONE: SUCCESS\n";
    } else {
        echo "✗ LOGIN AVEC TELEPHONE: FAILED\n";
    }
} else {
    echo "Pas de telephone configuré ou pas d'admin trouvé\n";
}

// 5. Vérifier les mots de passe
echo "\n5. VERIFICATION MOTS DE PASSE\n";
echo "--------------------------------\n";
$user = \App\Models\User::where('role', 'admin')->first();
if ($user) {
    echo "Admin: {$user->email}\n";
    echo "Password Hash: " . substr($user->password, 0, 20) . "...\n";
    echo "Password non vide: " . (!empty($user->password) ? 'OUI' : 'NON') . "\n";
}

echo "\n=========================================\n";
echo "DIAGNOSTIC TERMINE\n";
echo "=========================================\n";
