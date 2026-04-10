<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Vérification des tables ===\n\n";

try {
    // Vérifier si la table personnel existe
    $hasPersonnel = Schema::hasTable('personnel');
    echo "Table personnel: " . ($hasPersonnel ? "✅ Existe" : "❌ N'existe pas") . "\n";
    
    if ($hasPersonnel) {
        $columns = DB::select("SHOW COLUMNS FROM personnel");
        echo "\nColonnes dans personnel:\n";
        foreach ($columns as $column) {
            echo "- {$column->Field} ({$column->Type})\n";
        }
        
        // Vérifier les colonnes importantes
        $hasNom = Schema::hasColumn('personnel', 'nom');
        $hasName = Schema::hasColumn('personnel', 'name');
        $hasMatricule = Schema::hasColumn('personnel', 'matricule');
        
        echo "\nColonnes de tri:\n";
        echo "- nom: " . ($hasNom ? "✅" : "❌") . "\n";
        echo "- name: " . ($hasName ? "✅" : "❌") . "\n";
        echo "- matricule: " . ($hasMatricule ? "✅" : "❌") . "\n";
    }
    
    echo "\n=== Test de la page RH ===\n";
    
    // Simuler la requête du contrôleur
    if ($hasPersonnel) {
        try {
            $query = \App\Models\Personnel::query();
            
            if (Schema::hasColumn('personnel', 'nom')) {
                $query->orderBy('nom');
                echo "Tri par 'nom' - ✅\n";
            } elseif (Schema::hasColumn('personnel', 'name')) {
                $query->orderBy('name');
                echo "Tri par 'name' - ✅\n";
            } elseif (Schema::hasColumn('personnel', 'matricule')) {
                $query->orderBy('matricule');
                echo "Tri par 'matricule' - ✅\n";
            } else {
                $query->orderBy('id');
                echo "Tri par 'id' - ✅\n";
            }
            
            $count = $query->count();
            echo "Nombre d'enregistrements: $count\n";
            
        } catch (Exception $e) {
            echo "❌ Erreur de requête: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ Table personnel n'existe pas - utilisation fallback users\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n";
