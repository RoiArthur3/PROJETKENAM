<?php

use App\Http\Controllers\PlanificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
 Routes pour le Module Contrôle & Audit - Planifications
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->name('controle-audit.')->group(function () {

    // Route principale pour le module
    Route::get('/', function() {
        return redirect()->route('controle-audit.planifications.index');
    })->name('index');

    // Routes principales des planifications
    Route::prefix('planifications')->name('planifications.')->group(function () {

        // Index - Liste des planifications
        Route::get('/', [PlanificationController::class, 'index'])
            ->name('index');

        // Create - Formulaire de création
        Route::get('/create', [PlanificationController::class, 'create'])
            ->name('create');

        // Store - Enregistrement
        Route::post('/', [PlanificationController::class, 'store'])
            ->name('store');

        // Show - Détails d'une planification
        Route::get('/{planification}', [PlanificationController::class, 'show'])
            ->name('show');

        // Edit - Formulaire d'édition
        Route::get('/{planification}/edit', [PlanificationController::class, 'edit'])
            ->name('edit');

        // Update - Mise à jour
        Route::put('/{planification}', [PlanificationController::class, 'update'])
            ->name('update');

        // Destroy - Suppression
        Route::delete('/{planification}', [PlanificationController::class, 'destroy'])
            ->name('destroy');

        // Duplicate - Duplication
        Route::post('/{planification}/duplicate', [PlanificationController::class, 'duplicate'])
            ->name('duplicate');

        // Change Statut - Changer statut
        Route::post('/{planification}/change-statut', [PlanificationController::class, 'changeStatut'])
            ->name('change-statut');
    });

    // Routes API pour les planifications
    Route::prefix('api/planifications')->name('api.planifications.')->group(function () {

        // Index API - Liste avec filtres
        Route::get('/', [PlanificationController::class, 'apiIndex'])
            ->name('index');

        // Get types de planification
        Route::get('/types', function() {
            $types = [
                'audit_interne' => 'Audit Interne',
                'audit_externe' => 'Audit Externe',
                'controle_qualite' => 'Contrôle Qualité',
                'inspection_securite' => 'Inspection Sécurité',
                'evaluation_risque' => 'Évaluation des Risques',
                'revue_processus' => 'Revue de Processus',
                'verification_conformite' => 'Vérification Conformité'
            ];

            return response()->json($types);
        })->name('types');

        // Get statistiques
        Route::get('/stats', function() {
            $stats = [
                'total_planifications' => \App\Models\Planification::count(),
                'planifiees' => \App\Models\Planification::where('statut', 'planifie')->count(),
                'en_cours' => \App\Models\Planification::where('statut', 'en_cours')->count(),
                'terminees' => \App\Models\Planification::where('statut', 'terminee')->count(),
                'annulees' => \App\Models\Planification::where('statut', 'annulee')->count(),
                'reportees' => \App\Models\Planification::where('statut', 'reportee')->count(),
                'a_venir' => \App\Models\Planification::upcoming()->count(),
                'en_retard' => \App\Models\Planification::whereDate('date_planification', '<', now())
                    ->whereIn('statut', ['planifie', 'en_cours'])->count(),
                'budget_total' => \App\Models\Planification::sum('budget_estime'),
                'budget_moyen' => \App\Models\Planification::avg('budget_estime'),
            ];

            return response()->json($stats);
        })->name('stats');

        // Export des planifications
        Route::get('/export', function(\Illuminate\Http\Request $request) {
            $validated = $request->validate([
                'date_debut' => 'nullable|date',
                'date_fin' => 'nullable|date|after_or_equal:date_debut',
                'statut' => 'nullable|in:planifie,en_cours,terminee,annulee,reportee',
                'service_id' => 'nullable|exists:services_operationnels,id'
            ]);

            $query = \App\Models\Planification::with(['service', 'createur']);

            if (!empty($validated['date_debut'])) {
                $query->whereDate('date_planification', '>=', $validated['date_debut']);
            }

            if (!empty($validated['date_fin'])) {
                $query->whereDate('date_planification', '<=', $validated['date_fin']);
            }

            if (!empty($validated['statut'])) {
                $query->where('statut', $validated['statut']);
            }

            if (!empty($validated['service_id'])) {
                $query->where('service_concerne_id', $validated['service_id']);
            }

            $planifications = $query->get();

            // Générer le CSV
            $filename = 'planifications_' . date('Y-m-d_H-i-s') . '.csv';
            $handle = fopen('php://output', 'w');

            // En-têtes
            fputcsv($handle, [
                'Référence', 'Titre', 'Type', 'Service', 'Date', 'Heures',
                'Lieu', 'Priorité', 'Statut', 'Budget', 'Createur', 'Participants'
            ]);

            // Données
            foreach ($planifications as $planification) {
                fputcsv($handle, [
                    'PLAN-' . str_pad($planification->id, 6, '0', STR_PAD_LEFT),
                    $planification->titre,
                    $planification->type_label,
                    $planification->service?->nom ?? 'N/A',
                    $planification->date_formatee,
                    $planification->plage_horaire,
                    $planification->lieu,
                    $planification->priorite_label,
                    $planification->statut_label,
                    $planification->budget_formate,
                    $planification->createur?->name ?? 'N/A',
                    count($planification->participants ?? [])
                ]);
            }

            fclose($handle);

            return response()->download($filename, $filename, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        })->name('export');
    });

    // Routes pour le tableau de bord du module
    Route::prefix('dashboard')->name('dashboard.')->group(function () {

        // Tableau de bord principal
        Route::get('/', function() {
            $stats = [
                'total_planifications' => \App\Models\Planification::count(),
                'ce_mois' => \App\Models\Planification::whereMonth('date_planification', now()->month)
                    ->whereYear('date_planification', now()->year)->count(),
                'en_cours' => \App\Models\Planification::where('statut', 'en_cours')->count(),
                'a_venir' => \App\Models\Planification::upcoming()->count(),
                'terminees_mois' => \App\Models\Planification::where('statut', 'terminee')
                    ->whereMonth('date_planification', now()->month)
                    ->whereYear('date_planification', now()->year)->count(),
            ];

            $recentes = \App\Models\Planification::with(['service', 'createur'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $parPriorite = \App\Models\Planification::selectRaw('priorite, COUNT(*) as count')
                ->groupBy('priorite')
                ->orderBy('count', 'desc')
                ->get();

            $parStatut = \App\Models\Planification::selectRaw('statut, COUNT(*) as count')
                ->groupBy('statut')
                ->orderBy('count', 'desc')
                ->get();

            return view('controle-audit.dashboard', compact(
                'stats',
                'recentes',
                'parPriorite',
                'parStatut'
            ));
        })->name('index');

        // Statistiques détaillées
        Route::get('/stats', function() {
            $stats = [
                'par_mois' => \App\Models\Planification::selectRaw('MONTH(date_planification) as mois, COUNT(*) as count')
                    ->groupBy('mois')
                    ->orderBy('mois')
                    ->take(12)
                    ->get(),
                'par_type' => \App\Models\Planification::selectRaw('type_planification, COUNT(*) as count')
                    ->groupBy('type_planification')
                    ->orderBy('count', 'desc')
                    ->get(),
                'par_service' => \App\Models\Planification::with('service')
                    ->get()
                    ->groupBy('service_concerne_id')
                    ->map(function ($group) {
                        $service = $group->first()->service;
                        return [
                            'service' => $service?->nom ?? 'Non spécifié',
                            'count' => $group->count()
                        ];
                    })
                    ->values()
                    ->sortByDesc('count')
                    ->take(10),
                'evolution' => \App\Models\Planification::selectRaw('DATE_FORMAT(date_planification, "%Y-%m") as periode, COUNT(*) as count')
                    ->groupBy('periode')
                    ->orderBy('periode')
                    ->take(6)
                    ->get()
            ];

            return response()->json($stats);
        })->name('stats');
    });
});

/*
|--------------------------------------------------------------------------
 Middleware pour le module Contrôle & Audit
|--------------------------------------------------------------------------
*/

// Middleware pour vérifier les permissions spécifiques au module
Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('check.controle.audit.access')->group(function () {
        // Routes qui nécessitent une vérification d'accès au module
        Route::prefix('controle-audit')->group(function () {
            Route::get('/planifications', function() {
                return redirect()->route('controle-audit.planifications.index');
            });

            Route::get('/dashboard', function() {
                return redirect()->route('controle-audit.dashboard.index');
            });
        });
    });
});
