<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Finding tables containing 'pointage':\n";
foreach (Schema::getTables() as $table) {
    if (str_contains($table['name'], 'pointage')) {
        echo "- " . $table['name'] . "\n";
    }
}
