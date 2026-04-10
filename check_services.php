<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VÉRIFICATION DES SERVICES ===" . PHP_EOL;

$services = App\Models\ServiceOperationnel::all(['id', 'nom', 'actif']);

echo "📋 Services disponibles ({$services->count()}):" . PHP_EOL;
foreach ($services as $service) {
    $status = $service->actif ? '✅' : '❌';
    echo "  - ID: {$service->id}, Nom: {$service->nom}, Actif: {$status}" . PHP_EOL;
}

echo PHP_EOL . "🔍 Types d'opérations:" . PHP_EOL;
$types = App\Models\TypeOperation::all(['id', 'libelle']);

foreach ($types as $type) {
    echo "  - ID: {$type->id}, Libellé: {$type->libelle}" . PHP_EOL;
}

echo PHP_EOL . "=== FIN VÉRIFICATION ===" . PHP_EOL;
