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
            // Utiliser une opération réelle pour éviter tout chemin de création parallèle.
            $operation = Operation::first();

            if (!$operation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune opération existante disponible pour tester l\'envoi d\'email.',
                ], 422);
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
