<?php

echo "=== Migration Personnel Only ===\n\n";

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Exécuter seulement les migrations personnel
    $migrations = [
        '2026_02_26_000001_create_personnel_table',
        '2026_02_26_000002_create_personnel_conges_table',
        '2026_02_26_000003_create_personnel_paies_table',
        '2026_02_26_000004_create_personnel_documents_table',
        '2026_02_26_000005_clean_personnel_tables',
        '2026_03_03_000001_add_fin_periode_essai_to_personnel_table',
        '2026_03_04_190000_create_personnel_contrats_table',
        '2026_03_05_000003_standardize_personnel_statut_enum',
        '2026_03_06_150703_update_personnel_table_add_missing_fields',
        '2026_03_13_090000_add_photo_base64_to_personnel'
    ];

    foreach ($migrations as $migration) {
        echo "Exécution de: $migration\n";
        
        $class = str_replace('_', '', ucwords($migration, '_'));
        $class = 'Create' . substr($class, 0, -7) . 'Table';
        
        $migrationPath = database_path("migrations/{$migration}.php");
        
        if (file_exists($migrationPath)) {
            include_once $migrationPath;
            
            $instance = new $class();
            
            try {
                $instance->up();
                echo "✅ $migration - Succès\n";
            } catch (Exception $e) {
                echo "❌ $migration - Erreur: " . $e->getMessage() . "\n";
            }
        } else {
            echo "❌ $migration - Fichier non trouvé\n";
        }
        echo "\n";
    }
    
    echo "=== Vérification de la table personnel ===\n";
    
    $columns = DB::select("SHOW COLUMNS FROM personnel");
    
    echo "Colonnes trouvées:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n✅ Migration terminée!\n";
    
} catch (Exception $e) {
    echo "❌ Erreur générale: " . $e->getMessage() . "\n";
}

echo "\n";
