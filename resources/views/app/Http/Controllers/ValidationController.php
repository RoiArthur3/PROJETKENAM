<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\OperationService;
use App\Models\OperationStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use App\Notifications\OperationNeedsValidation;

class ValidationController extends Controller
{
    public function review(Request $request, Operation $operation, int $step)
    {
        $serviceStep = OperationService::where('operation_id', $operation->id)
            ->where('ordre', $step)
            ->firstOrFail();

        // If link opened and step is waiting, mark as pending to indicate active review
        if ($serviceStep->statut === 'waiting') {
            $serviceStep->update(['statut' => 'pending']);
        }

        $steps = OperationService::where('operation_id', $operation->id)
            ->orderBy('ordre')
            ->get();

        return view('operations.validation', compact('operation', 'serviceStep', 'steps'));
    }

    public function approve(Request $request, Operation $operation, int $step)
    {
        // Validation des champs requis
        $request->validate([
            'validator' => 'required|string|max:255',
            'commentaire' => 'required|string|min:5|max:1000',
        ], [
            'validator.required' => 'Le nom du validateur est requis.',
            'commentaire.required' => 'Le commentaire est obligatoire pour valider.',
            'commentaire.min' => 'Le commentaire doit contenir au moins 5 caractères.',
            'commentaire.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
        ]);

        $serviceStep = OperationService::where('operation_id', $operation->id)
            ->where('ordre', $step)
            ->firstOrFail();

        // Vérification de la logique de validation à 3 niveaux
        // Le destinataire principal (niveau 3) valide en dernier
        if ($step < 3) {
            // Vérifier que les niveaux supérieurs n'ont pas encore validé
            $higherSteps = OperationService::where('operation_id', $operation->id)
                ->where('ordre', '>', $step)
                ->get();

            foreach ($higherSteps as $higherStep) {
                if ($higherStep->statut === 'approved') {
                    return redirect()->back()
                        ->with('error', 'Le niveau supérieur (' . $higherStep->ordre . ') a déjà été validé. Vous ne pouvez plus valider ce niveau.')
                        ->withInput();
                }
            }
        } else {
            // Pour le niveau 3 (destinataire principal), vérifier que les niveaux 1 et 2 sont validés
            $previousSteps = OperationService::where('operation_id', $operation->id)
                ->where('ordre', '<', 3)
                ->get();

            foreach ($previousSteps as $previousStep) {
                if ($previousStep->statut !== 'approved') {
                    return redirect()->back()
                        ->with('error', 'Le destinataire principal (niveau 3) ne peut valider que lorsque les niveaux 1 et 2 ont été approuvés.')
                        ->withInput();
                }
            }
        }

        $serviceStep->update([
            'statut' => 'approved',
            'valide_par' => $request->input('validator'),
            'valide_le' => Carbon::now(),
            'commentaire' => $request->input('commentaire')
        ]);

        // Move next step to pending if exists
        $next = OperationService::where('operation_id', $operation->id)
            ->where('ordre', '>', $step)
            ->orderBy('ordre')
            ->first();

        if ($next) {
            $next->update(['statut' => 'pending']);
            $operation->update(['statut_courant' => 'pending_validation']);

            // Send notification to next validator with signed URL
            $url = URL::temporarySignedRoute(
                'validations.operations.review',
                now()->addDays(7),
                ['operation' => $operation->id, 'step' => $next->ordre]
            );
            Notification::route('mail', $next->service_email)
                ->notify(new OperationNeedsValidation($operation, $next->ordre, $url, $next->service_name, $next->destinataire_nom));
        } else {
            // All validations approved -> set operation to in_progress
            $operation->update(['statut_courant' => 'in_progress']);
        }

        OperationStatusLog::create([
            'operation_id' => $operation->id,
            'from_status' => 'pending_validation',
            'to_status' => $operation->statut_courant,
            'user_name' => $request->input('validator', 'Validateur'),
            'commentaire' => 'Étape S'.$step.' approuvée'
        ]);

        return redirect()->route('operations.show', $operation->id)
            ->with('status', 'Étape S'.$step.' approuvée.');
    }

    public function reject(Request $request, Operation $operation, int $step)
    {
        // Validation des champs requis
        $request->validate([
            'validator' => 'required|string|max:255',
            'commentaire' => 'required|string|min:5|max:1000',
        ], [
            'validator.required' => 'Le nom du validateur est requis.',
            'commentaire.required' => 'Le commentaire est obligatoire pour rejeter.',
            'commentaire.min' => 'Le commentaire doit contenir au moins 5 caractères.',
            'commentaire.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
        ]);

        $serviceStep = OperationService::where('operation_id', $operation->id)
            ->where('ordre', $step)
            ->firstOrFail();

        $serviceStep->update([
            'statut' => 'rejected',
            'valide_par' => $request->input('validator'),
            'valide_le' => Carbon::now(),
            'commentaire' => $request->input('commentaire')
        ]);

        $operation->update(['statut_courant' => 'rejected']);

        OperationStatusLog::create([
            'operation_id' => $operation->id,
            'from_status' => 'pending_validation',
            'to_status' => 'rejected',
            'user_name' => $request->input('validator', 'Validateur'),
            'commentaire' => 'Étape S'.$step.' rejetée'
        ]);

        // Notifier le demandeur
        if ($operation->demandeur_email) {
            try {
                \Illuminate\Support\Facades\Mail::to($operation->demandeur_email)->send(
                    new \App\Mail\OperationRejectedMail($operation, $request->input('commentaire', 'Non spécifié'))
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erreur envoi mail rejet: ' . $e->getMessage());
            }
        }

        return redirect()->route('operations.show', $operation->id)
            ->with('status', 'Étape S'.$step.' rejetée.');
    }
}
