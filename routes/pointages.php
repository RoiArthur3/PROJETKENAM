<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PointageLogistiqueController;

// Routes pour les pointages logistiques
Route::prefix('pointages-engins')->name('pointages-engins.')->group(function () {
    Route::get('/', [PointageLogistiqueController::class, 'index'])->name('index');
    Route::get('/create', [PointageLogistiqueController::class, 'create'])->name('create');
    Route::post('/', [PointageLogistiqueController::class, 'store'])->name('store');
    Route::get('/dashboard', [PointageLogistiqueController::class, 'dashboard'])->name('dashboard');
    Route::get('/{id}', [PointageLogistiqueController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PointageLogistiqueController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PointageLogistiqueController::class, 'update'])->name('update');
    Route::post('/{id}/validate', [PointageLogistiqueController::class, 'validatePointage'])->name('validate');
    Route::post('/{id}/reject', [PointageLogistiqueController::class, 'reject'])->name('reject');
    
    // Statistiques
    Route::get('/operation/{operationId}/stats', [PointageLogistiqueController::class, 'statsByOperation'])->name('stats.operation');
    Route::get('/driver/{driverId}/stats', [PointageLogistiqueController::class, 'statsByDriver'])->name('stats.driver');
});
