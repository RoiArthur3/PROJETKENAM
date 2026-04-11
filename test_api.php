<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test de l'API pour récupérer les engins d'un projet
    $projet = App\Models\Operation::first();

    if ($projet) {
        echo "Test API pour le projet: " . $projet->titre . " (ID: " . $projet->id . ")\n";

        // Test direct avec le modèle
        $engins = $projet->vehicules()->get();

        echo "Engins associés au projet:\n";
        foreach ($engins as $engin) {
            echo "- " . $engin->immatriculation . " (" . $engin->marque . " " . $engin->modele . ")\n";
        }

        // Préparer la réponse comme l'API
        $response = [
            'success' => true,
            'engins' => $engins->map(function($engin) {
                return [
                    'id' => $engin->id,
                    'immatriculation' => $engin->immatriculation,
                    'marque' => $engin->marque,
                    'modele' => $engin->modele,
                    'type_materiel' => $engin->type_materiel,
                    'prix_location' => $engin->prix_location,
                    'prix_achat' => $engin->prix_achat,
                    'statut' => $engin->statut,
                ];
            })->toArray()
        ];

        echo "Réponse de l'API:\n";
        echo json_encode($response, JSON_PRETTY_PRINT) . "\n";

    } else {
        echo "Aucun projet trouvé\n";
    }

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
