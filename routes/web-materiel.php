<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Materiel\MaterielController;
use App\Http\Controllers\Materiel\MaintenanceController;
use App\Http\Controllers\Materiel\CarburantController;
use App\Http\Controllers\Materiel\VisiteTechniqueController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\VehicleCostControlController;
use App\Http\Controllers\ChrononomiqueCostControlController;
use App\Http\Controllers\CostControlDashboardController;
use App\Http\Controllers\CamionPlateauController;

/*
|--------------------------------------------------------------------------
| Module Matériel Roulant
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('materiel')->name('materiel.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [MaterielController::class, 'dashboard'])->name('dashboard');

    // Véhicules (utilise le contrôleur existant)
    Route::get('/vehicules', [VehiculeController::class, 'index'])->name('vehicules');
    Route::get('/vehicules/create', [VehiculeController::class, 'create'])->name('vehicules.create');
    Route::post('/vehicules', [VehiculeController::class, 'store'])->name('vehicules.store');
    Route::get('/vehicules/{id}', [VehiculeController::class, 'show'])->name('vehicules.show');
    Route::get('/vehicules/{id}/edit', [VehiculeController::class, 'edit'])->name('vehicules.edit');
    Route::put('/vehicules/{id}', [VehiculeController::class, 'update'])->name('vehicules.update');
    Route::delete('/vehicules/{id}', [VehiculeController::class, 'destroy'])->name('vehicules.destroy');

    // Missions
    Route::get('/missions', [\App\Http\Controllers\VehicleMissionController::class, 'index'])->name('missions.index');
    Route::get('/missions/create', [\App\Http\Controllers\VehicleMissionController::class, 'create'])->name('missions.create');
    Route::post('/missions', [\App\Http\Controllers\VehicleMissionController::class, 'store'])->name('missions.store');
    Route::get('/missions/export', [\App\Http\Controllers\VehicleMissionController::class, 'export'])->name('missions.export');
    Route::get('/missions/{mission}', [\App\Http\Controllers\VehicleMissionController::class, 'show'])->name('missions.show');
    Route::get('/missions/{mission}/edit', [\App\Http\Controllers\VehicleMissionController::class, 'edit'])->name('missions.edit');
    Route::put('/missions/{mission}', [\App\Http\Controllers\VehicleMissionController::class, 'update'])->name('missions.update');
    Route::delete('/missions/{mission}', [\App\Http\Controllers\VehicleMissionController::class, 'destroy'])->name('missions.destroy');

    // ════════════════════════════════════════════════════════════════════════════
    // MODULE COST CONTROL - REFONTE COMPLÈTE
    // ════════════════════════════════════════════════════════════════════════════
    Route::prefix('cost-control')->group(function () {

        // Dashboard principal
        Route::get('/', [CostControlDashboardController::class, 'home']);
        Route::get('/home', [CostControlDashboardController::class, 'home']);
        // Alias legacy: certaines URLs en prod pointent vers /materiel/cost-control/pointages/create
        Route::get('/pointages/create', [VehicleCostControlController::class, 'createEnginPointage'])->name('cost-control.pointages.create');

        // Groupe avec namespacing explicite pour les sous-modules
        Route::prefix('engin')->name('cost-control.engin.')->group(function () {
            Route::get('/dashboard', [VehicleCostControlController::class, 'listStandard'])->name('dashboard');
            Route::get('/list', [VehicleCostControlController::class, 'listStandard'])->name('list');
            Route::get('/rapport-cost-controle', [VehicleCostControlController::class, 'rapportCostControl'])->name('rapport');
            Route::get('/pointages/create', [VehicleCostControlController::class, 'createEnginPointage'])->name('pointages.create');
            Route::post('/pointages', [VehicleCostControlController::class, 'store'])->name('pointages.store');
            Route::get('/pointages/{pointage}', [VehicleCostControlController::class, 'show'])->name('pointages.show');
            Route::get('/pointages/{pointage}/edit', [VehicleCostControlController::class, 'edit'])->name('pointages.edit');
            Route::put('/pointages/{pointage}', [VehicleCostControlController::class, 'update'])->name('pointages.update');
            Route::delete('/pointages/{pointage}', [VehicleCostControlController::class, 'destroy'])->name('pointages.destroy');
            Route::get('/charges/create', [VehicleCostControlController::class, 'createFinancialEntry'])->name('charges.create');
            Route::post('/charges', [VehicleCostControlController::class, 'storeFinancialEntry'])->name('charges.store');
            Route::get('/projets-termines', [VehicleCostControlController::class, 'projetsTermines'])->name('projets-termines');
            Route::get('/missions/{mission}/fiche', [VehicleCostControlController::class, 'fichePointage'])->name('fiche');
        });

        // ────────────────────────────────────────────────────────────────────────
        // CAMION PLATEAU - Module avec pointages par voyage + chrono
        // ────────────────────────────────────────────────────────────────────────
        Route::prefix('plateau')->name('cost-control.plateau.')->group(function () {
            Route::get('/dashboard', [VehicleCostControlController::class, 'listCamionPlateau'])->name('dashboard');
            Route::get('/list', [VehicleCostControlController::class, 'listCamionPlateau'])->name('list');

            // Pointage classique
            Route::get('/pointages/create', [VehicleCostControlController::class, 'create'])->name('pointages.create');
            Route::post('/pointages', [VehicleCostControlController::class, 'store'])->name('pointages.store');
            Route::get('/pointages/{pointage}', [VehicleCostControlController::class, 'show'])->name('pointages.show');
            Route::get('/pointages/{pointage}/edit', [VehicleCostControlController::class, 'edit'])->name('pointages.edit');
            Route::put('/pointages/{pointage}', [VehicleCostControlController::class, 'update'])->name('pointages.update');
            Route::delete('/pointages/{pointage}', [VehicleCostControlController::class, 'destroy'])->name('pointages.destroy');

            // Pointage chronométrique (NOUVEAU)
            Route::prefix('chrono')->name('chrono.')->group(function () {
                Route::get('/dashboard', [ChrononomiqueCostControlController::class, 'dashboard'])->name('dashboard');
                Route::get('/start', [ChrononomiqueCostControlController::class, 'startForm'])->name('start-form');
                Route::post('/start', [ChrononomiqueCostControlController::class, 'start'])->name('start');
                Route::post('/stop/{pointage}', [ChrononomiqueCostControlController::class, 'stop'])->name('stop');
                Route::delete('/cancel/{pointage}', [ChrononomiqueCostControlController::class, 'cancel'])->name('cancel');
                Route::get('/to-invoice', [ChrononomiqueCostControlController::class, 'toInvoice'])->name('to-invoice');
                Route::post('/mark-invoiced', [ChrononomiqueCostControlController::class, 'markInvoiced'])->name('mark-invoiced');
            });

            // Paramétrage
            Route::prefix('parametrage')->name('parametrage.')->group(function () {
                Route::get('/', [CamionPlateauController::class, 'parametrage'])->name('index');
                Route::get('/create', [CamionPlateauController::class, 'createParametrage'])->name('create');
                Route::post('/', [CamionPlateauController::class, 'storeParametrage'])->name('store');
                Route::get('/{param}', [CamionPlateauController::class, 'showParametrage'])->name('show');
                Route::get('/{param}/edit', [CamionPlateauController::class, 'editParametrage'])->name('edit');
                Route::put('/{param}', [CamionPlateauController::class, 'updateParametrage'])->name('update');
                Route::delete('/{param}', [CamionPlateauController::class, 'destroyParametrage'])->name('destroy');
            });

            // Facturation & Suivi
            Route::get('/facturation/mois', [CamionPlateauController::class, 'facturations'])->name('facturation.mois');
            Route::get('/suivi-voyages', [CamionPlateauController::class, 'suiviVoyages'])->name('suivi-voyages');
            Route::get('/projets-termines', [VehicleCostControlController::class, 'projetsTermines'])->name('projets-termines');
            Route::get('/charges/create', [VehicleCostControlController::class, 'createFinancialEntry'])->name('charges.create');
            Route::post('/charges', [VehicleCostControlController::class, 'storeFinancialEntry'])->name('charges.store');
        });

        // ────────────────────────────────────────────────────────────────────────
        // LEGACY ROUTES - Compatibilité avec url existantes
        // ────────────────────────────────────────────────────────────────────────
        Route::get('/index', [VehicleCostControlController::class, 'index'])->name('cost-control.index');
        Route::get('/dashboard', [VehicleCostControlController::class, 'index'])->name('cost-control.dashboard');
        Route::get('/list', [VehicleCostControlController::class, 'list'])->name('cost-control.list');
        Route::get('/list-standard', [VehicleCostControlController::class, 'listStandard'])->name('cost-control.list-standard');
        Route::get('/list-camion-plateau', [VehicleCostControlController::class, 'listCamionPlateau'])->name('cost-control.list-camion-plateau');
        Route::get('/create', [VehicleCostControlController::class, 'create'])->name('cost-control.create');
        Route::post('/store', [VehicleCostControlController::class, 'store'])->name('cost-control.store');
        Route::get('/{pointage}', [VehicleCostControlController::class, 'show'])->name('cost-control.show');
        Route::get('/{pointage}/edit', [VehicleCostControlController::class, 'edit'])->name('cost-control.edit');
        Route::put('/{pointage}', [VehicleCostControlController::class, 'update'])->name('cost-control.update');
        Route::delete('/{pointage}', [VehicleCostControlController::class, 'destroy'])->name('cost-control.destroy');
        Route::get('/projets-termines', [VehicleCostControlController::class, 'projetsTermines'])->name('cost-control.projets-termines');
        Route::get('/missions/{mission}/fiche-pointage', [VehicleCostControlController::class, 'fichePointage'])->name('cost-control.fiche-pointage');
        Route::get('/financial-entries/create', [VehicleCostControlController::class, 'createFinancialEntry'])->name('cost-control.financial-entries.create');
        Route::post('/financial-entries', [VehicleCostControlController::class, 'storeFinancialEntry'])->name('cost-control.financial-entries.store');
        Route::get('/financial-entries/{entry}', [VehicleCostControlController::class, 'showFinancialEntry'])->name('cost-control.financial-entries.show');
        Route::get('/financial-entries/{entry}/edit', [VehicleCostControlController::class, 'editFinancialEntry'])->name('cost-control.financial-entries.edit');
        Route::put('/financial-entries/{entry}', [VehicleCostControlController::class, 'updateFinancialEntry'])->name('cost-control.financial-entries.update');
        Route::delete('/financial-entries/{entry}', [VehicleCostControlController::class, 'destroyFinancialEntry'])->name('cost-control.financial-entries.destroy');

        // Camion Plateau Legacy (before refonte)
        Route::prefix('camion-plateau')->name('cost-control.camion-plateau.')->group(function () {
            Route::get('/parametrage', [CamionPlateauController::class, 'parametrage'])->name('parametrage');
            Route::get('/parametrage/create', [CamionPlateauController::class, 'createParametrage'])->name('parametrage.create');
            Route::post('/parametrage', [CamionPlateauController::class, 'storeParametrage'])->name('parametrage.store');
            Route::get('/parametrage/{param}/edit', [CamionPlateauController::class, 'editParametrage'])->name('parametrage.edit');
            Route::put('/parametrage/{param}', [CamionPlateauController::class, 'updateParametrage'])->name('parametrage.update');
            Route::delete('/parametrage/{param}', [CamionPlateauController::class, 'destroyParametrage'])->name('parametrage.destroy');
            Route::get('/facturation-mois', [CamionPlateauController::class, 'facturations'])->name('facturation-mois');
            Route::get('/suivi-voyages', [CamionPlateauController::class, 'suiviVoyages'])->name('suivi-voyages');
        });
    }); // Fermeture du groupe cost-control

    // ────────────────────────────────────────────────────────────────────────────────
    // LEGACY DIRECT ROUTES (backward compatibility)
    // ────────────────────────────────────────────────────────────────────────────────
    Route::get('/cost-control', [CostControlDashboardController::class, 'home'])->name('cost-control-home');

    // Missions - Comptabilité (Enregistrement CA et Dépenses)
    Route::post('/missions/{mission}/mark-completed', [\App\Http\Controllers\VehicleMissionController::class, 'markCompleted'])->name('missions.mark-completed');
    Route::post('/missions/{mission}/record-revenue', [\App\Http\Controllers\VehicleMissionController::class, 'recordRevenue'])->name('missions.record-revenue');
    Route::post('/missions/{mission}/record-expense', [\App\Http\Controllers\VehicleMissionController::class, 'recordExpense'])->name('missions.record-expense');

    // Maintenance
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store');
        Route::get('/{maintenance}', [MaintenanceController::class, 'show'])->name('show');
        Route::get('/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('edit');
        Route::put('/{maintenance}', [MaintenanceController::class, 'update'])->name('update');
        Route::post('/{maintenance}/result', [MaintenanceController::class, 'storeResult'])->name('result.store');
    });

    // Carburant
    Route::prefix('carburant')->name('carburant.')->group(function () {
        Route::get('/', [CarburantController::class, 'index'])->name('index');
        Route::get('/create', [CarburantController::class, 'create'])->name('create');
        Route::post('/', [CarburantController::class, 'store'])->name('store');
    });

    // Assurances
    Route::prefix('assurances')->name('assurances.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Materiel\AssuranceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Materiel\AssuranceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Materiel\AssuranceController::class, 'store'])->name('store');
        Route::get('/{assurance}', [\App\Http\Controllers\Materiel\AssuranceController::class, 'show'])->name('show');
        Route::get('/{assurance}/edit', [\App\Http\Controllers\Materiel\AssuranceController::class, 'edit'])->name('edit');
        Route::put('/{assurance}', [\App\Http\Controllers\Materiel\AssuranceController::class, 'update'])->name('update');
        Route::delete('/{assurance}', [\App\Http\Controllers\Materiel\AssuranceController::class, 'destroy'])->name('destroy');
    });

    // Visites Techniques
    Route::prefix('visites')->name('visites.')->group(function () {
        Route::get('/', [VisiteTechniqueController::class, 'index'])->name('index');
        Route::get('/create', [VisiteTechniqueController::class, 'create'])->name('create');
        Route::post('/', [VisiteTechniqueController::class, 'store'])->name('store');
        Route::get('/{visite}', [VisiteTechniqueController::class, 'show'])->name('show');
        Route::get('/{visite}/edit', [VisiteTechniqueController::class, 'edit'])->name('edit');
        Route::put('/{visite}', [VisiteTechniqueController::class, 'update'])->name('update');
        Route::delete('/{visite}', [VisiteTechniqueController::class, 'destroy'])->name('destroy');
    });

    // Rapports
    Route::get('/rapports', [MaterielController::class, 'rapports'])->name('rapports');
});

// Route alias pour cost-control home - créée en dehors du groupe pour éviter problèmes de namespace
Route::get('/materiel/cost-control/home', [CostControlDashboardController::class, 'home'])->name('materiel.cost-control.home')->middleware('auth');
Route::get('/materiel/cost-control', [CostControlDashboardController::class, 'home'])->middleware('auth');
