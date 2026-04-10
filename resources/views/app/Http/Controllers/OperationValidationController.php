<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\ServiceOperationnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OperationHighAmountMail;

class OperationValidationController extends Controller
{
    /**
     * Valider une opération et notifier les services concernés
     */
    public function validateOperation(Request $request, Operation $operation)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0',
            'services_additionnels' => 'nullable|array',
            'services_additionnels.*' => 'exists:services_operationnels,id',
            'commentaire' => 'nullable|string|max:500',
        ]);

        // Mettre à jour le montant de l'opération
        $operation->update([
            'montant' => $validated['montant'],
            'statut' => 'en_validation',
            'commentaire_validation' => $validated['commentaire'] ?? null,
        ]);

        // Vérifier si le montant est ≥ 1.000.000 FCFA
        if ($validated['montant'] >= 1000000) {
            $this->notifierDGManager($operation, $validated['services_additionnels'] ?? []);
        }

        return response()->json([
            'success' => true,
            'message' => $validated['montant'] >= 1000000
                ? 'Opération validée et notifications envoyées au DG et Manager'
                : 'Opération validée avec succès',
            'operation' => $operation->fresh(),
            'notifications_sent' => $validated['montant'] >= 1000000
        ]);
    }

    /**
     * Notifier le DG et Manager pour les montants élevés
     */
    private function notifierDGManager(Operation $operation, array $servicesAdditionnels)
    {
        // Récupérer les emails du DG et Manager depuis la configuration
        $emailDG = config('app.mail_dg', 'dg@kenamservices.com');
        $emailManager = config('app.mail_manager', 'manager@kenamservices.com');

        // Récupérer les services additionnels
        $services = ServiceOperationnel::whereIn('id', $servicesAdditionnels)->get();

        // Envoyer l'email au DG
        Mail::to($emailDG)->send(new OperationHighAmountMail($operation, 'dg', $services));

        // Envoyer l'email au Manager
        Mail::to($emailManager)->send(new OperationHighAmountMail($operation, 'manager', $services));

        // Notifier les services additionnels
        foreach ($services as $service) {
            if ($service->peutRecevoirRequetes()) {
                Mail::to($service->email)->send(new OperationHighAmountMail($operation, 'service', $services, $service));
            }
        }
    }

    /**
     * Afficher le formulaire de validation avec services additionnels
     */
    public function showValidationForm(Operation $operation)
    {
        $services = ServiceOperationnel::actif()->ordre()->get();

        return response()->json([
            'operation' => $operation,
            'services' => $services,
            'seuil_validation' => 1000000,
            'montant_actuel' => $operation->montant ?? 0,
            'requires_validation' => ($operation->montant ?? 0) >= 1000000
        ]);
    }

    /**
     * Récupérer les services pour la sélection
     */
    public function getServicesForValidation()
    {
        $services = ServiceOperationnel::actif()->ordre()->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'nom' => $service->nom,
                'code' => $service->code,
                'email' => $service->email,
                'icone' => $service->icone,
                'couleur' => $service->couleur,
                'responsable' => $service->responsable,
            ];
        });

        return response()->json($services);
    }

    /**
     * Vérifier si une opération nécessite une validation spéciale
     */
    public function checkValidationRequired(Request $request)
    {
        $montant = $request->input('montant');
        $requiresValidation = $montant >= 1000000;

        return response()->json([
            'requires_validation' => $requiresValidation,
            'seuil' => 1000000,
            'montant' => $montant,
            'difference' => $requiresValidation ? $montant - 1000000 : 1000000 - $montant,
            'message' => $requiresValidation
                ? 'Ce montant nécessite une validation du DG et Manager'
                : 'Ce montant ne nécessite pas de validation spéciale'
        ]);
    }
}
