<?php

use App\Http\Controllers\Juridique\ContratController;
use App\Http\Controllers\Juridique\JuridiqueDashboardController;
use App\Http\Controllers\Juridique\DocumentController;
use App\Http\Controllers\Juridique\EcheanceController;
use App\Http\Controllers\Juridique\FinancementController;
use App\Http\Controllers\Juridique\OffreBancaireController;
use Illuminate\Support\Facades\Route;

Route::prefix('juridique')->name('juridique.')->middleware(['auth', 'module:juridique'])->group(function () {
    Route::get('/', [JuridiqueDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [JuridiqueDashboardController::class, 'index'])->name('dashboard.main');

    Route::resource('contrats', ContratController::class);

    // Routes supplémentaires pour les contrats
    Route::get('contrats/{contrat}/duplicate', [ContratController::class, 'duplicate'])->name('contrats.duplicate');
    Route::post('contrats/{contrat}/creer-financement', [ContratController::class, 'creerFinancement'])->name('contrats.creer-financement');
    Route::get('contrats/export', [ContratController::class, 'export'])->name('contrats.export');

    Route::resource('documents', DocumentController::class);
    Route::resource('financements', FinancementController::class);
    Route::post('offres/{offre}/generate-echeancier', [OffreBancaireController::class, 'generateEcheancier'])->name('offres.generate-echeancier');
    Route::resource('offres', OffreBancaireController::class)->parameters(['offres' => 'offre']);
    Route::resource('echeances', EcheanceController::class);

    // Routes API pour les interactions
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('stats', [JuridiqueDashboardController::class, 'getStats'])->name('stats');
        Route::get('interactions', [JuridiqueDashboardController::class, 'getInteractions'])->name('interactions');
        Route::post('sync-rh', [JuridiqueDashboardController::class, 'syncRH'])->name('sync-rh');
    });
});
