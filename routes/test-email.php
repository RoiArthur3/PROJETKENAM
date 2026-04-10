<?php

use Illuminate\Support\Facades\Route;
use App\Models\Operation;
use App\Models\ServiceOperationnel;
use App\Mail\OperationHighAmountMail;
use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {
    // Créer une opération de test
    $operation = Operation::first();

    if (!$operation) {
        $operation = new Operation([
            'titre' => 'Test d\'envoi d\'email',
            'montant' => 1500000, // Montant supérieur à 1.000.000 pour déclencher la notification
            'statut' => 'en_attente',
            'description' => 'Ceci est un test d\'envoi d\'email',
        ]);
        $operation->save();
    }

    // Récupérer des services pour le test
    $services = ServiceOperationnel::take(2)->get();

    if ($services->isEmpty()) {
        // Créer un service de test si nécessaire
        $service = new ServiceOperationnel([
            'nom' => 'Service Test',
            'email' => 'service.test@example.com',
            'actif' => true,
        ]);
        $service->save();
        $services = collect([$service]);
    }

    // Envoyer l'email de test
    try {
        Mail::to('test@example.com')->send(new OperationHighAmountMail(
            $operation,
            'service',
            $services->toArray(),
            $services->first()
        ));

        return 'Email de test envoyé avec succès à test@example.com';
    } catch (\Exception $e) {
        return 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage();
    }
});
