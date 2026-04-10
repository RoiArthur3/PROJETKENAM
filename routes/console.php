<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Charger les commandes personnalisées
if (file_exists(app_path('Console/Commands'))) {
    $commandFiles = glob(app_path('Console/Commands/*.php'));

    foreach ($commandFiles as $file) {
        $className = 'App\\Console\\Commands\\' . basename($file, '.php');

        if (class_exists($className)) {
            try {
                $this->load($className);
            } catch (\Exception $e) {
                // Ignorer les erreurs de chargement
            }
        }
    }
}
