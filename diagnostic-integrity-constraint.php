<?php

/**
 * Script de diagnostic pour l'erreur "integrity constraint violation"
 *
 * Ce script identifie les colonnes 'description' qui ne sont pas nullable
 * mais qui reçoivent des valeurs NULL lors des mises à jour.
 */

echo "=== DIAGNOSTIC DES CONTRAINTES D'INTÉGRITÉ ===\n\n";

try {
    // Tentative de connexion à la base de données
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=kenam2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Connexion à la base de données établie\n\n";

    // Liste des tables suspectes
    $tables = ['operation_costs', 'operations', 'project_expenses'];

    foreach ($tables as $table) {
        echo "🔍 Vérification de la table : $table\n";

        try {
            // Vérifier si la table existe
            $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);

            if ($stmt->rowCount() > 0) {
                echo "  ✅ Table existe\n";

                // Vérifier la structure de la colonne description
                $stmt = $pdo->prepare("SHOW COLUMNS FROM $table LIKE 'description'");
                $stmt->execute();
                $column = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($column) {
                    echo "  📋 Colonne 'description' trouvée :\n";
                    echo "     - Type: {$column['Type']}\n";
                    echo "     - Null: {$column['Null']}\n";
                    echo "     - Default: {$column['Default']}\n";

                    if ($column['Null'] === 'NO') {
                        echo "  ⚠️  PROBLÈME : La colonne n'accepte pas les valeurs NULL!\n";

                        // Vérifier s'il y a des enregistrements avec description NULL
                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM $table WHERE description IS NULL");
                        $stmt->execute();
                        $nullCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                        if ($nullCount > 0) {
                            echo "  🚨 $nullCount enregistrement(s) avec description = NULL\n";
                        }
                    } else {
                        echo "  ✅ La colonne accepte les valeurs NULL\n";
                    }
                } else {
                    echo "  ❌ Colonne 'description' non trouvée\n";
                }
            } else {
                echo "  ❌ Table n'existe pas\n";
            }
        } catch (Exception $e) {
            echo "  ❌ Erreur: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }

    echo "=== RECOMMANDATIONS ===\n";
    echo "1. Démarrer le service MySQL/Laragon\n";
    echo "2. Exécuter la migration: php artisan migrate --force\n";
    echo "3. Vérifier les formulaires qui envoient des description vides\n";
    echo "4. Ajouter des valeurs par défaut dans les contrôleurs si nécessaire\n\n";

    echo "=== COMMANDE DE CORRECTION ===\n";
    echo "php artisan migrate --force\n\n";

} catch (PDOException $e) {
    echo "❌ ERREUR DE CONNEXION À LA BASE DE DONNÉES\n";
    echo "Message: " . $e->getMessage() . "\n\n";

    echo "=== SOLUTIONS POSSIBLES ===\n";
    echo "1. Vérifier que Laragon est bien démarré\n";
    echo "2. Vérifier que le service MySQL fonctionne\n";
    echo "3. Vérifier la configuration dans .env (DB_HOST, DB_PORT, DB_DATABASE)\n";
    echo "4. Redémarrer Laragon complètement\n\n";

    echo "=== COMMANDES UTILES ===\n";
    echo "# Démarrer les services Laragon\n";
    echo "# Vérifier le statut MySQL\n";
    echo "net start | findstr mysql\n";
    echo "tasklist | findstr MySQL\n\n";
}

echo "=== FIN DU DIAGNOSTIC ===\n";
