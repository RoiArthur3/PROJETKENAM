<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=========================================\n";
echo "DIAGNOSTIC COMPTES ADMIN\n";
echo "=========================================\n\n";

// 1. Vérifier les utilisateurs
echo "1. UTILISATEURS DANS LA BASE\n";
echo "--------------------------------\n";
$users = DB::table('users')->get();
echo "Total utilisateurs: " . count($users) . "\n";
foreach ($users as $user) {
    echo "  ID: {$user->id} | Email: {$user->email} | Name: {$user->name} | Role: {$user->role}\n";
}

// 2. Vérifier les rôles
echo "\n2. ROLES DISPONIBLES\n";
echo "--------------------------------\n";
$roles = DB::table('roles')->get();
echo "Total rôles: " . count($roles) . "\n";
foreach ($roles as $role) {
    echo "  ID: {$role->id} | Name: {$role->name}\n";
}

// 3. Vérifier les permissions
echo "\n3. PERMISSIONS\n";
echo "--------------------------------\n";
$perms = DB::table('permissions')->get();
echo "Total permissions: " . count($perms) . "\n";
if (count($perms) > 0) {
    foreach ($perms->take(5) as $perm) {
        echo "  ID: {$perm->id} | Name: {$perm->name}\n";
    }
    if (count($perms) > 5) {
        echo "  ... et " . (count($perms) - 5) . " autres\n";
    }
}

// 4. Vérifier la table model_has_roles
echo "\n4. ROLES ASSIGNÉS AUX UTILISATEURS\n";
echo "--------------------------------\n";
$userRoles = DB::table('model_has_roles')->get();
echo "Total assignations: " . count($userRoles) . "\n";
foreach ($userRoles as $ur) {
    echo "  Model: {$ur->model_type} | ID: {$ur->model_id} | Role ID: {$ur->role_id}\n";
}

// 5. Vérifier la configuration auth
echo "\n5. CONFIGURATION AUTH\n";
echo "--------------------------------\n";
echo "Auth Guard: " . config('auth.defaults.guard') . "\n";
echo "Auth Provider: " . config('auth.defaults.provider') . "\n";
echo "User Model: " . config('auth.providers.users.model') . "\n";

// 6. Vérifier le modèle User
echo "\n6. USER MODEL RELATIONS\n";
echo "--------------------------------\n";
try {
    $user = \App\Models\User::first();
    if ($user) {
        echo "User trouvé: {$user->email}\n";
        $roles = $user->roles;
        echo "Rôles du user: " . count($roles) . "\n";
        foreach ($roles as $role) {
            echo "  - {$role->name}\n";
        }
    } else {
        echo "Aucun utilisateur trouvé\n";
    }
} catch (\Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}

// 7. Vérifier la middleware d'authentification
echo "\n7. MIDDLEWARES ENREGISTRÉS\n";
echo "--------------------------------\n";
$routeMiddlewares = config('app.middleware');
if (is_array($routeMiddlewares)) {
    echo "Global middlewares: " . implode(', ', array_slice($routeMiddlewares, 0, 3)) . "...\n";
}

echo "\n=========================================\n";
echo "FIN DIAGNOSTIC\n";
echo "=========================================\n";
