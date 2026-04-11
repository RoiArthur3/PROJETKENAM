<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test de l'API du rapport financier
    $projet = App\Models\Operation::first();

    if ($projet) {
        echo "Test du rapport financier pour le projet: " . $projet->titre . " (ID: " . $projet->id . ")\n";

        // Créer une requête simulée pour l'API
        $request = new Illuminate\Http\Request();

        // Test direct avec le service financier
        $service = new App\Services\ProjetFinancialService();

        // Simuler une requête sans filtres
        $request = new Illuminate\Http\Request();

        // Récupérer les statistiques
        $stats = $service->getStatistiquesFinancieres($projet->id);

        echo "Statistiques financières du projet:\n";
        echo json_encode($stats, JSON_PRETTY_PRINT) . "\n";

        // Vérifier les données
        if (isset($stats['total_general'])) {
            echo "\nPointages trouvés: " . $stats['total_general']['nombre_pointages'] . "\n";
            echo "Total unites: " . $stats['total_general']['total_unites'] . "\n";
            echo "Total fournisseur: " . $stats['total_general']['total_fournisseur'] . " FCFA\n";
            echo "Total client: " . $stats['total_general']['total_client'] . " FCFA\n";
            echo "Total marge: " . $stats['total_general']['total_marge'] . " FCFA\n";
        }

    } else {
        echo "Aucun projet trouvé\n";
    }

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
