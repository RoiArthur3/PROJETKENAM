<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

echo "=== Test du compte de résultat ===\n";

try {
    // Simuler une requête pour la période
    $request = new Request();
    $request->merge([
        'start_date' => '2025-01-01',
        'end_date' => '2025-12-31'
    ]);

    echo "Vérification des tables nécessaires...\n";

    // Vérifier les tables
    $tables = ['operations', 'factures', 'encaissements', 'vehicle_financial_entries'];
    
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            echo "✅ Table $table: $count enregistrements\n";
        } catch (Exception $e) {
            echo "❌ Table $table: " . $e->getMessage() . "\n";
        }
    }

    echo "\nCalcul des données du compte de résultat...\n";

    // Calcul des produits (revenus)
    $totalProduits = 0;
    $detailsProduits = [];

    // Revenus des factures
    try {
        $factures = DB::table('factures')
            ->whereBetween('date_facture', ['2025-01-01', '2025-12-31'])
            ->sum('montant_ttc');
        
        if ($factures > 0) {
            $detailsProduits['Ventes prestations'] = $factures;
            $totalProduits += $factures;
            echo "✅ Factures: " . number_format($factures, 0, ',', ' ') . " FCFA\n";
        }
    } catch (Exception $e) {
        echo "❌ Factures: " . $e->getMessage() . "\n";
    }

    // Revenus des encaissements
    try {
        $encaissements = DB::table('encaissements')
            ->whereBetween('date_encaissement', ['2025-01-01', '2025-12-31'])
            ->sum('montant');
        
        if ($encaissements > 0) {
            $detailsProduits['Encaissements'] = $encaissements;
            $totalProduits += $encaissements;
            echo "✅ Encaissements: " . number_format($encaissements, 0, ',', ' ') . " FCFA\n";
        }
    } catch (Exception $e) {
        echo "❌ Encaissements: " . $e->getMessage() . "\n";
    }

    // Calcul des charges (dépenses)
    $totalCharges = 0;
    $detailsCharges = [];

    // Dépenses des operations
    try {
        $operations = DB::table('operations')
            ->whereBetween('date_operation', ['2025-01-01', '2025-12-31'])
            ->sum('montant_total');
        
        if ($operations > 0) {
            $detailsCharges['Achats marchandises'] = $operations;
            $totalCharges += $operations;
            echo "✅ Operations: " . number_format($operations, 0, ',', ' ') . " FCFA\n";
        }
    } catch (Exception $e) {
        echo "❌ Operations: " . $e->getMessage() . "\n";
    }

    // Dépenses des vehicle_financial_entries (expenses)
    try {
        $vehicleExpenses = DB::table('vehicle_financial_entries')
            ->where('type', 'expense')
            ->whereBetween('transaction_date', ['2025-01-01', '2025-12-31'])
            ->sum('amount');
        
        if ($vehicleExpenses > 0) {
            $detailsCharges['Charges véhicules'] = $vehicleExpenses;
            $totalCharges += $vehicleExpenses;
            echo "✅ Vehicle expenses: " . number_format($vehicleExpenses, 0, ',', ' ') . " FCFA\n";
        }
    } catch (Exception $e) {
        echo "❌ Vehicle expenses: " . $e->getMessage() . "\n";
    }

    // Calcul du résultat
    $resultatNet = $totalProduits - $totalCharges;
    $margeBrute = $resultatNet;

    echo "\n=== RÉSUMÉ DU COMPTE DE RÉSULTAT ===\n";
    echo "📊 Produits totaux: " . number_format($totalProduits, 0, ',', ' ') . " FCFA\n";
    echo "💰 Charges totales: " . number_format($totalCharges, 0, ',', ' ') . " FCFA\n";
    echo "📈 Résultat net: " . number_format($resultatNet, 0, ',', ' ') . " FCFA\n";
    echo "📊 Marge brute: " . number_format($margeBrute, 0, ',', ' ') . " FCFA\n";

    // Préparer les données comme dans le contrôleur
    $donnees = [
        'total_produits' => $totalProduits,
        'total_charges' => $totalCharges,
        'marge_brute' => $margeBrute,
        'resultat_exploitation' => $resultatNet,
        'resultat_financier' => 0,
        'resultat_net' => $resultatNet,
        'produits' => $detailsProduits,
        'charges' => $detailsCharges,
        'periode' => 'Année 2025',
        'debut' => '2025-01-01',
        'fin' => '2025-12-31',
        'resultat_precedent' => 0,
        'variation' => $resultatNet,
        'variation_pourcentage' => 100,
        'graphique_mensuel' => [],
        'chiffre_affaires' => $totalProduits,
        'marge_brute_pourcentage' => $totalProduits != 0 ? ($margeBrute / $totalProduits) * 100 : 0,
        'taux_rentabilite' => $totalCharges != 0 ? ($totalProduits / $totalCharges) * 100 : 0,
    ];

    echo "\n✅ Structure des données préparée avec succès !\n";
    echo "✅ Le compte de résultat peut être affiché\n";

    // Vérifier si la vue existe
    $viewPath = resource_path('views/comptabilite/rapports/compte-resultat.blade.php');
    if (file_exists($viewPath)) {
        echo "✅ Vue compte-resultat.blade.php trouvée\n";
    } else {
        echo "❌ Vue compte-resultat.blade.php non trouvée\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
