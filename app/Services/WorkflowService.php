<?php

namespace App\Services;

use App\Models\Validation;
use App\Models\ValidationLog;
use App\Notifications\ValidationRequired;
use App\Notifications\ValidationCompleted;
use App\Models\Operation;
use App\Services\OperationRequeteService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Services\SmsService;

class WorkflowService
{
    public static function createValidation(string $module, int $recordId, string $type, string $titre, ?string $description = null, ?int $validateurId = null): Validation
    {
        $validation = Validation::create([
            'module_source' => $module,
            'record_id' => $recordId,
            'type' => $type,
            'titre' => $titre,
            'description' => $description,
            'initiateur_id' => Auth::id(),
            'validateur_id' => $validateurId,
            'statut' => 'en_attente',
        ]);

        // Log de création
        ValidationLog::create([
            'validation_id' => $validation->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'commentaire' => 'Création de la demande de validation',
        ]);

        // Notifier le validateur
        if ($validateurId) {
            $validateur = \App\Models\User::find($validateurId);
            if ($validateur) {
                Notification::send($validateur, new ValidationRequired($validation));
            }
        }

        return $validation;
    }

    public static function validate(int $validationId, string $decision, ?string $commentaire = null): bool
    {
        $validation = Validation::findOrFail($validationId);
        $ancienStatut = $validation->statut;

        $validation->update([
            'statut' => $decision === 'approve' ? 'valide' : ($decision === 'reject' ? 'rejete' : 'corrige'),
            'commentaire' => $commentaire,
            'date_validation' => now(),
            'validateur_id' => Auth::id(),
        ]);

        // Log de l'action
        ValidationLog::create([
            'validation_id' => $validation->id,
            'user_id' => Auth::id(),
            'action' => $decision === 'approve' ? 'validated' : ($decision === 'reject' ? 'rejected' : 'corrected'),
            'commentaire' => $commentaire,
            'ancien_statut' => ['statut' => $ancienStatut],
            'nouveau_statut' => ['statut' => $validation->statut],
        ]);

        // Notifier l'initiateur
        if ($validation->initiateur) {
            Notification::send($validation->initiateur, new ValidationCompleted($validation, $decision));
        }

        // Synchroniser avec le module source le cas échéant
        if ($validation->module_source === 'requetes' && $validation->record_id) {
            $operation = Operation::find($validation->record_id);
            if ($operation) {
                $service = app(OperationRequeteService::class);
                $nouveauStatut = match ($decision) {
                    'approve' => 'EN_COURS_DE_TRAITEMENT',
                    'reject' => 'REJETEE',
                    'correct' => 'ENREGISTREE',
                    default => null,
                };
                if ($nouveauStatut) {
                    $service->changerStatut($operation, $nouveauStatut, $commentaire);
                }

                // Envoi SMS au demandeur / service pour les requêtes
                $demandeur = $validation->initiateur; // généralement l'utilisateur ayant créé la demande
                $serviceDest = $operation->serviceDestinataire;

                $phone = $demandeur->phone
                    ?? optional($serviceDest)->phone
                    ?? null;

                if ($phone) {
                    $numero = $operation->reference_requete ?? ('OP-'.$operation->id);
                    $serviceNom = optional($serviceDest)->nom ?? 'Service destinataire';
                    $statutLibelle = match ($decision) {
                        'approve' => 'VALIDÉE',
                        'reject' => 'REJETÉE',
                        'correct' => 'À CORRIGER',
                        default => strtoupper($decision),
                    };
                    $lien = route('requetes.show', $operation);

                    $message = "[KENAM SERVICES] Requête {$numero}\n" .
                        "Statut : {$statutLibelle}\n" .
                        "Service : {$serviceNom}\n" .
                        "Voir la demande : {$lien}";

                    SmsService::send($phone, $message);
                }
            }
        }

        return true;
    }

    public static function getValidationsForUser(?int $userId = null)
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();

        return Validation::with(['initiateur', 'validateur', 'logs'])
            ->where(function($query) use ($user) {
                $query->where('validateur_id', $user->id)
                      ->orWhere('initiateur_id', $user->id);
            })
            ->whereIn('statut', ['en_attente', 'en_cours'])
            ->latest();
    }

    public static function getStatistics()
    {
        $totalEnAttente = Validation::where('statut', 'en_attente')->count();
        $totalEnCours = Validation::where('statut', 'en_cours')->count();
        $totalValide = Validation::where('statut', 'valide')->count();
        $totalRejete = Validation::where('statut', 'rejete')->count();

        $totalGlobal = $totalEnAttente + $totalEnCours + $totalValide + $totalRejete;
        $tauxValidation = $totalGlobal > 0
            ? round(($totalValide / $totalGlobal) * 100, 2)
            : 0;

        return [
            'total_en_attente' => $totalEnAttente,
            'total_en_cours' => $totalEnCours,
            'total_valide' => $totalValide,
            'total_rejete' => $totalRejete,
            'temps_moyen_traitement' => self::calculateAverageProcessingTime(),
            'taux_validation' => $tauxValidation,
        ];
    }

    private static function calculateAverageProcessingTime(): float
    {
        $validations = Validation::whereNotNull('date_validation')
            ->whereNotNull('created_at')
            ->get();

        if ($validations->isEmpty()) {
            return 0;
        }

        $totalHours = $validations->sum(function ($validation) {
            return $validation->created_at->diffInHours($validation->date_validation);
        });

        return round($totalHours / $validations->count(), 2);
    }
}
