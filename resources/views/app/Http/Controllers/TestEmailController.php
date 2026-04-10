<?php

namespace App\Http\Controllers;

use App\Mail\OperationHighAmountMail;
use App\Models\Operation;
use App\Models\ServiceOperationnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class TestEmailController extends Controller
{
    public function testEmail()
    {
        try {
            // Récupérer une opération existante ou en créer une factice
            $operation = Operation::first();

            if (!$operation) {
                // Créer une opération factice avec les champs minimaux requis
                $operation = Operation::create([
                    'titre' => 'Test d\'envoi d\'email',
                    'montant' => 1500000,
                    'statut' => 'en_attente',
                    'description' => 'Ceci est un test d\'envoi d\'email',
                    'type' => 'depense',
                    'date_operation' => now(),
                    'statut_requete' => 'ENREGISTREE',
                    'type_requete' => 'test',
                    'reference_requete' => 'TEST-' . time(),
                    'description_requete' => 'Opération de test pour l\'envoi d\'email',
                    'service' => 'Service Test',
                    'responsable_name' => 'Admin Test',
                    'responsable_email' => 'admin@example.com',
                    'demandeur_name' => 'Demandeur Test',
                    'demandeur_email' => 'demandeur@example.com',
                    'quantite' => 1
                ]);
            }

            // Récupérer ou créer un service pour le test
            $service = ServiceOperationnel::first();

            if (!$service) {
                $service = ServiceOperationnel::create([
                    'nom' => 'Service Test',
                    'email' => 'service.test@example.com',
                    'actif' => true,
                ]);
            }

            // Récupérer l'email de destination depuis la requête ou utiliser une valeur par défaut
            $email = request('email', 'test@example.com');

            // Envoyer l'email de test
            Mail::to($email)->send(new OperationHighAmountMail(
                $operation,
                'service',
                [$service],
                $service
            ));

            return response()->json([
                'success' => true,
                'message' => 'Email envoyé avec succès à ' . $email,
                'operation_id' => $operation->id,
                'service_id' => $service->id,
                'mail_config' => [
                    'driver' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                    'username' => config('mail.mailers.smtp.username') ? 'défini' : 'non défini',
                    'from' => [
                        'address' => config('mail.from.address'),
                        'name' => config('mail.from.name'),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}
