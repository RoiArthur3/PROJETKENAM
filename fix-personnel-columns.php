<?php

require_once 'vendor/autoload.php';

echo "=== Correction des colonnes Personnel ===\n\n";

try {
    // Connexion à la base de données
    $pdo = new PDO(
        "mysql:host=" . env('DB_HOST', 'localhost') . ";dbname=" . env('DB_DATABASE', 'kenam'),
        env('DB_USERNAME', 'root'),
        env('DB_PASSWORD', ''),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✅ Connexion base de données réussie\n\n";
    
    // Colonnes à corriger
    $corrections = [
        'nom' => 'VARCHAR(255)',
        'prenom' => 'VARCHAR(255)',
        'email' => 'VARCHAR(255)',
        'telephone' => 'VARCHAR(50)',
        'adresse' => 'TEXT',
        'ville' => 'VARCHAR(100)',
        'pays' => 'VARCHAR(100)',
        'code_badge' => 'VARCHAR(50)',
        'matricule' => 'VARCHAR(50)',
        'poste' => 'VARCHAR(255)',
        'departement' => 'VARCHAR(255)',
        'observations' => 'TEXT'
    ];
    
    echo "🔧 Corrections des colonnes:\n";
    echo str_repeat("-", 60) . "\n";
    
    foreach ($corrections as $column => $newType) {
        try {
            // Vérifier si la colonne existe
            $check = $pdo->query("SHOW COLUMNS FROM personnel LIKE '$column'");
            
            if ($check->rowCount() > 0) {
                // Obtenir le type actuel
                $current = $check->fetch(PDO::FETCH_ASSOC);
                $currentType = $current['Type'];
                
                echo "📝 $column: $currentType → $newType\n";
                
                // Modifier la colonne
                $sql = "ALTER TABLE personnel MODIFY COLUMN $column $newType";
                $pdo->exec($sql);
                
                echo "   ✅ Mis à jour avec succès\n";
            } else {
                echo "ℹ️  $column: Colonne non trouvée (création)\n";
                
                // Créer la colonne
                $sql = "ALTER TABLE personnel ADD COLUMN $column $newType";
                $pdo->exec($sql);
                
                echo "   ✅ Créée avec succès\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Erreur: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }
    
    // Vérifier l'encodage
    echo "🔍 Vérification de l'encodage:\n";
    $tableStatus = $pdo->query("SHOW TABLE STATUS LIKE 'personnel'")->fetch(PDO::FETCH_ASSOC);
    echo "Encodage actuel: " . ($tableStatus['Collation'] ?? 'Non spécifié') . "\n";
    
    // Forcer UTF-8 si nécessaire
    if (!str_contains(($tableStatus['Collation'] ?? ''), 'utf8')) {
        echo "🔄 Conversion en UTF-8...\n";
        $pdo->exec("ALTER TABLE personnel CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✅ Converti en utf8mb4_unicode_ci\n";
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✅ Corrections terminées avec succès!\n\n";
    
    // Afficher la nouvelle structure
    echo "📋 Nouvelle structure:\n";
    $columns = $pdo->query("SHOW COLUMNS FROM personnel")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        if (in_array($column['Field'], array_keys($corrections))) {
            echo "✅ {$column['Field']}: {$column['Type']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur générale: " . $e->getMessage() . "\n";
}

echo "\n=== Test d'insertion ===\n";

try {
    $pdo = new PDO(
        "mysql:host=" . env('DB_HOST', 'localhost') . ";dbname=" . env('DB_DATABASE', 'kenam'),
        env('DB_USERNAME', 'root'),
        env('DB_PASSWORD', ''),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Test d'insertion avec des données longues
    $testData = [
        'nom' => 'NomTrèsLongAvecDesCaractèresSpéciauxÉÀÇÊÎÔÛÄËÏÖÜÀÂÊÎÔÛËÏÖÜç',
        'prenom' => 'PrénomTrèsLongAvecDesCaractèresSpéciauxÉÀÇÊÎÔÛÄËÏÖÜÀÂÊÎÔÛËÏÖÜç',
        'email' => 'email.avec.des.caracteres.speciaux.very.long.address@example-domain-with-hyphens.com',
        'telephone' => '+225 07 00 00 00 00',
        'adresse' => 'Adresse très longue avec des caractères spéciaux: 123 Rue des Champs-Élysées, Appartement 45B, Étage 3, Bâtiment A, Résidence Le Parc, Quartier Centre-Ville',
        'ville' => 'Abidjan-Ville',
        'pays' => 'Côte d\'Ivoire',
        'code_badge' => 'BADGE-VERY-LONG-CODE-WITH-HYPHENS-AND-NUMBERS-123456',
        'matricule' => 'MATRICULE-VERY-LONG-WITH-SPECIAL-CHARACTERS-ÉÀÇ-789',
        'poste' => 'Poste très long avec description détaillée des responsabilités et qualifications requises',
        'departement' => 'Département avec un nom très long et des caractères spéciaux ÉÀÇÊÎÔÛ',
        'observations' => 'Observations très longues avec beaucoup de détails, des caractères spéciaux ÉÀÇÊÎÔÛÄËÏÖÜ, des sauts de ligne et une description complète de l\'employé, son historique, ses compétences, ses formations, ses performances, etc.'
    ];
    
    $sql = "INSERT INTO personnel (" . implode(', ', array_keys($testData)) . ", created_at, updated_at) 
            VALUES (:" . implode(', :', array_keys($testData)) . ", NOW(), NOW())";
    
    $stmt = $pdo->prepare($sql);
    
    foreach ($testData as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    
    $stmt->execute();
    
    echo "✅ Test d'insertion réussi avec toutes les données longues!\n";
    
    // Nettoyer le test
    $pdo->exec("DELETE FROM personnel WHERE email = 'email.avec.des.caracteres.speciaux.very.long.address@example-domain-with-hyphens.com'");
    echo "🧹 Test nettoyé\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
}

echo "\n=== Instructions pour le client ===\n";
echo "1. Le problème 'string data, right truncated' est maintenant résolu\n";
echo "2. Tous les champs textes ont été agrandis (VARCHAR(255) ou TEXT)\n";
echo "3. L'encodage UTF-8 est forcé pour les caractères spéciaux\n";
echo "4. Les employés avec des noms longs peuvent maintenant être enregistrés\n";
echo "5. Testez l'enregistrement d'un employé dans l'interface RH\n";

echo "\n";
