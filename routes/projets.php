<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjetsDashboardController;

// Routes Projets
Route::prefix('projets')->name('projets.')->group(function () {
    // Route de test
    Route::get('/test', function() {
        return 'Route projets fonctionne - ' . date('Y-m-d H:i:s');
    })->name('test');
    // Routes principales
    Route::get('/', [ProjetsDashboardController::class, 'list'])->name('index');
    Route::get('/list', [ProjetsDashboardController::class, 'list'])->name('list');
    Route::get('/dashboard', [ProjetsDashboardController::class, 'index'])->name('dashboard');

    // Routes CRUD
    Route::get('/create', [ProjetsDashboardController::class, 'create'])->name('create');
    Route::post('/', [ProjetsDashboardController::class, 'store'])->name('store');
    Route::get('/{projet}', [ProjetsDashboardController::class, 'show'])->name('show');
    Route::get('/{projet}/edit', [ProjetsDashboardController::class, 'edit'])->name('edit');
    Route::put('/{projet}', [ProjetsDashboardController::class, 'update'])->name('update');
    Route::delete('/{projet}', [ProjetsDashboardController::class, 'destroy'])->name('destroy');

    // Routes spécifiques
    Route::get('/{projet}/avancement', [ProjetsDashboardController::class, 'avancement'])->name('avancement');
    Route::get('/{projet}/affectations', [ProjetsDashboardController::class, 'affectations'])->name('affectations');
    Route::get('/cloture', [ProjetsDashboardController::class, 'cloture'])->name('cloture');
    Route::get('/{projet}/cloture', [ProjetsDashboardController::class, 'clotureProjet'])->name('cloture.projet');
    Route::get('/rapports', [ProjetsDashboardController::class, 'reports'])->name('rapports');
    Route::get('/{projet}/rapport-financier', [ProjetsDashboardController::class, 'rapportFinancier'])->name('rapport-financier');
    Route::get('/{projet}/rapport-financier/facture', [ProjetsDashboardController::class, 'genererFacture'])->name('rapport-financier.facture');

    // API routes
    Route::prefix('api')->group(function () {
        Route::get('/{projet}/engins', [ProjetsDashboardController::class, 'getEnginsByProjet'])->name('api.engins');
        Route::get('/{projet}/rapport-financier/data', [ProjetsDashboardController::class, 'rapportFinancierData'])->name('api.rapport-financier.data');
    });

});
