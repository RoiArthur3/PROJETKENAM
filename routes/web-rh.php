<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes RH - Ressources Humaines
|--------------------------------------------------------------------------
*/

Route::prefix('rh')->name('rh.')->middleware(['auth', 'module:rh'])->group(function () {

    // Inclure les routes de personnel
    require __DIR__.'/personnel.php';

    // Pointage RH manuel du personnel
    Route::prefix('pointages')->name('pointages.')->group(function () {
        Route::get('/', [App\Http\Controllers\RHController::class, 'pointages'])->name('index');
        Route::get('/create', [App\Http\Controllers\RHController::class, 'createPointage'])->name('create');
        Route::post('/', [App\Http\Controllers\RHController::class, 'storePointage'])->name('store');
        Route::get('/mass', [App\Http\Controllers\RHController::class, 'massPointages'])->name('mass');
        Route::post('/mass', [App\Http\Controllers\RHController::class, 'storeMassPointages'])->name('mass.store');
        Route::get('/history', [App\Http\Controllers\RHController::class, 'historyPointages'])->name('history');
    });

    // Pointage facial Hikvision
    Route::prefix('facial-pointage')->name('facial-pointage.')->group(function () {
        Route::get('/', [App\Http\Controllers\FacialPointageController::class, 'dashboard'])->name('index');
        Route::get('/dashboard', [App\Http\Controllers\FacialPointageController::class, 'dashboard'])->name('dashboard');
    });

    // Terminaux faciaux Hikvision
    Route::prefix('facial-devices')->name('facial-devices.')->group(function () {
        Route::get('/', [App\Http\Controllers\FacialDeviceController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\FacialDeviceController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\FacialDeviceController::class, 'store'])->name('store');
        Route::delete('/{device}', [App\Http\Controllers\FacialDeviceController::class, 'destroy'])->name('destroy');
        Route::post('/{device}/regenerate-token', [App\Http\Controllers\FacialDeviceController::class, 'regenerateToken'])->name('regenerate-token');
        Route::post('/{device}/toggle-status', [App\Http\Controllers\FacialDeviceController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Routes pour la synchronisation caméra
    Route::prefix('camera-sync')->name('camera-sync.')->group(function () {
        Route::get('/', [App\Http\Controllers\RH\CameraSyncController::class, 'index'])->name('index');
        Route::get('/create/{id}', [App\Http\Controllers\RH\CameraSyncController::class, 'create'])->name('create');
        Route::post('/store/{id}', [App\Http\Controllers\RH\CameraSyncController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [App\Http\Controllers\RH\CameraSyncController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [App\Http\Controllers\RH\CameraSyncController::class, 'update'])->name('update');
        Route::post('/sync/{id}', [App\Http\Controllers\RH\CameraSyncController::class, 'syncSingle'])->name('sync');
        Route::post('/bulk-sync', [App\Http\Controllers\RH\CameraSyncController::class, 'bulkSync'])->name('bulk-sync');
        Route::delete('/destroy/{id}', [App\Http\Controllers\RH\CameraSyncController::class, 'destroy'])->name('destroy');
    });

    // Inclure les routes de pointages engins (ancien flux logistique, séparé du RH manuel)
    require __DIR__.'/pointages.php';

    // Routes pour les congés
    Route::prefix('conges')->name('conges.')->group(function () {
        Route::get('/', [App\Http\Controllers\PersonnelCongeController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\PersonnelCongeController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\PersonnelCongeController::class, 'store'])->name('store');
        Route::get('/{conge}', [App\Http\Controllers\PersonnelCongeController::class, 'show'])->name('show');
        Route::get('/{conge}/edit', [App\Http\Controllers\PersonnelCongeController::class, 'edit'])->name('edit');
        Route::put('/{conge}', [App\Http\Controllers\PersonnelCongeController::class, 'update'])->name('update');
        Route::delete('/{conge}', [App\Http\Controllers\PersonnelCongeController::class, 'destroy'])->name('destroy');
    });

    // Routes pour la paie
    Route::prefix('paie')->name('paie.')->group(function () {
        Route::get('/', [App\Http\Controllers\RHController::class, 'paieIndex'])->name('index');
        Route::get('/create', [App\Http\Controllers\RHController::class, 'createPaie'])->name('create');
        Route::post('/', [App\Http\Controllers\RHController::class, 'storePaie'])->name('store');
        Route::get('/generate', [App\Http\Controllers\RHController::class, 'generateAllPaie'])->name('generate');
        Route::get('/export', [App\Http\Controllers\RHController::class, 'exportPaie'])->name('export');
        Route::get('/{paie}/pdf', [App\Http\Controllers\RHController::class, 'pdfPaie'])->name('pdf')->whereNumber('paie');
        Route::get('/{paie}', [App\Http\Controllers\RHController::class, 'showPaie'])->name('show')->whereNumber('paie');
        Route::get('/{paie}/edit', [App\Http\Controllers\RHController::class, 'editPaie'])->name('edit')->whereNumber('paie');
        Route::put('/{paie}', [App\Http\Controllers\RHController::class, 'updatePaie'])->name('update')->whereNumber('paie');
        Route::delete('/{paie}', [App\Http\Controllers\RHController::class, 'deletePaie'])->name('destroy')->whereNumber('paie');
    });

    // Routes pour les documents
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [App\Http\Controllers\PersonnelDocumentController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\PersonnelDocumentController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\PersonnelDocumentController::class, 'store'])->name('store');
        Route::get('/{document}', [App\Http\Controllers\PersonnelDocumentController::class, 'show'])->name('show');
        Route::get('/{document}/edit', [App\Http\Controllers\PersonnelDocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [App\Http\Controllers\PersonnelDocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [App\Http\Controllers\PersonnelDocumentController::class, 'destroy'])->name('destroy');
    });
});

// Route de test sans middleware pour diagnostiquer
Route::get('/test-camera-sync', [App\Http\Controllers\RH\CameraSyncController::class, 'index']);
Route::get('/camera-sync-test', [App\Http\Controllers\RH\CameraSyncController::class, 'index']);
Route::get('/test-simple', [App\Http\Controllers\TestCameraController::class, 'index']);
