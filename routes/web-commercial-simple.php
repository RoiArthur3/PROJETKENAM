<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Commerciales Simple
|--------------------------------------------------------------------------
*/

Route::prefix('commercial')->name('commercial.')->group(function () {

    // Tableau de bord commercial - version simple
    Route::get('/', function() {
        return view('commercial.dashboard-simple');
    })->name('dashboard');

    // Redirection /commercial/dashboard vers /commercial
    Route::get('/dashboard', function() {
        return redirect()->route('commercial.dashboard');
    })->name('dashboard.redirect');

    // Route de test
    Route::get('/test', function() {
        return 'Test commercial OK !';
    })->name('test');

    // Bons de commande
    Route::get('/bon-commande', function() {
        return view('commercial.bon-commande.index');
    })->name('bon-commande.index');

    Route::get('/bon-commande/create', function() {
        return view('commercial.bon-commande.create');
    })->name('bon-commande.create');

    // Bons de livraison
    Route::get('/bon-livraison', function() {
        return view('commercial.bon-livraison.index');
    })->name('bon-livraison.index');

    Route::get('/bon-livraison/create', function() {
        return view('commercial.bon-livraison.create');
    })->name('bon-livraison.create');

    // Contrats
    Route::get('/contrats', function() {
        return view('commercial.contrats');
    })->name('contrats.index');

    Route::get('/contrats/create', function() {
        return view('commercial.contrats-create');
    })->name('contrats.create');

    Route::get('/contrats/{id}', function($id) {
        return view('commercial.contrats-show', ['id' => $id]);
    })->name('contrats.show');

    Route::get('/contrats/{id}/edit', function($id) {
        return view('commercial.contrats-edit', ['id' => $id]);
    })->name('contrats.edit');

    // Devis
    Route::get('/devis', [App\Http\Controllers\CommercialController::class, 'devisIndex'])->name('devis.index');

    Route::get('/devis/create', [App\Http\Controllers\CommercialController::class, 'devisCreate'])->name('devis.create');

    Route::get('/devis/{id}', [App\Http\Controllers\CommercialController::class, 'devisShow'])->name('devis.show');

    Route::get('/devis/{id}/edit', [App\Http\Controllers\CommercialController::class, 'devisEdit'])->name('devis.edit');

    // Clients - Test simple d'abord
    Route::get('/clients', function() {
        return view('commercial.clients');
    })->name('clients.index');

    Route::get('/clients-test', function() {
        return 'Route clients fonctionne !';
    })->name('clients.test');

    Route::get('/clients/create', [App\Http\Controllers\CommercialController::class, 'clientsCreate'])->name('clients.create');

    Route::post('/clients', [App\Http\Controllers\CommercialController::class, 'clientsStore'])->name('clients.store');

    Route::get('/clients/{client}', [App\Http\Controllers\CommercialController::class, 'clientsShow'])->name('clients.show');

    Route::get('/clients/{client}/edit', [App\Http\Controllers\CommercialController::class, 'clientsEdit'])->name('clients.edit');

    Route::put('/clients/{client}', [App\Http\Controllers\CommercialController::class, 'clientsUpdate'])->name('clients.update');

    Route::delete('/clients/{client}', [App\Http\Controllers\CommercialController::class, 'clientsDestroy'])->name('clients.destroy');

});
