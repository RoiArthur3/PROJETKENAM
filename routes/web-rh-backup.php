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

    // Inclure les routes de pointages
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
        Route::get('/', [App\Http\Controllers\PersonnelPaieController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\PersonnelPaieController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\PersonnelPaieController::class, 'store'])->name('store');
        Route::get('/{paie}', [App\Http\Controllers\PersonnelPaieController::class, 'show'])->name('show');
        Route::get('/{paie}/edit', [App\Http\Controllers\PersonnelPaieController::class, 'edit'])->name('edit');
        Route::put('/{paie}', [App\Http\Controllers\PersonnelPaieController::class, 'update'])->name('update');
        Route::delete('/{paie}', [App\Http\Controllers\PersonnelPaieController::class, 'destroy'])->name('destroy');
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
