<?php

namespace App\Services;

use App\Models\Operation;
use App\Models\ServiceOperationnel;
use App\Models\OperationServiceValidation;
use App\Models\EntrepriseSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OperationValidationService
{
    /**
     * Configure le circuit de validation d'une opération
     * ORDRE : Validateurs 1,2,3 (optionnels) → Destinataire Principal (Responsable)
     *         → DG si montant > seuil configuré → Comptabilité si nécessaire
     */
    public function setupValidationChain(Operation $operation, int $destinatairePrincipalId, array $customValidators = []): array
    {
        // Vérifier si le circuit existe déjà
        $exists = DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->exists();

        if ($exists) {
            return [];
        }

        // Chaque étape : [service_id, role_label]
        $steps = [];

        // ÉTAPE 1 : Validateurs optionnels (1, 2, 3)
        $validatorCount = 0;
        foreach ($customValidators as $vId) {
            $vId = (int) $vId;
            if ($vId && !in_array($vId, array_column($steps, 0))) {
                $validatorCount++;
                $steps[] = [$vId, 'validateur_' . $validatorCount];
            }
        }

        // ÉTAPE 2 : Destinataire Principal (Responsable de service émetteur)
        $destId = (int) $destinatairePrincipalId;
        if ($destId && !in_array($destId, array_column($steps, 0))) {
            $steps[] = [$destId, 'responsable'];
        }

        // Récupérer les paramètres dynamiques de l'entreprise
        $entreprise = EntrepriseSettings::getActive();
        $seuilPrincipal = $entreprise->seuil_validation_principal ?? 500000;
        $seuilDG = $entreprise->seuil_validation_dg ?? 1000000;
        $seuilDGForce = $entreprise->seuil_validation_dg_force ?? 2500000; // Seuil où DG doit obligatoirement valider

        // ÉTAPE ADDITIONNELLE : Destinataire Principal (Paramètres) si montant < seuil principal
        if (($operation->montant ?? 0) <= $seuilPrincipal) {
            $emailPrincipal = $entreprise->email_destinataire_principal;
            if ($emailPrincipal) {
                $principalService = DB::table('services_operationnels')->where('email', $emailPrincipal)->first();
                if ($principalService && !in_array($principalService->id, array_column($steps, 0))) {
                    $steps[] = [$principalService->id, 'destinataire_principal'];
                }
            }
        }

        // ÉTAPE 3 : Direction Générale (conditions multiples)
        $montant = $operation->montant ?? 0;
        $requiresDG = false;
        $dgReason = '';

        if ($montant > $seuilDGForce) {
            // Condition 1: Montant très élevé (> seuil DG force)
            $requiresDG = true;
            $dgReason = "Montant élevé (> " . number_format($seuilDGForce, 0, ',', ' ') . " FCFA)";
        } elseif ($montant > $seuilDG) {
            // Condition 2: Montant élevé (> seuil DG normal)
            $requiresDG = true;
            $dgReason = "Montant supérieur au seuil DG (" . number_format($seuilDG, 0, ',', ' ') . " FCFA)";
        } /* elseif ($operation->type_operation_id) {
            // Condition 3: Type d'opération requérant DG
            $typeOperation = DB::table('types_operations')->where('id', $operation->type_operation_id)->first();
            if ($typeOperation && isset($typeOperation->require_dg_validation) && $typeOperation->require_dg_validation) {
                $requiresDG = true;
                $dgReason = "Type d'opération requérant validation DG";
            }
        } */ elseif ($operation->priorite === 'urgente') {
            // Condition 4: Priorité urgente
            $requiresDG = true;
            $dgReason = "Opération urgente";
        }

        if ($requiresDG) {
            $dgEmail = $entreprise->email_dg ?? config('app.dg_email', 'f.tourefatim@kenamsholding.net');
            $dgService = DB::table('services_operationnels')->where('email', $dgEmail)->first();
            if ($dgService && !in_array($dgService->id, array_column($steps, 0))) {
                $steps[] = [$dgService->id, 'dg'];
                Log::info("DG ajouté au circuit - Opération #{$operation->id} - Raison: {$dgReason}");
            }
        }

        // ÉTAPE 4 : Comptabilité/Trésorerie (si paiement requis)
        if ($montant > 0) {
            $comptaEmail = $entreprise->email_tresorerie ?? config('app.compta_email', 'konanakanie@kenamservices.net');
            $comptaService = DB::table('services_operationnels')->where('email', $comptaEmail)->first();
            if ($comptaService && !in_array($comptaService->id, array_column($steps, 0))) {
                $steps[] = [$comptaService->id, 'compta'];
            }
        }

        if (empty($steps)) {
            Log::warning("setupValidationChain : Aucune étape générée pour l'opération #{$operation->id}");
            return [];
        }

        // Insertion en base avec ordre explicite
        foreach ($steps as $index => [$serviceId, $roleLabel]) {
            DB::table('operation_service_validation')->insert([
                'operation_id'            => $operation->id,
                'service_operationnel_id' => $serviceId,
                'ordre_validation'        => $index + 1,
                'statut'                  => ($index === 0) ? 'EN_COURS' : 'EN_ATTENTE',
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);
        }

        // Statut initial de l'opération
        $firstRole = $steps[0][1];
        $operation->update(['statut_courant' => $this->getRoleStatus($firstRole)]);

        Log::info("Circuit de validation créé pour l'opération #{$operation->id}", [
            'etapes' => array_map(fn($s) => ['service_id' => $s[0], 'role' => $s[1]], $steps),
            'montant' => $operation->montant,
            'necessite_dg' => $requiresDG,
            'dg_reason' => $dgReason,
            'seuil_dg' => $seuilDG,
            'seuil_dg_force' => $seuilDGForce,
        ]);

        return $steps;
    }

    /**
     * Retourne le statut_courant correspondant au rôle d'une étape de validation
     */
    public function getRoleStatus(string $roleLabel): string
    {
        return match(true) {
            str_starts_with($roleLabel, 'validateur') => 'en_validation',
            $roleLabel === 'responsable'              => 'en_validation_responsable',
            $roleLabel === 'destinataire_principal'   => 'en_validation',
            $roleLabel === 'dg'                       => 'en_validation_dg',
            $roleLabel === 'compta'                   => 'bon_pour_accord',
            default                                   => 'en_validation',
        };
    }

    /**
     * Détermine le rôle à partir du service_operationnel_id
     */
    public function getRoleFromService(int $serviceOperationnelId): string
    {
        $service = DB::table('services_operationnels')->where('id', $serviceOperationnelId)->first();

        if (!$service) {
            return 'unknown';
        }

        // Récupérer les emails de configuration
        $dgEmail = config('app.dg_email', 'f.tourefatim@kenamsholding.net');
        $comptaEmail = config('app.compta_email', 'konanakanie@kenamservices.net');

        // Déterminer le rôle selon l'email ou le nom du service
        if ($service->email === $dgEmail) {
            return 'dg';
        } elseif ($service->email === $comptaEmail) {
            return 'compta';
        } elseif (str_contains(strtolower($service->nom ?? ''), 'direction') || str_contains(strtolower($service->nom ?? ''), 'dg')) {
            return 'dg';
        } elseif (str_contains(strtolower($service->nom ?? ''), 'compta') || str_contains(strtolower($service->nom ?? ''), 'trésor')) {
            return 'compta';
        } else {
            return 'responsable';
        }
    }

    /**
     * Attacher le role_label dynamiquement aux étapes
     */
    public function attachRoleLabels($steps)
    {
        foreach ($steps as $step) {
            $role = $this->getRoleFromService($step->service_operationnel_id);
            $step->role_label = match($role) {
                'dg' => 'Direction Générale',
                'compta' => 'Comptabilité / Trésorerie',
                'responsable' => 'Responsable de Service',
                default => 'Validateur Technique',
            };
        }
        return $steps;
    }

    /**
     * Récupère l'étape EN_COURS pour une opération
     */
    public function getCurrentStep(Operation $operation)
    {
        return DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->where('statut', 'EN_COURS')
            ->first();
    }

    /**
     * Récupère toutes les étapes de validation d'une opération
     */
    public function getValidationSteps(Operation $operation)
    {
        return DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->orderBy('operation_service_validation.ordre_validation')
            ->get([
                'operation_service_validation.*',
                'services_operationnels.nom  as service_name',
                'services_operationnels.email as service_email',
            ]);
    }
}
