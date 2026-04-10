<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\PendingValidationController;

/*
|--------------------------------------------------------------------------
| Routes pour l'Historique des Validations - Solution Définitive
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Route principale pour l'historique des validations
    Route::get('/validations/history', function () {
        $operations = \App\Models\Operation::with(['client', 'type', 'operationalService', 'initiateur'])
            ->whereIn('statut_courant', ['approuvee', 'rejetee', 'terminee'])
            ->latest()
            ->paginate(15);

        return view('validations.history-simple', compact('operations'));
    })->name('validations.history');

    // Routes supplémentaires pour la navigation
    Route::get('/validations/dashboard', [OperationController::class, 'validationDashboard'])->name('validations.dashboard');

    Route::get('/validations/pending', [PendingValidationController::class, 'pending'])->name('validations.pending');
    Route::get('/validations/approved', [OperationController::class, 'approvedValidation'])->name('validations.approved');
    Route::get('/validations/rejected', [OperationController::class, 'rejectedValidation'])->name('validations.rejected');
});
