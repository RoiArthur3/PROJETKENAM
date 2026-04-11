<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $tables = DB::getSchemaBuilder()->getTableListing();
    
    echo "Tables contenant 'pointage':\n";
    foreach ($tables as $table) {
        if (strpos($table, 'pointage') !== false) {
            echo "- " . $table . "\n";
        }
    }
    
    echo "\nTables contenant 'vehicle':\n";
    foreach ($tables as $table) {
        if (strpos($table, 'vehicle') !== false) {
            echo "- " . $table . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
