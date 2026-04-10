<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OperationSubmittedNotification;
use App\Mail\HighAmountOperationNotification;
use App\Mail\OperationValidationRequest;
use App\Models\Operation;

class EmailNotificationService
{
    /**
     * Envoyer les emails de notification lors de la soumission d'une opération
     */
    public function sendOperationSubmittedNotifications(Operation $operation, $demandeur)
    {
        try {
            // 1. Email de confirmation au demandeur
            if ($demandeur && $demandeur->email) {
                Mail::to($demandeur->email)
                    ->send(new OperationSubmittedNotification($operation, $demandeur));

                Log::info("Email de confirmation envoyé au demandeur: {$demandeur->email}");
            }

            // 2. Email au validateur principal du service
            if ($operation->operationalService) {
                $service = $operation->operationalService;

                // Priorité 1: Email du validateur si défini
                if ($service->validateur_email) {
                    Mail::to($service->validateur_email)
                        ->send(new OperationValidationRequest($operation, 'validation'));

                    Log::info("Email de validation envoyé au validateur: {$service->validateur_email}");
                }
                // Priorité 2: Email du service si défini
                elseif ($service->email) {
                    Mail::to($service->email)
                        ->send(new OperationValidationRequest($operation, 'validation'));

                    Log::info("Email de validation envoyé au service: {$service->email}");
                } else {
                    Log::warning("Aucun email configuré pour le service: {$service->nom} (ID: {$service->id})");
                }

                // 3. Emails en copie (CC) si définis
                if ($service->emails_cc) {
                    $ccEmails = explode(',', $service->emails_cc);
                    $ccEmails = array_map('trim', $ccEmails);
                    $ccEmails = array_filter($ccEmails, function($email) {
                        return filter_var($email, FILTER_VALIDATE_EMAIL);
                    });

                    if (!empty($ccEmails)) {
                        foreach ($ccEmails as $ccEmail) {
                            Mail::to($ccEmail)
                                ->send(new OperationValidationRequest($operation, 'copie'));
                        }

                        Log::info("Emails CC envoyés à: " . implode(', ', $ccEmails));
                    }
                }
            }

            // 4. Email au DG si montant élevé (> 99 999 FCFA)
            if ($operation->montant > 99999) {
                $dgEmail = config('app.dg_email', 'pmo.arthur@webpluriel.com'); // Utiliser votre email par défaut
                try {
                    Mail::to($dgEmail)
                        ->send(new HighAmountOperationNotification($operation, $demandeur));

                    Log::info("Email de montant élevé envoyé au DG: {$dgEmail}");
                } catch (\Exception $e) {
                    Log::warning("Email DG non envoyé (adresse invalide): {$dgEmail} - " . $e->getMessage());
                    // Ne pas échouer toute l'opération si juste l'email DG échoue
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi des emails: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer un email de validation à un service spécifique
     */
    public function sendValidationEmail(Operation $operation, $serviceEmail, $type = 'validation')
    {
        try {
            Mail::to($serviceEmail)
                ->send(new OperationValidationRequest($operation, $type));

            Log::info("Email de validation envoyé à: {$serviceEmail}");
            return true;

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de l'email de validation à {$serviceEmail}: " . $e->getMessage());
            return false;
        }
    }
}
