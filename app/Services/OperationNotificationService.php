<?php

namespace App\Services;

use App\Models\Operation;
use App\Models\ServiceOperationnel;
use App\Models\EntrepriseSettings;
use App\Mail\SendEmail;
use App\Mail\ValidationStepNotification;
use App\Mail\OperationApprovedNotification;
use App\Mail\OperationRejectedMail;
use App\Mail\BonPourAccordNotification;
use App\Mail\OperationPaidNotification;
use App\Mail\OperationStepApprovedMail;
use App\Mail\DynamicMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\SMS\SMSManager;
use App\Services\SMSStatisticsService;
use App\Services\EmailStatisticsService;

class OperationNotificationService
{
    private SMSStatisticsService $smsStatisticsService;
    private EmailStatisticsService $emailStatisticsService;

    public function __construct()
    {
        $this->smsStatisticsService = app(SMSStatisticsService::class);
        $this->emailStatisticsService = app(EmailStatisticsService::class);
    }
    /**
     * Centralise l'envoi des mails de demande de validation avec vérification des conditions
     */
    public function sendValidationRequestMail(Operation $operation, $ccServices = ''): bool
    {
        try {
            Log::info("Tentative d'envoi de mail de validation pour l'opération #{$operation->id}");

            // 1. Trouver l'étape actuellement "EN_COURS"
            $currentStep = DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('statut', 'EN_COURS')
                ->first();

            if (!$currentStep) {
                Log::warning("Échec envoi mail : Aucune étape 'EN_COURS' trouvée pour l'opération #{$operation->id}");
                return false;
            }

            // 2. Récupérer l'email du service actuellement en charge de la validation
            $validatorService = ServiceOperationnel::find($currentStep->service_operationnel_id);
            $entreprise = EntrepriseSettings::getActive();
            $validatorEmail = $validatorService && !empty($validatorService->email)
                ? $validatorService->email
                : ($entreprise && !empty($entreprise->email_destinataire_principal)
                    ? $entreprise->email_destinataire_principal
                    : config('mail.from.address'));

            if (empty($validatorEmail) || !filter_var($validatorEmail, FILTER_VALIDATE_EMAIL)) {
                Log::warning("Échec envoi mail : Aucun email valide pour l'étape de validation en cours (Opération #{$operation->id})", [
                    'service_operationnel_id' => $currentStep->service_operationnel_id,
                ]);
                return false;
            }

            // 3. Préparer les CC (services en copie si besoin)
            $ccEmails = [];
            if ($ccServices) {
                $ccServiceIds = is_array($ccServices) ? $ccServices : explode(',', $ccServices);
                foreach ($ccServiceIds as $id) {
                    $sc = ServiceOperationnel::find(trim($id));
                    if ($sc && $sc->email && filter_var($sc->email, FILTER_VALIDATE_EMAIL)) {
                        $ccEmails[] = $sc->email;
                    }
                }
            }

            // 4. Générer l'URL et envoyer
            $url = request()->getSchemeAndHttpHost() . "/operations/{$operation->id}/validate";
            $mail = new SendEmail($operation, $url);

            if (!empty($ccEmails)) {
                $mail->cc(array_unique($ccEmails));
            }

            // Envoyer l'email
            Mail::to($validatorEmail)->send($mail);

            // Enregistrer l'email dans les statistiques
            $this->emailStatisticsService->logEmail([
                'recipient' => $validatorEmail,
                'template' => 'validation_request',
                'status' => 'success',
                'provider' => 'smtp',
                'operation_reference' => $operation->numero_operation,
                'subject' => 'Demande de validation - Opération #' . $operation->numero_operation
            ]);

            // Envoyer aussi le SMS si le service a un téléphone
            if ($validatorService && !empty($validatorService->telephone)) {
                $this->sendValidationSMS($validatorService, $operation, $url);
            }

            Log::info("Mail de validation envoyé avec succès à {$validatorEmail} (Opération #{$operation->id})");
            return true;

        } catch (\Exception $e) {
            Log::error("Erreur technique lors de l'envoi du mail (Opération #{$operation->id}) : " . $e->getMessage(), [
                'recipient' => $validatorEmail ?? null,
            ]);

            // Enregistrer l'échec de l'email
            $this->emailStatisticsService->logEmail([
                'recipient' => $validatorEmail ?? 'unknown',
                'template' => 'validation_request',
                'status' => 'failed',
                'provider' => 'smtp',
                'operation_reference' => $operation->numero_operation,
                'error_message' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Envoie un SMS de validation
     */
    private function sendValidationSMS(ServiceOperationnel $validatorService, Operation $operation, string $url): void
    {
        try {
            $smsResult = app(SMSManager::class)->sendTemplate(
                $validatorService->telephone,
                'operation_alert',
                [
                    'reference' => $operation->numero_operation,
                    'titre' => $operation->titre,
                    'montant' => number_format($operation->montant, 0, ',', ' '),
                    'demandeur' => $operation->demandeur_name ?? 'Non spécifié',
                    'url' => $url,
                    'priorite' => $operation->priorite ?? 'normale',
                    'echeance' => $operation->echeance ? $operation->echeance->format('d/m/Y') : 'Non définie'
                ]
            );

            if (($smsResult['success'] ?? false) === true) {
                Log::info("SMS d'intervention envoyé à {$validatorService->telephone} pour l'opération #{$operation->id}", [
                    'provider' => $smsResult['provider'] ?? null,
                    'service' => $validatorService->nom,
                    'reference' => $operation->numero_operation,
                ]);

                // Enregistrer le SMS dans les statistiques
                $this->smsStatisticsService->logSMS([
                    'phone' => $validatorService->telephone,
                    'template' => 'operation_alert',
                    'status' => 'success',
                    'provider' => $smsResult['provider'] ?? null,
                    'operation_reference' => $operation->numero_operation,
                    'message' => "Validation requise pour opération #{$operation->numero_operation}"
                ]);
            } else {
                Log::warning("Echec envoi SMS d'intervention pour l'opération #{$operation->id}", [
                    'telephone' => $validatorService->telephone,
                    'provider' => $smsResult['provider'] ?? null,
                    'error' => $smsResult['error'] ?? 'Erreur inconnue',
                    'service' => $validatorService->nom,
                ]);

                // Enregistrer l'échec du SMS
                $this->smsStatisticsService->logSMS([
                    'phone' => $validatorService->telephone,
                    'template' => 'operation_alert',
                    'status' => 'failed',
                    'provider' => $smsResult['provider'] ?? null,
                    'operation_reference' => $operation->numero_operation,
                    'error_message' => $smsResult['error'] ?? 'Erreur inconnue'
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Erreur envoi SMS validation (Opération #{$operation->id}): " . $e->getMessage(), [
                'telephone' => $validatorService->telephone,
                'service' => $validatorService->nom,
            ]);
        }
    }

    /**
     * Notifie le demandeur de l'approbation
     */
    public function notifyRequesterApproval(Operation $operation, string $commentaire = ''): void
    {
        if ($this->isValidEmail($operation->demandeur_email)) {
            try {
                Mail::to($operation->demandeur_email)->send(
                    new OperationApprovedNotification($operation, $commentaire)
                );
                Log::info("Email d'approbation envoyé au demandeur: {$operation->demandeur_email}");
            } catch (\Exception $e) {
                Log::error('Erreur envoi email approbation demandeur: ' . $e->getMessage(), [
                    'recipient' => $operation->demandeur_email,
                    'operation_id' => $operation->id,
                ]);
            }

            // Envoyer aussi le SMS si le demandeur a un téléphone
            $demandeurUser = \App\Models\User::where('email', $operation->demandeur_email)->first();
            if ($demandeurUser && !empty($demandeurUser->telephone)) {
                $this->sendApprovalSMS($demandeurUser, $operation, $commentaire);
            }
        }
    }

    /**
     * Envoie un SMS d'approbation au demandeur
     */
    private function sendApprovalSMS(\App\Models\User $demandeurUser, Operation $operation, string $commentaire): void
    {
        try {
            app(SMSManager::class)->sendTemplate(
                $demandeurUser->telephone,
                'validation_approved',
                [
                    'reference' => $operation->numero_operation,
                    'validateur' => 'System',
                    'montant' => number_format($operation->montant, 0, ',', ' ')
                ]
            );

            Log::info("SMS d'approbation envoyé à {$demandeurUser->telephone} pour l'opération #{$operation->id}");
        } catch (\Exception $e) {
            Log::error("Erreur envoi SMS approbation (Opération #{$operation->id}): " . $e->getMessage());
        }
    }

    /**
     * Notifie la trésorerie pour le choix de caisse
     */
    public function notifyTreasury(Operation $operation, string $commentaire = ''): void
    {
        try {
            $entreprise = EntrepriseSettings::getActive();
            $tresorerieEmail = $entreprise?->email_tresorerie ?? config('mail.from.address');

            if ($this->isValidEmail($tresorerieEmail)) {
                Mail::to($tresorerieEmail)->send(new DynamicMail([
                    'subject' => "À PAYER : Opération approuvée #{$operation->id}",
                    'view' => 'emails.operations.notify-tresorerie',
                    'data' => ['operation' => $operation],
                ]));

                Log::info("Notification Trésorerie envoyée à {$tresorerieEmail} (Opération #{$operation->id})");
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification trésorerie: ' . $e->getMessage(), [
                'operation_id' => $operation->id,
            ]);
        }
    }

    /**
     * Notifie la caisse pour le Bon Pour Accord
     */
    public function notifyCaisse(Operation $operation, string $caisseEmail, string $commentaire = ''): void
    {
        if ($this->isValidEmail($caisseEmail)) {
            try {
                Mail::to($caisseEmail)->send(
                    new BonPourAccordNotification($operation, $commentaire)
                );
                Log::info("BON POUR EXÉCUTION envoyé à la caisse : {$caisseEmail} (Opération #{$operation->id})");
            } catch (\Exception $e) {
                Log::error('Erreur mail BON POUR EXÉCUTION caisse: ' . $e->getMessage(), [
                    'recipient' => $caisseEmail,
                    'operation_id' => $operation->id,
                ]);
            }
        }
    }

    /**
     * Notifie le paiement avec SMS
     */
    public function notifyPayment(Operation $operation): void
    {
        if ($operation->demandeur_email) {
            try {
                Mail::to($operation->demandeur_email)->send(
                    new OperationPaidNotification($operation, $operation->demandeur_name ?? 'Demandeur')
                );
                Log::info("Email de clôture envoyé au demandeur : {$operation->demandeur_email}");
            } catch (\Exception $e) {
                Log::error('Erreur envoi mail clôture paiement : ' . $e->getMessage());
            }

            // Envoyer aussi le SMS de paiement
            $this->sendPaymentSMS($operation);
        }
    }

    /**
     * Notifie le rejet avec SMS
     */
    public function notifyRejection(Operation $operation, string $commentaire): void
    {
        if ($this->isValidEmail($operation->demandeur_email)) {
            try {
                Mail::to($operation->demandeur_email)->send(
                    new OperationRejectedMail($operation, $commentaire)
                );
                Log::info("Email de rejet envoyé au demandeur: {$operation->demandeur_email}");
            } catch (\Exception $e) {
                Log::error('Erreur envoi email rejet: ' . $e->getMessage(), [
                    'recipient' => $operation->demandeur_email,
                    'operation_id' => $operation->id,
                ]);
            }

            // Envoyer aussi le SMS si le demandeur a un téléphone
            $demandeurUser = \App\Models\User::where('email', $operation->demandeur_email)->first();
            if ($demandeurUser && !empty($demandeurUser->telephone)) {
                $this->sendRejectionSMS($demandeurUser, $operation, $commentaire);
            }
        }
    }

    /**
     * Envoie un SMS de rejet
     */
    private function sendRejectionSMS(\App\Models\User $demandeurUser, Operation $operation, string $commentaire): void
    {
        try {
            $smsResult = app(SMSManager::class)->sendTemplate(
                $demandeurUser->telephone,
                'validation_rejected',
                [
                    'reference' => $operation->numero_operation,
                    'validateur' => 'Validateur',
                    'montant' => number_format($operation->montant, 0, ',', ' '),
                    'titre' => $operation->titre,
                    'motif' => $commentaire ?? 'Non spécifié'
                ]
            );

            if (($smsResult['success'] ?? false) === true) {
                Log::info("SMS de rejet envoyé à {$demandeurUser->telephone} pour l'opération #{$operation->id}", [
                    'provider' => $smsResult['provider'] ?? null,
                    'reference' => $operation->numero_operation,
                ]);
            } else {
                Log::warning("Echec envoi SMS rejet pour l'opération #{$operation->id}", [
                    'telephone' => $demandeurUser->telephone,
                    'provider' => $smsResult['provider'] ?? null,
                    'error' => $smsResult['error'] ?? 'Erreur inconnue',
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Erreur envoi SMS rejet (Opération #{$operation->id}): " . $e->getMessage(), [
                'telephone' => $demandeurUser->telephone,
            ]);
        }
    }

    /**
     * Envoie un SMS de notification de paiement
     */
    private function sendPaymentSMS(Operation $operation): void
    {
        if (!$operation->demandeur_email) return;

        $demandeurUser = \App\Models\User::where('email', $operation->demandeur_email)->first();
        if (!$demandeurUser || empty($demandeurUser->telephone)) return;

        try {
            $smsResult = app(SMSManager::class)->sendTemplate(
                $demandeurUser->telephone,
                'payment_notification',
                [
                    'reference' => $operation->numero_operation,
                    'montant' => number_format($operation->montant, 0, ',', ' '),
                    'titre' => $operation->titre,
                    'mode_paiement' => $operation->mode_paiement ?? 'Non spécifié',
                    'date_paiement' => $operation->paid_at ? $operation->paid_at->format('d/m/Y') : 'Aujourd\'hui'
                ]
            );

            if (($smsResult['success'] ?? false) === true) {
                Log::info("SMS de paiement envoyé à {$demandeurUser->telephone} pour l'opération #{$operation->id}", [
                    'provider' => $smsResult['provider'] ?? null,
                    'reference' => $operation->numero_operation,
                ]);
            } else {
                Log::warning("Echec envoi SMS paiement pour l'opération #{$operation->id}", [
                    'telephone' => $demandeurUser->telephone,
                    'provider' => $smsResult['provider'] ?? null,
                    'error' => $smsResult['error'] ?? 'Erreur inconnue',
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Erreur envoi SMS paiement (Opération #{$operation->id}): " . $e->getMessage(), [
                'telephone' => $demandeurUser->telephone,
            ]);
        }
    }

    /**
     * Vérifie si un email est valide
     */
    private function isValidEmail(?string $email): bool
    {
        return is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
