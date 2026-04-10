<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=========================================\n";
echo "DIAGNOSTIC COMPTES ADMIN - DETAIL 2\n";
echo "=========================================\n\n";

// Vérifier quelles tables existent
$tables = [
    'users' => 'Table des utilisateurs',
    'roles' => 'Table des roles (Spatie)',
    'permissions' => 'Table des permissions',
    'model_has_roles' => 'Liaison user-roles',
    'role_has_permissions' => 'Liaison roles-permissions',
];

echo "1. TABLES DANS LA BASE DE DONNEES\n";
echo "--------------------------------\n";
foreach ($tables as $table => $desc) {
    $exists = Schema::hasTable($table);
    $status = $exists ? 'OK' : 'MANQUE';
    echo "[$status] $table - $desc\n";
}

// Vérifier la structure de la table users
echo "\n2. STRUCTURE TABLE 'users'\n";
echo "--------------------------------\n";
$columns = Schema::getColumnListing('users');
echo "Colonnes: " . implode(', ', $columns) . "\n";

// Vérifier les valeurs de 'role' dans les users
echo "\n3. VALEURS DANS LA COLONNE 'role'\n";
echo "--------------------------------\n";
$roles = DB::table('users')->distinct('role')->pluck('role');
echo "Roles uniques dans les users: " . implode(', ', $roles->toArray()) . "\n";

// Analyser les permissions config
echo "\n4. CONFIGURATION AUTH/PERMISSIONS\n";
echo "--------------------------------\n";
echo "Auth Guard: " . config('auth.defaults.guard') . "\n";
echo "Auth Provider: " . config('auth.defaults.provider') . "\n";

// Vérifier les middlewares
echo "\n5. MIDDLEWARES D'AUTHENTIFICATION\n";
echo "--------------------------------\n";
try {
    $user = \App\Models\User::where('role', 'admin')->first();
    if ($user) {
        echo "TEST: User admin trouvé: {$user->email}\n";
        echo "  Rôle DB: {$user->role}\n";
        echo "  hasRole('admin'): " . ($user->hasRole('admin') ? 'OUI' : 'NON') . "\n";
    } else {
        echo "ERREUR: Aucun utilisateur admin trouvé\n";
    }
} catch (\Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}

echo "\n6. CLASSES UTILISANT LES ROLES\n";
echo "--------------------------------\n";
echo "Checking Middleware classes...\n";
$files = glob(__DIR__ . '/app/Http/Middleware/*.php');
$roleMiddlewares = [];
foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'hasRole') !== false || strpos($content, 'authorize') !== false) {
        $filename = basename($file);
        $roleMiddlewares[] = $filename;
    }
}
if (empty($roleMiddlewares)) {
    echo "Aucun middleware trouvé checking hasRole/authorize\n";
} else {
    echo "Middlewares trouvés: " . implode(', ', $roleMiddlewares) . "\n";
}

echo "\n7. ANALYSE DU PROBLEME\n";
echo "--------------------------------\n";
if (!Schema::hasTable('roles')) {
    echo "ERREUR CRITIQUE:\n";
    echo "- La table 'roles' n'existe PAS\n";
    echo "- Mais le code essaie de l'utiliser (Spatie/Permission)\n";
    echo "- Le système utilise aussi une colonne 'role' simple\n";
    echo "- CES DEUX SYSTEMES SONT EN CONFLIT\n\n";
    echo "SOLUTION:\n";
    echo "Créer les migrations manquantes OU\n";
    echo "Utiliser UNIQUEMENT le système simple (colonne 'role')\n";
}

echo "\n=========================================\n";
