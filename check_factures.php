<?php
// Simple PHP script to check factures table status

// Get env variables
$env_file = __DIR__ . '/.env';
if (!file_exists($env_file)) {
    die('Fichier .env non trouvé');
}

// Load environment
require_once __DIR__ . '/vendor/autoload.php';

try {
    // Initialize Laravel app
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $db = app('db');
    
    echo "=== DIAGNOSTIC TABLE FACTURES ===\n";
    echo "Base de données: " . config('database.connections.mysql.database') . "\n\n";
    
    // Check if table exists
    $factures_exists = $db->getSchemaBuilder()->hasTable('factures');
    echo "✓ Table 'factures' existe: " . ($factures_exists ? "OUI" : "NON") . "\n\n";
    
    if ($factures_exists) {
        // Get columns
        $columns = $db->getSchemaBuilder()->getColumnListing('factures');
        echo "✓ Colonnes dans la table 'factures' (" . count($columns) . " total):\n";
        foreach ($columns as $col) {
            echo "  - $col\n";
        }
        echo "\n";
        
        // Count records
        $count = $db->table('factures')->count();
        echo "✓ Nombre de factures: " . $count . "\n\n";
        
        // Show sample data if exists
        if ($count > 0) {
            echo "✓ Exemple de données (5 premières lignes):\n";
            $factures = $db->table('factures')->limit(5)->get();
            $i = 1;
            foreach ($factures as $facture) {
                echo "  [$i] ID: " . ($facture->id ?? 'N/A') . " | ";
                echo "Numero: " . ($facture->numero ?? 'N/A') . " | ";
                echo "Type: " . ($facture->type ?? 'N/A') . " | ";
                echo "Montant: " . ($facture->montant_ttc ?? 'N/A') . " | ";
                echo "Statut: " . ($facture->statut ?? 'N/A') . "\n";
                $i++;
            }
            echo "\n";
        } else {
            echo "⚠️  AUCUNE FACTURE TROUVÉE DANS LA TABLE!\n\n";
            echo "   CES SONT LES RAISONS POSSIBLES:\n";
            echo "   1. Aucune facture n'a été créée dans le système\n";
            echo "   2. Les factures ne sont pas sauvegardées correctement lors de la création\n";
            echo "   3. Il existe un bug dans le formulaire de création de factures\n\n";
            echo "   ACTIONS À PRENDRE:\n";
            echo "   - Vérifiez le contrôleur 'facturesCreate' et 'facturesStore' si elle existe\n";
            echo "   - Testez la création d'une nouvelle facture et vérifiez les logs d'erreurs\n";
            echo "   - Vérifiez les migrations de base de données\n";
        }
    } else {
        echo "❌ La table 'factures' n'existe pas!\n";
        echo "   La table doit d'abord être créée via une migration.\n";
        echo "   Exécutez: php artisan migrate\n";
    }
    
    // Check clients table
    echo "\n=== INFORMATIONS CONNEXES ===\n";
    $clients_exists = $db->getSchemaBuilder()->hasTable('clients');
    echo "✓ Table 'clients' existe: " . ($clients_exists ? "OUI" : "NON") . "\n";
    if ($clients_exists) {
        $clients_count = $db->table('clients')->count();
        echo "  Nombre de clients: $clients_count\n";
    }
    
    echo "\n";
    
} catch (\Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n\n";
}
