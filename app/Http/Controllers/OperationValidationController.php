<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\ServiceOperationnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\OperationHighAmountMail;

class OperationValidationController extends Controller
{
    /**
     * Valider une opération et notifier les services concernés
     */
    public function validateOperation(Request $request, Operation $operation)
    {
        $entreprise = \App\Models\EntrepriseSettings::getActive();
        $seuilDG = $entreprise?->seuil_validation_dg ?? 1000000;
        $seuilPrincipal = $entreprise?->seuil_validation_principal ?? 500000;

        $hasExistingAttachment = $operation->fichiers()->exists();
        $attachmentRule = $hasExistingAttachment ? 'nullable' : 'required';

        $validated = $request->validate([
            'montant' => 'required|numeric|min:0',
            'services_additionnels' => 'nullable|array',
            'services_additionnels.*' => 'exists:services_operationnels,id',
            'commentaire' => 'nullable|string|max:500',
            'validation_attachment' => $attachmentRule . '|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,csv|max:10240',
        ]);

        if (!$hasExistingAttachment && !$request->hasFile('validation_attachment')) {
            return response()->json([
                'success' => false,
                'message' => 'Une pièce jointe est obligatoire avant validation de l\'opération.',
                'errors' => [
                    'validation_attachment' => ['Une pièce jointe est obligatoire avant validation de l\'opération.']
                ]
            ], 422);
        }

        if ($request->hasFile('validation_attachment')) {
            $file = $request->file('validation_attachment');
            $path = $file->store('operations/' . $operation->id . '/validations', 'public');

            $operation->fichiers()->create([
                'nom' => $file->getClientOriginalName(),
                'chemin' => $path,
                'type_mime' => $file->getMimeType() ?: 'application/octet-stream',
                'taille' => $file->getSize() ?: 0,
                'extension' => strtolower($file->getClientOriginalExtension() ?: ''),
                'uploaded_by' => Auth::id(),
                'description' => 'Pièce jointe ajoutée lors de la validation de l\'opération',
            ]);
        }

        // Mettre à jour le montant de l'opération
        $operation->update([
            'montant' => $validated['montant'],
            'statut' => 'en_validation',
            'commentaire_validation' => $validated['commentaire'] ?? null,
        ]);

        // Vérifier les notifications selon les seuils
        $notifyDG = ($validated['montant'] >= $seuilDG);
        $notifyPrincipal = ($validated['montant'] < $seuilPrincipal);

        if ($notifyDG || $notifyPrincipal) {
            $this->notifierSelonSeuils($operation, $validated['services_additionnels'] ?? [], $notifyDG, $notifyPrincipal);
        }

        $message = 'Opération validée avec succès';
        if ($notifyDG && $notifyPrincipal) $message .= ' (Notifications DG et Destinataire Principal envoyées)';
        elseif ($notifyDG) $message .= ' (Notification DG envoyée)';
        elseif ($notifyPrincipal) $message .= ' (Notification Destinataire Principal envoyée)';

        return response()->json([
            'success' => true,
            'message' => $message,
            'operation' => $operation->fresh(),
            'notifications_sent' => ($notifyDG || $notifyPrincipal)
        ]);
    }

    /**
     * Notifier les responsables selon les seuils
     */
    private function notifierSelonSeuils(Operation $operation, array $servicesAdditionnels, bool $notifyDG, bool $notifyPrincipal)
    {
        $entreprise = \App\Models\EntrepriseSettings::getActive();
        /** @var \Illuminate\Database\Eloquent\Collection<int, ServiceOperationnel> $services */
        $services = ServiceOperationnel::whereIn('id', $servicesAdditionnels)->get();
        $servicesPayload = $services->all();

        // Notification DG
        if ($notifyDG) {
            $emailDG = $entreprise?->email_dg ?? config('app.mail_dg', 'dg@kenamservices.com');
            $this->sendNotification($emailDG, new OperationHighAmountMail($operation, 'dg', $servicesPayload), 'DG', $operation);
        }

        // Notification Destinataire Principal
        if ($notifyPrincipal) {
            $emailPrincipal = $entreprise?->email_destinataire_principal ?? 'admin@kenamservices.net';
            $this->sendNotification($emailPrincipal, new OperationHighAmountMail($operation, 'manager', $servicesPayload), 'destinataire principal', $operation);
        }

        // Notifier les services additionnels
        foreach ($services as $service) {
            if ($service->peutRecevoirRequetes() && $service->email) {
                $this->sendNotification($service->email, new OperationHighAmountMail($operation, 'service', $servicesPayload, $service), 'service ' . $service->nom, $operation);
            }
        }
    }

    private function sendNotification(string $recipient, OperationHighAmountMail $mail, string $target, Operation $operation): void
    {
        try {
            Mail::to($recipient)->send($mail);
        } catch (\Throwable $exception) {
            Log::error('Echec envoi notification validation operation', [
                'operation_id' => $operation->id,
                'recipient' => $recipient,
                'target' => $target,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Afficher le formulaire de validation avec services additionnels
     */
    public function showValidationForm(Operation $operation)
    {
        $entreprise = \App\Models\EntrepriseSettings::getActive();
        $seuilDG = $entreprise?->seuil_validation_dg ?? 1000000;
        $seuilPrincipal = $entreprise?->seuil_validation_principal ?? 500000;
        $services = ServiceOperationnel::actif()->ordre()->get();

        $montant = $operation->montant ?? 0;
        $requiresDG = $montant >= $seuilDG;
        $requiresPrincipal = $montant < $seuilPrincipal;

        return response()->json([
            'operation' => $operation,
            'services' => $services,
            'seuil_dg' => $seuilDG,
            'seuil_principal' => $seuilPrincipal,
            'montant_actuel' => $montant,
            'requires_dg' => $requiresDG,
            'requires_principal' => $requiresPrincipal,
            'requires_validation' => ($requiresDG || $requiresPrincipal)
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
        $entreprise = \App\Models\EntrepriseSettings::getActive();
        $seuilDG = $entreprise?->seuil_validation_dg ?? 1000000;
        $seuilPrincipal = $entreprise?->seuil_validation_principal ?? 500000;
        
        $montant = $request->input('montant');
        $requiresDG = $montant >= $seuilDG;
        $requiresPrincipal = $montant < $seuilPrincipal;

        $message = 'Traitement standard';
        if ($requiresDG && $requiresPrincipal) $message = 'Validation DG et Destinataire Principal requise';
        elseif ($requiresDG) $message = 'Validation DG requise';
        elseif ($requiresPrincipal) $message = 'Validation Destinataire Principal requise';

        return response()->json([
            'requires_validation' => ($requiresDG || $requiresPrincipal),
            'requires_dg' => $requiresDG,
            'requires_principal' => $requiresPrincipal,
            'seuil_dg' => $seuilDG,
            'seuil_principal' => $seuilPrincipal,
            'montant' => $montant,
            'message' => $message
        ]);
    }

}
