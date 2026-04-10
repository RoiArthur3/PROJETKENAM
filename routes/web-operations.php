<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperationController;

/*
|--------------------------------------------------------------------------
| Module Opérations
|--------------------------------------------------------------------------
*/

Route::prefix('operations')->name('operations.')->middleware(['auth', 'module:operations'])->group(function () {

    // Dashboard des opérations
    Route::get('/dashboard', [OperationController::class, 'dashboard'])->name('dashboard');

    // Calendrier des opérations
    Route::get('/calendar', [OperationController::class, 'calendar'])->name('calendar');
    Route::get('/calendar/events', [OperationController::class, 'calendarEvents'])->name('calendar.events');

    // Liste des opérations
    Route::get('/', [OperationController::class, 'index'])->name('index');

    // Création d'une opération
    Route::get('/create', [OperationController::class, 'create'])->name('create');
    Route::post('/', [OperationController::class, 'store'])->name('store');

    // Validation des opérations (statiques)
    Route::get('/valider', [OperationController::class, 'pendingValidation'])->name('valider');
    Route::get('/validation/dashboard', [OperationController::class, 'validationDashboard'])->name('validation.dashboard');

    // Validation d'une opération (doit être AVANT show pour éviter les conflits)
    Route::match(['get', 'post'], '/{operationId}/validate', [OperationController::class, 'validateOperation'])->name('validate');

    // Affichage et édition d'une opération (variables)
    Route::get('/{operationId}', [OperationController::class, 'show'])
        ->whereNumber('operationId')
        ->name('show');
    Route::get('/{operationId}/edit', [OperationController::class, 'edit'])
        ->whereNumber('operationId')
        ->name('edit');
    Route::put('/{operationId}', [OperationController::class, 'update'])
        ->whereNumber('operationId')
        ->name('update');
    Route::delete('/{operationId}', [OperationController::class, 'destroy'])
        ->whereNumber('operationId')
        ->name('destroy');

    // Routes spécifiques avec paramètres différents
    Route::get('/{operationId}/download-pdf', [OperationController::class, 'downloadPDF'])->name('download-pdf');
    Route::post('/{operationId}/approve/{step}', [OperationController::class, 'approveOperation'])->name('approve');
    Route::post('/{operationId}/reject/{step}', [OperationController::class, 'rejectOperation'])->name('reject');
    Route::get('/{operationId}/tracking', [OperationController::class, 'tracking'])->name('tracking');
    Route::post('/{operationId}/upload', [OperationController::class, 'uploadFiles'])->name('upload');
    Route::post('/{operationId}/send-execution-email', [OperationController::class, 'sendExecutionEmail'])->name('send-execution-email');
    Route::get('/{operationId}/bon-pour-accord', [OperationController::class, 'bonPourAccord'])->name('bon-pour-accord');
    Route::post('/{operationId}/mark-paid', [OperationController::class, 'markAsPaid'])->name('mark-paid');

    // ── DOCUMENTS : Téléchargement spécifique
    Route::get('/files/{file}/download-as-pdf', [OperationController::class, 'downloadFileAsPDF'])->name('files.download-as-pdf');
});

