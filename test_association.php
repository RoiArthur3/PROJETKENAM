<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $projet = App\Models\Operation::first();
    $vehicule = App\Models\Vehicule::first();
    
    if ($projet && $vehicule) {
        // Vérifier si l'association existe déjà
        $exists = $projet->vehicules()->where('vehicules.id', $vehicule->id)->exists();
        
        if (!$exists) {
            $projet->vehicules()->attach($vehicule->id, [
                'date_affectation' => now(),
                'actif' => true
            ]);
            echo "Association créée: Projet " . $projet->titre . " -> Véhicule " . $vehicule->immatriculation . "\n";
        } else {
            echo "Association existe déjà: Projet " . $projet->titre . " -> Véhicule " . $vehicule->immatriculation . "\n";
        }
        
        // Tester la récupération
        $engins = $projet->vehicules()->get();
        echo "Engins associés au projet: " . $engins->count() . "\n";
        
        foreach ($engins as $engin) {
            echo "- " . $engin->immatriculation . " (" . $engin->marque . " " . $engin->modele . ")\n";
        }
        
    } else {
        echo "Pas de projet ou de véhicule trouvé\n";
        echo "Projets: " . App\Models\Operation::count() . "\n";
        echo "Véhicules: " . App\Models\Vehicule::count() . "\n";
    }
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
