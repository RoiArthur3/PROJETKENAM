<?php

use App\Http\Controllers\AchatController;
use Illuminate\Support\Facades\Route;

Route::prefix('achat')->name('achat.')->middleware(['auth', 'module:achat'])->group(function () {
    Route::get('/', [AchatController::class, 'index'])->name('index');
    Route::get('/create', [AchatController::class, 'create'])->name('create');
    Route::post('/', [AchatController::class, 'store'])->name('store');
    Route::get('/{commande}', [AchatController::class, 'show'])->name('show');
    Route::post('/{commande}/factures', [AchatController::class, 'storeFacture'])->name('factures.store');
    Route::post('/{commande}/factures/{facture}/paiements', [AchatController::class, 'storePaiement'])->name('paiements.store');
    Route::post('/{commande}/valider', [AchatController::class, 'valider'])->name('valider');
    Route::get('/{commande}/decaisser', [AchatController::class, 'redirectToDecaissement'])->name('decaisser');
});