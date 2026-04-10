<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlertController;

/*
|--------------------------------------------------------------------------
| Web Routes - Alertes
|--------------------------------------------------------------------------
|
| Routes pour la gestion des alertes véhicules
|
*/

Route::middleware(['auth', 'verified'])->prefix('alerts')->name('alerts.')->group(function () {
    Route::get('/', [AlertController::class, 'index'])->name('index');
    Route::get('/create', [AlertController::class, 'create'])->name('create');
    Route::post('/', [AlertController::class, 'store'])->name('store');
    Route::get('/{alert}', [AlertController::class, 'show'])->name('show');
    Route::put('/{alert}', [AlertController::class, 'update'])->name('update');
    Route::delete('/{alert}', [AlertController::class, 'destroy'])->name('destroy');

    // Routes API pour les alertes
    Route::prefix('api')->name('alerts.api.')->group(function () {
        Route::get('/stats', [AlertController::class, 'apiStats'])->name('stats');
        Route::put('/{alert}/marquer-traite', [AlertController::class, 'markAsProcessed'])->name('mark.processed');
        Route::get('/active', [AlertController::class, 'activeAlerts'])->name('active');
    });
});

Route::middleware(['auth', 'verified'])->prefix('api')->name('api.')->group(function () {
    Route::get('/alertes', [AlertController::class, 'apiIndex'])->name('alertes');
});
