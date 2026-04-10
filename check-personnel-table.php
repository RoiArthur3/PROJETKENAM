<?php

echo "=== VÉRIFICATION TABLE PERSONNEL ===\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=kenam2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Connexion à la base de données établie\n\n";

    // Vérifier si la table existe
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'personnel'");
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        echo "❌ La table 'personnel' n'existe pas\n";
        echo "   Exécutez: php artisan migrate\n";
        exit;
    }

    echo "✅ Table 'personnel' trouvée\n\n";

    // Obtenir la structure de la table
    $stmt = $pdo->prepare("SHOW COLUMNS FROM personnel");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_OBJ);

    echo "Structure des colonnes:\n";
    echo str_repeat("=", 80) . "\n";
    printf("%-20s %-15s %-10s %-10s %-10s %s\n", "Colonne", "Type", "Null", "Key", "Default", "Extra");
    echo str_repeat("-", 80) . "\n";

    foreach ($columns as $column) {
        printf("%-20s %-15s %-10s %-10s %-10s %s\n",
            $column->Field,
            $column->Type,
            $column->Null,
            $column->Key,
            $column->Default ?? 'NULL',
            $column->Extra
        );
    }

    echo "\n" . str_repeat("=", 80) . "\n";

    // Vérifier les colonnes potentiellement problématiques
    echo "\n🔍 Colonnes potentiellement problématiques:\n";

    foreach ($columns as $column) {
        $type = strtolower($column->Type);

        // Vérifier les colonnes importantes qui doivent être nullable
        if (in_array($column->Field, ['lien_parente', 'groupe_sanguin', 'banque']) && $column->Null === 'NO') {
            echo "⚠️  {$column->Field}: {$column->Type} (devrait être nullable)\n";
        }

        // Vérifier les VARCHAR trop courts
        if (strpos($type, 'varchar') !== false) {
            preg_match('/varchar\((\d+)\)/', $type, $matches);
            $size = isset($matches[1]) ? (int)$matches[1] : 0;

            if ($size < 100 && in_array($column->Field, ['nom', 'prenoms', 'email_personnel', 'adresse_residence'])) {
                echo "⚠️  {$column->Field}: {$column->Type} (trop court pour ce type de donnée)\n";
            }
        }
    }

    // Vérifier les données existantes
    echo "\n📊 Analyse des données existantes:\n";

    $stmt = $pdo->prepare("SELECT nom, prenoms, email_personnel, LENGTH(nom) as nom_len, LENGTH(prenoms) as prenoms_len FROM personnel LIMIT 5");
    $stmt->execute();
    $sampleData = $stmt->fetchAll(PDO::FETCH_OBJ);

    if (!empty($sampleData)) {
        echo "Échantillon de données:\n";
        foreach ($sampleData as $row) {
            echo "- {$row->nom} ({$row->nom_len} chars) / {$row->prenoms} ({$row->prenoms_len} chars)\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE LA VÉRIFICATION ===\n";
