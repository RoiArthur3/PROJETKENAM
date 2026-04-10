<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Comptabilite\Tresorerie\ApprovisionnementCaisseResourceController;
use App\Http\Controllers\Comptabilite\Tresorerie\CaisseResourceController;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
|
| Routes API pour la version 1 de l'application.
| Toutes les routes sont préfixées par /api/v1 et protégées par authentification.
|
*/

Route::prefix('v1')->middleware(['auth:api'])->group(function () {
    // ===== TRÉSORERIE =====
    Route::prefix('tresorerie')->middleware(['check.admin:treasury'])->group(function () {
        // Gestion des caisses
        Route::apiResource('caisses', CaisseResourceController::class);

        // Routes personnalisées pour les caisses
        Route::prefix('caisses/{caisse}')->group(function () {
            Route::get('historique', [CaisseResourceController::class, 'historique'])
                ->name('caisses.historique');
            Route::post('update-solde', [CaisseResourceController::class, 'updateSolde'])
                ->name('caisses.update-solde');
        });

        // Gestion des approvisionnements
        Route::apiResource('approvisionnements', ApprovisionnementCaisseResourceController::class);

        // Routes personnalisées pour les approvisionnements
        Route::prefix('approvisionnements/{approvisionnement}')->group(function () {
            Route::post('valider', [ApprovisionnementCaisseResourceController::class, 'valider'])
                ->name('approvisionnements.valider');
            Route::post('rejeter', [ApprovisionnementCaisseResourceController::class, 'rejeter'])
                ->name('approvisionnements.rejeter');
        });
    });

    // ===== AUTRES MODULES =====
    // Ajouter ici les autres groupes de routes pour les différents modules
    // Exemple : RH, Logistique, etc.
});
