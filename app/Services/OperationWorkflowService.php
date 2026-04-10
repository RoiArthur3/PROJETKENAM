<?php

namespace App\Services;

use App\Models\Operation;
use App\Models\OperationStatusLog;
use App\Models\DepenseCaisse;
use App\Models\Caisse;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OperationWorkflowService
{
    private OperationValidationService $validationService;
    private OperationNotificationService $notificationService;

    public function __construct(
        OperationValidationService $validationService,
        OperationNotificationService $notificationService
    ) {
        $this->validationService = $validationService;
        $this->notificationService = $notificationService;
    }

    /**
     * Approuve une étape de validation
     */
    public function approveStep(Operation $operation, int $step, array $validated, User $user): string
    {
        return DB::transaction(function() use ($operation, $step, $validated, $user) {
            $isAdmin = in_array($user->role, ['admin', 'superadmin', 'moderator', 'moderateur']);
            $forceApprove = $isAdmin && ($validated['force_approve'] ?? false);

            if ($forceApprove) {
                return $this->forceApproveAll($operation, $validated, $user);
            }

            // Mettre à jour l'étape de validation actuelle
            DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('ordre_validation', $step)
                ->update([
                    'statut' => 'APPROUVE',
                    'validateur_id' => $user->id,
                    'date_validation' => now(),
                    'commentaire' => $validated['commentaire'] ?? null,
                ]);

            // Gérer le passage à l'étape suivante
            $nextStep = DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('ordre_validation', '>', $step)
                ->where('statut', 'EN_ATTENTE')
                ->orderBy('ordre_validation')
                ->first();

            if ($nextStep) {
                return $this->activateNextStep($operation, $nextStep, $step, $validated, $user);
            } else {
                return $this->completeWorkflow($operation, $step, $validated, $user);
            }
        });
    }

    /**
     * Approuve toutes les étapes (admin)
     */
    private function forceApproveAll(Operation $operation, array $validated, User $user): string
    {
        // Valider toutes les étapes restantes
        DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->where('statut', '!=', 'APPROUVE')
            ->update([
                'statut' => 'APPROUVE',
                'validateur_id' => $user->id,
                'date_validation' => now(),
                'commentaire' => ($validated['commentaire'] ?? '') . ' (Approbation directe par Admin)',
            ]);

        $previousStatus = $operation->statut_courant;
        $operation->update(['statut_courant' => 'Approuvé_en_attente_paiement']);

        // Log d'approbation directe
        $this->createStatusLog($operation, $previousStatus, 'Approuvé_en_attente_paiement', $user->name, 
            'Approbation directe effectuée par l\'administrateur. ' . ($validated['commentaire'] ?? ''));

        // Notifier le demandeur
        $this->notificationService->notifyRequesterApproval($operation, $validated['commentaire'] ?? 'Approbation directe par Admin');

        // Notifier la trésorerie
        $this->notificationService->notifyTreasury($operation, $validated['commentaire'] ?? 'Approbation directe par Admin');

        return 'Approuvé_en_attente_paiement';
    }

    /**
     * Active l'étape suivante
     */
    private function activateNextStep(Operation $operation, $nextStep, int $currentStepNumber, array $validated, User $user): string
    {
        // Activer l'étape suivante
        DB::table('operation_service_validation')
            ->where('id', $nextStep->id)
            ->update(['statut' => 'EN_COURS']);

        // Mettre à jour le statut de l'opération selon le RÔLE de l'étape suivante
        $nextRole = $this->validationService->getRoleFromService($nextStep->service_operationnel_id);
        $nextStatus = $this->validationService->getRoleStatus($nextRole);
        $prevStatus = $operation->statut_courant;
        $operation->update(['statut_courant' => $nextStatus]);

        // Envoyer la notification au validateur suivant
        $combinedCcServices = $this->combineCcServices($validated);
        $this->notificationService->sendValidationRequestMail($operation, $combinedCcServices);

        // Log de passage d'étape
        $this->createStatusLog($operation, $prevStatus, $nextStatus, $user->name, 
            "Étape {$currentStepNumber} validée par {$user->name}. En attente : {$nextRole}. " . ($validated['commentaire'] ?? ''));

        // Notifier le demandeur de l'avancement
        $this->notifyStepProgress($operation, $user, $validated['commentaire'] ?? null);

        return $nextStatus;
    }

    /**
     * Termine le workflow (dernière étape)
     */
    private function completeWorkflow(Operation $operation, int $step, array $validated, User $user): string
    {
        $prevStatus = $operation->statut_courant;
        $currentStepRow = DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->where('ordre_validation', $step)
            ->first();
        $currentRole = $currentStepRow ? $this->validationService->getRoleFromService($currentStepRow->service_operationnel_id) : 'unknown';

        if ($currentRole === 'compta') {
            return $this->handleComptabilityApproval($operation, $validated, $user, $prevStatus);
        } else {
            return $this->handleGenericApproval($operation, $validated, $user, $prevStatus);
        }
    }

    /**
     * Gère l'approbation par la comptabilité
     */
    private function handleComptabilityApproval(Operation $operation, array $validated, User $user, string $prevStatus): string
    {
        $finalStatus = 'Approuvé_en_attente_paiement';
        $operation->update(['statut_courant' => $finalStatus]);

        // Récupérer la caisse choisie
        $caisseEmail = $validated['caisse_email'] ?? null;
        $caisseId = $validated['caisse_id'] ?? null;

        // Fallback : chercher les caisses définies dans les paramètres
        if (!$caisseEmail || !filter_var($caisseEmail, FILTER_VALIDATE_EMAIL)) {
            $entreprise = \App\Models\EntrepriseSettings::getActive();
            $caisseEmail = $entreprise->email_caisse_1;

            if (!$caisseEmail) {
                $caisseService = DB::table('services_operationnels')
                    ->whereIn('email', [
                        'tossaviama@kenamservices.net',
                        'agouabenedicten@kenamservices.net',
                        'caisse@kenamservices.net',
                    ])
                    ->first();
                $caisseEmail = $caisseService?->email;
                $caisseId = $caisseService?->id;
            }
        }

        // Sauvegarder la caisse désignée
        $operation->forceFill([
            'caisse_executante_id' => $caisseId ?: null,
            'caisse_email' => $caisseEmail,
            'bon_pour_accord_at' => now(),
            'bon_pour_accord_by' => $user->id,
        ])->save();

        // Notifier le demandeur
        $this->notificationService->notifyRequesterApproval($operation, ($validated['commentaire'] ?? '') . '');

        // Notifier la caisse
        $this->notificationService->notifyCaisse($operation, $caisseEmail, ($validated['commentaire'] ?? '') . '');

        // Log
        $this->createStatusLog($operation, $prevStatus, $finalStatus, $user->name, 
            'Circuit terminé par ' . $user->name . '. ' . ($validated['commentaire'] ?? ''));

        return $finalStatus;
    }

    /**
     * Gère l'approbation générique (autre rôle)
     */
    private function handleGenericApproval(Operation $operation, array $validated, User $user, string $prevStatus): string
    {
        $finalStatus = 'Approuvé_en_attente_paiement';
        $operation->update(['statut_courant' => $finalStatus]);

        // Notifier la trésorerie
        $this->notificationService->notifyTreasury($operation, $validated['commentaire'] ?? null);

        // Notifier le demandeur
        $this->notificationService->notifyRequesterApproval($operation, $validated['commentaire'] ?? null);

        // Log
        $this->createStatusLog($operation, $prevStatus, $finalStatus, $user->name, 
            'Circuit terminé par ' . $user->name . '. ' . ($validated['commentaire'] ?? ''));

        return $finalStatus;
    }

    /**
     * Rejette une opération
     */
    public function rejectOperation(Operation $operation, int $step, array $validated, User $user): void
    {
        DB::transaction(function() use ($operation, $step, $validated, $user) {
            // Mettre à jour l'étape de validation
            DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('ordre_validation', $step)
                ->update([
                    'statut' => 'REJETE',
                    'validateur_id' => $user->id,
                    'date_validation' => now(),
                    'commentaire' => $validated['commentaire'],
                ]);

            // Mettre à jour le statut de l'opération
            $operation->update(['statut_courant' => 'rejetee']);

            // Enregistrer dans l'historique
                $this->createStatusLog(
                    $operation,
                    'pending_validation',
                    'rejetee',
                    $user->name,
                    isset($validated['commentaire']) && $validated['commentaire'] !== null ? $validated['commentaire'] : ''
                );

            // Notifier le demandeur
            $this->notificationService->notifyRejection(
                $operation,
                isset($validated['commentaire']) && $validated['commentaire'] !== null ? $validated['commentaire'] : ''
            );
        });
    }

    /**
     * Marque une opération comme payée
     */
    public function markAsPaid(Operation $operation, array $validated, User $user): void
    {
        $operation->forceFill([
            'is_paid' => true,
            'paid_at' => now(),
            'paid_by' => $user->id,
            'statut_courant' => 'payee',
            'mode_paiement' => $validated['mode_paiement'],
            'payment_reference' => $validated['payment_reference'] ?? null,
        ])->save();

        // Log du paiement
        $this->createStatusLog($operation, 'Approuvé_en_attente_paiement', 'payee', $user->name, 
            'Paiement exécuté par la caisse. Mode : ' . $validated['mode_paiement']
            . ($validated['payment_reference'] ? ' | Réf : ' . $validated['payment_reference'] : '')
            . ($validated['commentaire'] ? ' | ' . $validated['commentaire'] : ''));

        // NOTIFIER LE DEMANDEUR
        $this->notificationService->notifyPayment($operation);

        // SYNC COMPTABLE : Créer une dépense de caisse
        $this->createDepenseCaisse($operation, $validated, $user);
    }

    /**
     * Crée une dépense de caisse pour le paiement
     */
    private function createDepenseCaisse(Operation $operation, array $validated, User $user): void
    {
        try {
            if ($operation->montant > 0) {
                DepenseCaisse::create([
                    'operation_id' => $operation->id,
                    'reference' => 'DEP-OP-' . $operation->id,
                    'caisse_id' => $operation->caisse_executante_id ?? 1,
                    'libelle' => 'Paiement Opération #' . $operation->id . ' : ' . $operation->titre,
                    'description' => 'Paiement de l\'opération. Mode: ' . $validated['mode_paiement']
                                    . ($validated['payment_reference'] ? ' | Réf: ' . $validated['payment_reference'] : '')
                                    . ($validated['commentaire'] ? ' | ' . $validated['commentaire'] : ''),
                    'montant' => $operation->montant,
                    'date_depense' => $operation->paid_at ?? now(),
                    'mode_paiement' => $validated['mode_paiement'],
                    'statut' => 'validé',
                    'created_by' => $user->id,
                    'valideur_id' => $user->id,
                    'date_validation' => now(),
                ]);

                // Mettre à jour le solde de la caisse si spécifiée
                if ($operation->caisse_executante_id) {
                    $caisse = Caisse::find($operation->caisse_executante_id);
                    if ($caisse) {
                        $caisse->decrement('solde_actuel', $operation->montant);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Erreur création dépense (OP #{$operation->id}) : " . $e->getMessage());
        }
    }

    /**
     * Combine les services CC des différentes sources
     */
    private function combineCcServices(array $validated): string
    {
        $allCcServices = [];
        if (!empty($validated['cc_services'])) {
            $allCcServices[] = $validated['cc_services'];
        }
        if (!empty($validated['circuit_cc_services'])) {
            $allCcServices[] = $validated['circuit_cc_services'];
        }
        return implode(',', array_filter($allCcServices));
    }

    /**
     * Notifie le demandeur de l'avancement
     */
    private function notifyStepProgress(Operation $operation, User $user, ?string $commentaire): void
    {
        if (filter_var($operation->demandeur_email, FILTER_VALIDATE_EMAIL)) {
            try {
                \Illuminate\Support\Facades\Mail::to($operation->demandeur_email)->send(
                    new \App\Mail\OperationStepApprovedMail($operation, $user, $commentaire, null)
                );
            } catch (\Exception $e) {
                Log::error('Erreur mail avancement demandeur: ' . $e->getMessage(), [
                    'recipient' => $operation->demandeur_email,
                    'operation_id' => $operation->id,
                ]);
            }
        }
    }

    /**
     * Crée un log de changement de statut
     */
    private function createStatusLog(Operation $operation, string $fromStatus, string $toStatus, string $userName, string $commentaire): void
    {
        OperationStatusLog::create([
            'operation_id' => $operation->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'user_name' => $userName,
            'commentaire' => $commentaire,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
