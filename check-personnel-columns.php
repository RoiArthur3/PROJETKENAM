<?php

echo "=== Vérification des colonnes de la table personnel ===\n\n";

try {
    $pdo = new PDO(
        "mysql:host=" . env('DB_HOST', 'localhost') . ";dbname=" . env('DB_DATABASE', 'kenam'),
        env('DB_USERNAME', 'root'),
        env('DB_PASSWORD', ''),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✅ Connexion réussie\n\n";
    
    // Vérifier si la table existe
    $tables = $pdo->query("SHOW TABLES LIKE 'personnel'")->fetchAll();
    
    if (empty($tables)) {
        echo "❌ La table 'personnel' n'existe pas\n";
        echo "   Solution: php artisan migrate\n";
        exit;
    }
    
    echo "✅ Table 'personnel' trouvée\n\n";
    
    // Lister toutes les colonnes
    $columns = $pdo->query("SHOW COLUMNS FROM personnel")->fetchAll();
    
    echo "📋 Colonnes disponibles:\n";
    echo str_repeat("=", 50) . "\n";
    
    $hasNom = false;
    $hasName = false;
    
    foreach ($columns as $column) {
        $fieldName = $column['Field'];
        echo "- {$fieldName} ({$column['Type']})\n";
        
        if ($fieldName === 'nom') $hasNom = true;
        if ($fieldName === 'name') $hasName = true;
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    
    // Diagnostic
    if ($hasNom) {
        echo "✅ Colonne 'nom' existe - Le contrôleur devrait fonctionner\n";
    } elseif ($hasName) {
        echo "⚠️  Colonne 'name' existe mais pas 'nom' - Problème de nommage\n";
        echo "   Solution: Changer 'orderBy('nom')' en 'orderBy('name')'\n";
    } else {
        echo "❌ Ni 'nom' ni 'name' n'existent - Colonne de tri manquante\n";
        echo "   Solutions possibles:\n";
        echo "   1. Ajouter une colonne 'nom' ou 'name'\n";
        echo "   2. Utiliser une autre colonne pour le tri (matricule, email)\n";
        echo "   3. Trier par ID\n";
    }
    
    // Vérifier les données
    echo "\n📊 Aperçu des données:\n";
    $sample = $pdo->query("SELECT * FROM personnel LIMIT 3")->fetchAll();
    
    if (!empty($sample)) {
        foreach ($sample as $row) {
            echo "ID: {$row['id']} | ";
            foreach ($row as $key => $value) {
                if ($key !== 'id') {
                    echo "$key: " . (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) . " | ";
                }
            }
            echo "\n";
        }
    } else {
        echo "ℹ️  Aucune donnée dans la table\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== Solutions recommandées ===\n";
echo "1. Si 'nom' existe: Vérifier pourquoi l'erreur se produit\n";
echo "2. Si 'name' existe: Corriger le contrôleur\n";
echo "3. Si aucun des deux: Ajouter une colonne ou utiliser un autre tri\n";

?>
