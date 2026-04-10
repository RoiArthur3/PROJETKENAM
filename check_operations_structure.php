<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Vérification de la structure de la table operations ===\n";

try {
    // Obtenir la structure de la table
    $columns = DB::select("SHOW COLUMNS FROM operations");
    
    echo "Colonnes de la table operations:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    echo "\nVérification des colonnes pertinentes...\n";
    
    $relevantColumns = ['montant', 'montant_total', 'montant_ht', 'montant_ttc', 'prix', 'total'];
    $foundColumns = [];
    
    foreach ($relevantColumns as $col) {
        $exists = collect($columns)->firstWhere('Field', $col);
        if ($exists) {
            $foundColumns[] = $col;
            echo "✅ $col trouvé\n";
        } else {
            echo "❌ $col non trouvé\n";
        }
    }
    
    if (!empty($foundColumns)) {
        echo "\n✅ Colonnes utilisables: " . implode(', ', $foundColumns) . "\n";
    } else {
        echo "\n❌ Aucune colonne de montant trouvée\n";
        
        // Vérifier quelques enregistrements pour voir la structure
        echo "\nVérification des enregistrements existants...\n";
        $sample = DB::table('operations')->limit(1)->get();
        
        if ($sample->count() > 0) {
            echo "Structure d'un enregistrement:\n";
            foreach ($sample->first() as $key => $value) {
                echo "- $key: $value\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
