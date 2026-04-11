<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test du service financier
    $projet = App\Models\Operation::first();
    $vehicule = App\Models\Vehicule::first();

    if ($projet && $vehicule) {
        echo "Test du service financier\n";
        echo "Projet: " . $projet->titre . " (ID: " . $projet->id . ")\n";
        echo "Véhicule: " . $vehicule->immatriculation . " (ID: " . $vehicule->id . ")\n";

        // Créer un pointage de test
        $pointage = new App\Models\Pointage();
        $pointage->user_id = 1;
        $pointage->vehicle_id = $vehicule->id;
        $pointage->operation_id = $projet->id;
        $pointage->date_pointage = now()->format('Y-m-d');
        $pointage->unit_type = 'heure';
        $pointage->quantity = 8;
        $pointage->supplier_unit_cost = $vehicule->prix_achat ?? 20000;
        $pointage->client_unit_price = $vehicule->prix_location ?? 25000;
        $pointage->total_supplier_cost = $pointage->quantity * $pointage->supplier_unit_cost;
        $pointage->total_client_amount = $pointage->quantity * $pointage->client_unit_price;
        $pointage->statut = 'validé';
        $pointage->submodule = 'engin';
        $pointage->created_by = 1;
        $pointage->save();

        echo "Pointage créé: " . $pointage->quantity . " heures\n";
        echo "Coût fournisseur: " . $pointage->total_supplier_cost . " FCFA\n";
        echo "Montant client: " . $pointage->total_client_amount . " FCFA\n";

        // Tester la mise à jour du montant à facturer
        $service = new App\Services\ProjetFinancialService();
        $montantFacturer = $service->updateMontantFacturer($projet->id);

        echo "Montant à facturer mis à jour: " . $montantFacturer . " FCFA\n";

        // Rafraîchir le projet
        $projet->refresh();
        echo "Montant à facturer dans le projet: " . ($projet->montant_facturer ?? 0) . " FCFA\n";

        // Tester les statistiques
        $stats = $service->getStatistiquesFinancieres($projet->id);
        echo "\nStatistiques financières:\n";
        echo "Total général:\n";
        echo "  - Unités: " . $stats['total_general']['total_unites'] . "\n";
        echo "  - Coût fournisseur: " . $stats['total_general']['total_fournisseur'] . " FCFA\n";
        echo "  - Montant client: " . $stats['total_general']['total_client'] . " FCFA\n";
        echo "  - Marge: " . $stats['total_general']['total_marge'] . " FCFA\n";

    } else {
        echo "Pas de projet ou de véhicule trouvé\n";
    }

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
