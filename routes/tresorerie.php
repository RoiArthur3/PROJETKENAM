<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tresorerie\DashboardController;
use App\Http\Controllers\Tresorerie\CaisseController;
use App\Http\Controllers\Tresorerie\ApprovisionnementController;
use App\Http\Controllers\DepenseCaisseController as CoreDepenseCaisseController;
use App\Http\Controllers\Tresorerie\RapprochementController;
use App\Http\Controllers\Tresorerie\CompteBancaireController;
use App\Http\Controllers\Tresorerie\VirementController;
use App\Http\Controllers\Tresorerie\DepenseCaisseController;
use App\Http\Controllers\Tresorerie\EncaissementController;
use App\Http\Controllers\ComptabiliteController;

/*
|--------------------------------------------------------------------------
| Module Trésorerie
|--------------------------------------------------------------------------
*/

Route::prefix('tresorerie')->name('tresorerie.')->middleware(['auth', 'module:tresorerie'])->group(function () {

    // Tableau de bord de la trésorerie
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.main');

    // Gestion des caisses
    Route::prefix('caisses')->name('caisses.')->group(function () {
        Route::get('/', [CaisseController::class, 'index'])->name('index');
        Route::get('/create', [CaisseController::class, 'create'])->name('create');
        Route::post('/', [CaisseController::class, 'store'])->name('store');
        Route::get('/{caisse}', [CaisseController::class, 'show'])->name('show');
        Route::get('/{caisse}/edit', [CaisseController::class, 'edit'])->name('edit');
        Route::put('/{caisse}', [CaisseController::class, 'update'])->name('update');
        Route::delete('/{caisse}', [CaisseController::class, 'destroy'])->name('destroy');
        Route::get('/{caisse}/historique', [CaisseController::class, 'historique'])->name('historique');
    });
    Route::get('caisses-list', [CaisseController::class, 'index'])->name('caisses');
    Route::get('caisse', [CaisseController::class, 'index'])->name('caisse'); // Alias requested

    // Comptes bancaires (standalone - sans banque pré-sélectionnée)
    Route::prefix('comptes-bancaires')->name('comptes-bancaires.')->group(function () {
        Route::get('/', [CompteBancaireController::class, 'index'])->name('index');
        Route::get('/create', [CompteBancaireController::class, 'createStandalone'])->name('create');
        Route::post('/', [CompteBancaireController::class, 'storeStandalone'])->name('store');
        Route::get('/{compte}', [CompteBancaireController::class, 'showStandalone'])->name('show');
        Route::get('/{compte}/edit', [CompteBancaireController::class, 'editStandalone'])->name('edit');
        Route::put('/{compte}', [CompteBancaireController::class, 'updateStandalone'])->name('update');
        Route::delete('/{compte}', [CompteBancaireController::class, 'destroyStandalone'])->name('destroy');
    });
    Route::get('comptes-bancaires-list', [ComptabiliteController::class, 'comptesBancairesIndex'])->name('comptes-bancaires');

    // Opérations bancaires (alias pour compatibilité)
    Route::prefix('banque')->name('banque.')->group(function () {
        Route::get('/', [CompteBancaireController::class, 'index'])->name('index');
        Route::get('/create', [CompteBancaireController::class, 'createStandalone'])->name('create');
        Route::post('/', [CompteBancaireController::class, 'storeStandalone'])->name('store');
        Route::get('/{operation}', [CompteBancaireController::class, 'showStandalone'])->name('show');
        Route::get('/{operation}/edit', [CompteBancaireController::class, 'editStandalone'])->name('edit');
        Route::put('/{operation}', [CompteBancaireController::class, 'updateStandalone'])->name('update');
        Route::delete('/{operation}', [CompteBancaireController::class, 'destroyStandalone'])->name('destroy');
    });
    Route::get('banque', [CompteBancaireController::class, 'index'])->name('banque');

    // Demandes d'approvisionnement (Trésorerie → Comptabilité)
    Route::prefix('approvisionnement-demandes')->name('approvisionnement-demandes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ApprovisionnementDemandeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\ApprovisionnementDemandeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\ApprovisionnementDemandeController::class, 'store'])->name('store');
        Route::get('/{demande}', [\App\Http\Controllers\ApprovisionnementDemandeController::class, 'show'])->name('show');
    });

    // Approvisionnements
    Route::prefix('approvisionnements')->name('approvisionnements.')->group(function () {
        Route::get('/', [ApprovisionnementController::class, 'index'])->name('index');
        Route::get('/create', [ApprovisionnementController::class, 'create'])->name('create');
        Route::post('/', [ApprovisionnementController::class, 'store'])->name('store');
        Route::get('/{approvisionnement}', [ApprovisionnementController::class, 'show'])->name('show');
        Route::post('/{approvisionnement}/valider', [ApprovisionnementController::class, 'valider'])->name('valider');
        Route::post('/{approvisionnement}/rejeter', [ApprovisionnementController::class, 'rejeter'])->name('rejeter');
        Route::post('/{approvisionnement}/decaisser', [ApprovisionnementController::class, 'decaisser'])->name('decaisser');
    });
    Route::get('approvisionnements-list', [ApprovisionnementController::class, 'index'])->name('approvisionnements');

    // Dépenses de caisse (branchées sur la BDD complète)
    Route::prefix('depenses')->name('depenses.')->group(function () {
        Route::get('/', [CoreDepenseCaisseController::class, 'index'])->name('index');
        Route::get('/create', [CoreDepenseCaisseController::class, 'create'])->name('create');
        Route::post('/', [CoreDepenseCaisseController::class, 'store'])->name('store');
        Route::get('/{depense}', [CoreDepenseCaisseController::class, 'show'])->name('show');
        Route::get('/{depense}/edit', [CoreDepenseCaisseController::class, 'edit'])->name('edit');
        Route::put('/{depense}', [CoreDepenseCaisseController::class, 'update'])->name('update');
        Route::delete('/{depense}', [CoreDepenseCaisseController::class, 'destroy'])->name('destroy');
    });
    Route::get('depenses-list', [CoreDepenseCaisseController::class, 'index'])->name('depenses');

    // Rapprochements
    Route::prefix('rapprochements')->name('rapprochements.')->group(function () {
        Route::get('/', [RapprochementController::class, 'index'])->name('index');
        Route::get('/create', [RapprochementController::class, 'create'])->name('create');
        Route::post('/', [RapprochementController::class, 'store'])->name('store');
        Route::get('/{rapprochement}', [RapprochementController::class, 'show'])->name('show');
        Route::get('/{rapprochement}/edit', [RapprochementController::class, 'edit'])->name('edit');
        Route::put('/{rapprochement}', [RapprochementController::class, 'update'])->name('update');
        Route::delete('/{rapprochement}', [RapprochementController::class, 'destroy'])->name('destroy');
        Route::post('/{rapprochement}/valider', [RapprochementController::class, 'valider'])->name('valider');
    });
    Route::get('rapprochements-list', [RapprochementController::class, 'index'])->name('rapprochements');

    // Encaissements
    Route::get('/encaissements', [EncaissementController::class, 'index'])->name('encaissements');
    Route::get('/encaissements/create', [EncaissementController::class, 'create'])->name('encaissements.create');
    Route::post('/encaissements', [EncaissementController::class, 'store'])->name('encaissements.store');
    Route::get('/encaissements/{id}', [EncaissementController::class, 'show'])->name('encaissements.show');

    Route::get('/decaissements', [DashboardController::class, 'decaissements'])->name('decaissements');
    Route::get('/decaissements/create', [DepenseCaisseController::class, 'create'])->name('decaissements.create');
    Route::post('/decaissements', [DepenseCaisseController::class, 'store'])->name('decaissements.store');
    Route::get('/decaissements/{id}', [DashboardController::class, 'decaissementsShow'])->name('decaissements.show');
    Route::post('/decaissements/{id}/encaisser', [DepenseCaisseController::class, 'markEncashed'])->name('decaissements.encaisser');
    Route::get('/decaissements/{id}/imprimer', [DepenseCaisseController::class, 'imprimer'])->name('decaissements.imprimer');
    Route::get('/decaissements/{id}/edit', [DepenseCaisseController::class, 'edit'])->name('decaissements.edit');
    Route::put('/decaissements/{id}', [DepenseCaisseController::class, 'update'])->name('decaissements.update');
    Route::delete('/decaissements/{id}', [DepenseCaisseController::class, 'destroy'])->name('decaissements.destroy');
    Route::get('/soldes-caisse', [DashboardController::class, 'soldesCaisse'])->name('soldes-caisse');

    // A Payer (utilise la même logique que les validations à payer)
    Route::get('/a-payer', [DashboardController::class, 'aPayer'])->name('a-payer');

    Route::get('/paiements-fournisseurs', [DashboardController::class, 'paiementsFournisseurs'])->name('paiements-fournisseurs');
    Route::get('/paiements-salaires', [DashboardController::class, 'paiementsSalaires'])->name('paiements-salaires');
    Route::get('/flux', [DashboardController::class, 'flux'])->name('flux');

    // Avances
    Route::prefix('avances')->name('avances.')->group(function () {
        Route::get('/', [DashboardController::class, 'avances'])->name('index');
        Route::get('/create', [DashboardController::class, 'avancesCreate'])->name('create');
        Route::post('/', [DashboardController::class, 'avancesStore'])->name('store');
    });
    Route::get('avances', [DashboardController::class, 'avances'])->name('avances');

    // Paiements
    Route::prefix('paiements')->name('paiements.')->group(function () {
        Route::get('/', [DashboardController::class, 'paiements'])->name('index');
        Route::get('/create', [DashboardController::class, 'paiementsCreate'])->name('create');
        Route::post('/', [DashboardController::class, 'paiementsStore'])->name('store');
        Route::get('/{id}', [DashboardController::class, 'paiementsShow'])->name('show');
        Route::get('/{id}/edit', [DashboardController::class, 'paiementsEdit'])->name('edit');
        Route::delete('/{id}', [DashboardController::class, 'paiementsDestroy'])->name('destroy');
    });
    Route::get('paiements', [DashboardController::class, 'paiements'])->name('paiements');

    // Virements
    Route::prefix('virements')->name('virements.')->group(function () {
        Route::get('/', [VirementController::class, 'index'])->name('index');
        Route::get('/create', [VirementController::class, 'create'])->name('create');
        Route::post('/', [VirementController::class, 'store'])->name('store');
        Route::get('/{virement}', [VirementController::class, 'show'])->name('show');
        Route::get('/{virement}/edit', [VirementController::class, 'edit'])->name('edit');
        Route::put('/{virement}', [VirementController::class, 'update'])->name('update');
        Route::delete('/{virement}', [VirementController::class, 'destroy'])->name('destroy');
        Route::post('/{virement}/annuler', [VirementController::class, 'annuler'])->name('annuler');
    });
    Route::get('virements', [VirementController::class, 'index'])->name('virements');

    // Bons pour Accord
    Route::get('/bon-pour-accord', [DashboardController::class, 'bonPourAccord'])->name('bon-pour-accord');

    // API pour le dashboard
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/solde-caisses', [DashboardController::class, 'getSoldeCaisses'])->name('solde-caisses');
        Route::get('/avances-ouvertes', [DashboardController::class, 'getAvancesOuvertes'])->name('avances-ouvertes');
        Route::get('/depenses-mensuelles', [DashboardController::class, 'getDepensesMensuelles'])->name('depenses-mensuelles');
    });
});
