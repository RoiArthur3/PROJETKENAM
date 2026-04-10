<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalysesController;

// Routes pour le module d'analyses
Route::prefix('analyses')->name('analyses.')->middleware(['auth'])->group(function () {
    // Tableau de bord des analyses
    Route::get('/dashboard', [AnalysesController::class, 'dashboard'])
        ->name('dashboard');

    // Exports
    Route::get('/exports/ca-entrepot', [AnalysesController::class, 'exportCaEntrepot'])
        ->name('exports.ca-entrepot');

    // Comparatifs
    Route::get('/comparatifs', [AnalysesController::class, 'comparatifs'])
        ->name('comparatifs');

    // Rapports
    Route::get('/rapports', [AnalysesController::class, 'rapports'])
        ->name('rapports');

    // Statistiques
    Route::get('/statistiques', [AnalysesController::class, 'statistiques'])
        ->name('statistiques');
});
