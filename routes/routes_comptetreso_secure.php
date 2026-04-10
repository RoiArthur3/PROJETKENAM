<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompteTresoController;

/*
|--------------------------------------------------------------------------
| Routes Compte Treso - Isolées et Sécurisées
|--------------------------------------------------------------------------
|
| Ces routes sont dédiées au profil Compte Treso et sont complètement
| isolées du système existant pour éviter toute destabilisation.
|
*/

Route::middleware(['auth'])->prefix('comptetreso')->name('comptetreso.')->group(function () {
    
    // Routes accessibles uniquement pour le rôle comptetreso
    Route::middleware(['comptetreso'])->group(function () {
        
        // Dashboard principal
        Route::get('/dashboard', [CompteTresoController::class, 'dashboard'])
            ->name('dashboard');
            
        // Liste des opérations à payer
        Route::get('/operations', [CompteTresoController::class, 'operationsToPay'])
            ->name('operations');
            
        // Détails d'une opération
        Route::get('/operations/{id}', [CompteTresoController::class, 'show'])
            ->name('operations.show');
            
        // Marquer comme payée (POST pour sécurité)
        Route::post('/operations/{id}/mark-paid', [CompteTresoController::class, 'markAsPaid'])
            ->name('operations.mark-paid');
    });
    
    // Route de secours si accès non autorisé
    Route::get('/unauthorized', function() {
        return view('comptetreso.unauthorized');
    })->name('unauthorized');
});
