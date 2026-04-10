<?php

use App\Http\Controllers\PendingValidationController;
use App\Http\Controllers\OperationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
 Routes pour la Validation des Opérations
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Routes pour le suivi des validations
    Route::prefix('validations')->name('validations.')->group(function () {
        // Tableau de bord des validations
        Route::get('/', function() {
            return view('validations.index');
        })->name('index');

            // Alias pour /to-pay qui redirige vers /payer
            Route::get('/to-pay', function() {
                return redirect()->route('validations.to-pay');
            });

        // Validations en attente
        Route::get('/pending', [PendingValidationController::class, 'pending'])
            ->name('pending')
            ->middleware('auth');

        // Validations approuvées
        Route::get('/approved', [PendingValidationController::class, 'approved'])->name('approved');

        // Validations rejetées
        Route::get('/rejected', [PendingValidationController::class, 'rejected'])->name('rejected');

        // Historique des validations
        Route::get('/history', [PendingValidationController::class, 'history'])->name('history');

        // Operations payées (suivi paiement)
        Route::get('/operations-payees', [OperationController::class, 'paidOperations'])->name('paid');

        // ── PAGE COMPTABILITÉ : BON POUR ACCORD → sélection de la caisse
        Route::get('/payer', [OperationController::class, 'toPayOperations'])->name('to-pay');

        // ── PAGE CAISSE : BON POUR EXÉCUTION (les opérations désignées à UNE caisse)
        Route::get('/caisse-execution', [OperationController::class, 'caisseExecution'])->name('caisse-execution');

        // ── ACTION TRÉSORERIE : Émettre Bon Pour Exécution
        Route::post('/operations/{operation}/emit-execution', [OperationController::class, 'emitBonPourExecution'])->name('emit-execution');

        // ── ACTION CAISSE : Confirmer le paiement
        Route::post('/operations/{operation}/mark-paid', [OperationController::class, 'markAsPaid'])->name('operations.markPaid');

        // Dashboard de validation
        Route::get('/dashboard', function() {
            $stats = [
                'total_validations' => \App\Models\Operation::count(),
                'en_attente' => \App\Models\Operation::where('statut_courant', 'en_attente')
                    ->orWhere('statut_courant', 'pending_validation')->count(),
                'en_validation' => \App\Models\Operation::where('statut_courant', 'en_validation')->count(),
                'approuvees' => \App\Models\Operation::where('statut_courant', 'approuvee')->count(),
                'rejetees' => \App\Models\Operation::where('statut_courant', 'rejetee')->count(),
            ];

            // La vue attend $validations pour afficher les détails par service
            $validations = \App\Models\Operation::with(['initiateur', 'operationalService'])
                ->whereIn('statut_courant', ['en_attente', 'en_validation', 'pending_validation'])
                ->orderBy('updated_at', 'desc')
                ->get();

            return view('validations.dashboard', compact('stats', 'validations'));
        })->name('dashboard')
        ->middleware('auth');

        // Export des validations
        Route::get('/export', function(\Illuminate\Http\Request $request) {
            $validated = $request->validate([
                'date_debut' => 'nullable|date',
                'date_fin' => 'nullable|date|after_or_equal:date_debut',
                'statut_courant' => 'nullable|in:en_attente,en_validation,approuvee,rejetee'
            ]);

            $query = \App\Models\Operation::with(['initiateur']);

            if (!empty($validated['date_debut'])) {
                $query->whereDate('created_at', '>=', $validated['date_debut']);
            }

            if (!empty($validated['date_fin'])) {
                $query->whereDate('created_at', '<=', $validated['date_fin']);
            }

            if (!empty($validated['statut_courant'])) {
                $query->where('statut_courant', $validated['statut_courant']);
            }

            $operations = $query->get();

            // Générer le CSV
            $filename = 'validations_' . date('Y-m-d_H-i-s') . '.csv';
            $handle = fopen('php://output', 'w');

            // En-têtes
            fputcsv($handle, [
                'Référence', 'Date', 'Initiateur', 'Statut',
                'Services Notifiés', 'Date Validation', 'Commentaire'
            ]);

            // Données
            foreach ($operations as $operation) {
                fputcsv($handle, [
                    $operation->reference ?? 'N/A',
                    $operation->created_at->format('d/m/Y H:i'),
                    $operation->initiateur?->name ?? 'N/A',
                    $operation->statut_courant,
                    $operation->services->pluck('nom')->implode(', '),
                    $operation->date_validation?->format('d/m/Y H:i') ?? 'N/A',
                    $operation->commentaire_validation ?? ''
                ]);
            }

            fclose($handle);

            return response()->stream(
                function () use ($handle) {
                    fpassthru($handle);
                },
                200,
                [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ]
            );
        })->name('export')
        ->middleware('auth');

        // Détails d'une validation
        Route::get('/{operation}', [PendingValidationController::class, 'show'])->name('show')
        ->middleware('auth');
    });

    // Routes API pour la validation
    Route::prefix('api/validation')->name('api.validation.')->group(function () {
        // Vérifier en temps réel le montant
        Route::post('/check-amount', function(\Illuminate\Http\Request $request) {
            $montant = $request->input('montant');
            $seuil = 1000000;
            $requiresValidation = $montant >= $seuil;

            return response()->json([
                'requires_validation' => $requiresValidation,
                'seuil' => $seuil,
                'montant' => $montant,
                'difference' => $requiresValidation ? $montant - $seuil : $seuil - $montant,
                'message' => $requiresValidation
                    ? 'Ce montant nécessite une validation du DG et Manager'
                    : 'Ce montant ne nécessite pas de validation spéciale',
                'level' => $requiresValidation ? 'high' : 'normal',
                'color' => $requiresValidation ? '#dc3545' : '#28a745',
                'icon' => $requiresValidation ? 'fas fa-exclamation-triangle' : 'fas fa-check-circle'
            ]);
        })->name('check-amount');

        // Récupérer les services disponibles
        Route::get('/services', function() {
            $services = \App\Models\ServiceOperationnel::actif()->ordre()->get()->map(function (\App\Models\ServiceOperationnel $service) {
                return [
                    'id' => $service->id,
                    'nom' => $service->nom,
                    'code' => $service->code,
                    'email' => $service->email,
                    'icone' => $service->icone,
                    'couleur' => $service->couleur,
                    'responsable' => $service->responsable,
                    'actif' => $service->actif,
                    'peut_recevoir' => $service->peutRecevoirRequetes()
                ];
            });

            return response()->json($services);
        })->name('services');
    });
});

/*
|--------------------------------------------------------------------------
 Middleware pour la validation automatique
|--------------------------------------------------------------------------
*/

// Middleware pour vérifier automatiquement les montants élevés
Route::middleware(['auth'])->group(function () {
    // Routes qui nécessitent une vérification de montant
    // Les routes operations.store et operations.update sont déjà définies dans web.php
});
