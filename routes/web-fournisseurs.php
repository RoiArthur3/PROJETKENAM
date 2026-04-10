<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\CommandeFournisseurController;
use App\Http\Controllers\FactureFournisseurController;

/*
|--------------------------------------------------------------------------
| Routes Fournisseurs
|--------------------------------------------------------------------------
*/

Route::prefix('fournisseurs')->name('fournisseurs.')->middleware(['auth'])->group(function () {

    // Evite la collision entre les routes statiques (/commandes, /factures)
    // et le parametre dynamique /{fournisseur}.
    Route::pattern('fournisseur', '[0-9]+');

    // Dashboard fournisseurs
    Route::get('/dashboard', [FournisseurController::class, 'dashboard'])->name('dashboard');

    // Gestion des fournisseurs
    Route::get('/', [FournisseurController::class, 'index'])->name('index');
    Route::get('/list', function() {
        return view('fournisseurs.list', ['fournisseurs' => \App\Models\Fournisseur::paginate(15)]);
    })->name('list');
    Route::get('/create', [FournisseurController::class, 'create'])->name('create');
    Route::post('/', [FournisseurController::class, 'store'])->name('store');
    Route::get('/{fournisseur}', [FournisseurController::class, 'show'])->name('show');
    Route::get('/{fournisseur}/edit', [FournisseurController::class, 'edit'])->name('edit');
    Route::put('/{fournisseur}', [FournisseurController::class, 'update'])->name('update');
    Route::delete('/{fournisseur}', [FournisseurController::class, 'destroy'])->name('destroy');

    // Import/Export
    Route::post('/import', [FournisseurController::class, 'import'])->name('import');
    Route::get('/export', [FournisseurController::class, 'export'])->name('export');
    Route::get('/{fournisseur}/pdf', [FournisseurController::class, 'downloadPDF'])->name('pdf');

    // Évaluations et contacts
    Route::post('/{fournisseur}/evaluations', [FournisseurController::class, 'addEvaluation'])->name('evaluations.store');
    Route::post('/{fournisseur}/contacts', [FournisseurController::class, 'addContact'])->name('contacts.store');
    Route::post('/{fournisseur}/documents', [FournisseurController::class, 'addDocument'])->name('documents.store');

    // Redirection /list
    Route::get('/list', [FournisseurController::class, 'listSimple'])->name('list');

    // Route de test debug
    Route::get('/test-debug', function () {
        return 'Debug Fournisseurs OK';
    });

    // Gestion des commandes fournisseurs
    Route::prefix('commandes')->name('commandes.')->group(function () {
        Route::get('/', [CommandeFournisseurController::class, 'indexGlobal'])->name('index');
        Route::get('/export/excel', [CommandeFournisseurController::class, 'exportGlobalExcel'])->name('export-excel');
        Route::get('/export/pdf', [CommandeFournisseurController::class, 'exportGlobalPdf'])->name('export-pdf');
        Route::get('/create', [CommandeFournisseurController::class, 'createGlobal'])->name('create');
        Route::post('/', [CommandeFournisseurController::class, 'storeGlobal'])->name('store');
        Route::get('/{commande}', [CommandeFournisseurController::class, 'showGlobal'])->name('show');
        Route::get('/{commande}/edit', [CommandeFournisseurController::class, 'editGlobal'])->name('edit');
        Route::put('/{commande}', [CommandeFournisseurController::class, 'updateGlobal'])->name('update');
        Route::delete('/{commande}', [CommandeFournisseurController::class, 'destroyGlobal'])->name('destroy');
        Route::post('/{commande}/valider', [CommandeFournisseurController::class, 'validerGlobal'])->name('valider');
        Route::get('/search-engins', [CommandeFournisseurController::class, 'searchEngins'])->name('search-engins');
    });

    // Gestion des factures fournisseurs
    Route::prefix('factures')->name('factures.')->group(function () {
        Route::get('/', [FactureFournisseurController::class, 'index'])->name('index');
        Route::get('/{fournisseur_id}', [FactureFournisseurController::class, 'index'])->whereNumber('fournisseur_id')->name('show_by_fournisseur');
        Route::get('/{fournisseur}/{facture}', [FactureFournisseurController::class, 'show'])->name('show');
    });
});
