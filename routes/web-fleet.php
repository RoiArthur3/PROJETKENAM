<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParcDashboardController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\ParcAssuranceController;
use App\Http\Controllers\ReparationController;

Route::middleware(['auth'])->prefix('fleet')->name('fleet.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ParcDashboardController::class, 'index'])->name('dashboard');

    // Engins
    Route::get('/engins', [VehiculeController::class, 'index'])->name('engins');
    Route::get('/engins/create', [VehiculeController::class, 'create'])->name('engins.create');
    Route::post('/engins', [VehiculeController::class, 'store'])->name('engins.store');
    Route::get('/engins/{vehicule}', [VehiculeController::class, 'show'])->name('engins.show');
    Route::get('/engins/{vehicule}/edit', [VehiculeController::class, 'edit'])->name('engins.edit');
    Route::put('/engins/{vehicule}', [VehiculeController::class, 'update'])->name('engins.update');

    // Missions
    Route::get('/missions', [\App\Http\Controllers\VehicleMissionController::class, 'index'])->name('missions.index');
    Route::get('/missions/create', [\App\Http\Controllers\VehicleMissionController::class, 'create'])->name('missions.create');
    Route::post('/missions', [\App\Http\Controllers\VehicleMissionController::class, 'store'])->name('missions.store');
    Route::get('/missions/export', [\App\Http\Controllers\VehicleMissionController::class, 'export'])->name('missions.export');

    // Affectations
    Route::get('/affectations/export', [\App\Http\Controllers\VehicleAssignmentController::class, 'export'])->name('affectations.export');
    Route::resource('affectations', \App\Http\Controllers\VehicleAssignmentController::class);

    // Maintenance & Réparations
    Route::get('/maintenance', [ReparationController::class, 'index'])->name('maintenance');
    Route::get('/maintenance/create', [ReparationController::class, 'create'])->name('maintenance.create');

    // Carburant
    Route::get('/carburant', [ParcDashboardController::class, 'carburant'])->name('carburant');
    Route::post('/carburant', [ParcDashboardController::class, 'storeCarburant'])->name('carburant.store');

    // Rapports
    Route::get('/rapports', [ParcDashboardController::class, 'index'])->name('rapports');

    // Checking (Vérifications)
    Route::prefix('checking')->name('checking.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Checking\CheckingController::class, 'index'])->name('index');
        Route::get('/by-type', [\App\Http\Controllers\Checking\CheckingController::class, 'byType'])->name('by-type');
        Route::get('/create', [\App\Http\Controllers\Checking\CheckingController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Checking\CheckingController::class, 'store'])->name('store');
        Route::get('/{checking}', [\App\Http\Controllers\Checking\CheckingController::class, 'show'])->name('show');
        Route::get('/{checking}/edit', [\App\Http\Controllers\Checking\CheckingController::class, 'edit'])->name('edit');
        Route::put('/{checking}', [\App\Http\Controllers\Checking\CheckingController::class, 'update'])->name('update');
        Route::delete('/{checking}', [\App\Http\Controllers\Checking\CheckingController::class, 'destroy'])->name('destroy');
        Route::get('/search/vehicle', [\App\Http\Controllers\Checking\CheckingController::class, 'vehicleSearch'])->name('vehicle-search');
    });
});

// Alias pour compatibilité avec certains liens
Route::middleware(['auth', 'module:fleet'])->prefix('parc')->name('parc.')->group(function () {
    Route::get('/dashboard', [ParcDashboardController::class, 'index'])->name('dashboard');
    Route::get('/carburant', [ParcDashboardController::class, 'carburant'])->name('carburant');

    // Engins
    Route::get('/engins', [VehiculeController::class, 'index'])->name('engins');
    Route::get('/engins/create', [VehiculeController::class, 'create'])->name('engins.create');
    Route::post('/engins', [VehiculeController::class, 'store'])->name('engins.store');
    Route::get('/engins/{vehicule}', [VehiculeController::class, 'show'])->name('engins.show');
    Route::get('/engins/{vehicule}/edit', [VehiculeController::class, 'edit'])->name('engins.edit');
    Route::put('/engins/{vehicule}', [VehiculeController::class, 'update'])->name('engins.update');
    Route::delete('/engins/{vehicule}', [VehiculeController::class, 'destroy'])->name('engins.destroy');

    // Missions
    Route::get('/missions', [\App\Http\Controllers\VehicleMissionController::class, 'index'])->name('missions.index');
    Route::get('/missions/create', [\App\Http\Controllers\VehicleMissionController::class, 'create'])->name('missions.create');
    Route::post('/missions', [\App\Http\Controllers\VehicleMissionController::class, 'store'])->name('missions.store');
    Route::get('/missions/export', [\App\Http\Controllers\VehicleMissionController::class, 'export'])->name('missions.export');

    // Affectations
    Route::get('/affectations/export', [\App\Http\Controllers\VehicleAssignmentController::class, 'export'])->name('affectations.export');
    Route::resource('affectations', \App\Http\Controllers\VehicleAssignmentController::class);

    // Assurances
    Route::get('/assurances', [ParcAssuranceController::class, 'index'])->name('assurances.index');
    Route::get('/assurances/create', [ParcAssuranceController::class, 'create'])->name('assurances.create');
    Route::post('/assurances', [ParcAssuranceController::class, 'store'])->name('assurances.store');
    Route::get('/assurances/{assurance}/edit', [ParcAssuranceController::class, 'edit'])->name('assurances.edit');
    Route::put('/assurances/{assurance}', [ParcAssuranceController::class, 'update'])->name('assurances.update');
    Route::delete('/assurances/{assurance}', [ParcAssuranceController::class, 'destroy'])->name('assurances.destroy');
});
