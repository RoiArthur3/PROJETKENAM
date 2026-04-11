<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Test des nouveaux modules Magasin et Entrepot ===\n";

$superadmin = App\Models\User::where('role', 'superadmin')->first();
if (!$superadmin) {
    echo "ERREUR: Aucun superadmin trouvé\n";
    exit(1);
}

echo "Superadmin: " . $superadmin->name . " (ID: " . $superadmin->id . ")\n\n";

$newModules = [
    'magasin' => 'Magasin',
    'entrepot' => 'Entrepot',
    'warehouse' => 'Warehouse (ancien)'
];

echo "Accès aux modules:\n";
foreach ($newModules as $moduleKey => $moduleName) {
    $access = $superadmin->canAccessModule($moduleKey) ? 'YES' : 'NO';
    echo "- $moduleKey ($moduleName): $access\n";
}

echo "\n=== Test des raccourcis profil ===\n";
$controller = new App\Http\Controllers\ProfileController();
$request = new Illuminate\Http\Request();
$request->setUserResolver(function() use ($superadmin) {
    return $superadmin;
});

try {
    $response = $controller->dashboard($request);
    echo "Profile dashboard fonctionne\n";
    
    // Extraire les raccourcis
    $data = $response->getData();
    if (isset($data['shortcuts']) && is_array($data['shortcuts'])) {
        echo "Raccourcis disponibles: " . count($data['shortcuts']) . "\n";
        
        $newShortcuts = array_filter($data['shortcuts'], function($shortcut) {
            return in_array($shortcut['module'], ['magasin', 'entrepot']);
        });
        
        echo "Raccourcis Magasin/Entrepot: " . count($newShortcuts) . "\n";
        foreach ($newShortcuts as $shortcut) {
            echo "- " . $shortcut['label'] . " (" . $shortcut['module'] . ") -> " . $shortcut['url'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
}
