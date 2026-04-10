<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommercialController;
use App\Http\Controllers\Commercial\CommercialCommandeController;
use App\Http\Controllers\Commercial\OpportuniteController;
use App\Http\Controllers\Commercial\ProspectController;

/*
|--------------------------------------------------------------------------
| Routes Commerciales
|--------------------------------------------------------------------------
|
| Routes pour le module commercial
|
*/

Route::prefix('commercial')->name('commercial.')->middleware(['auth'])->group(function () {

    // Tableau de bord commercial
    Route::get('/', [CommercialController::class, 'dashboard'])->name('dashboard.index');
    Route::get('/dashboard', [CommercialController::class, 'dashboard'])->name('dashboard');

    // Gestion des contrats
    Route::get('/contrats', [CommercialController::class, 'contratsIndex'])->name('contrats.index');
    Route::get('/contrats/create', [CommercialController::class, 'contratsCreate'])->name('contrats.create');
    Route::post('/contrats', [CommercialController::class, 'contratsStore'])->name('contrats.store');
    Route::get('/contrats/{contrat}', [CommercialController::class, 'contratsShow'])->name('contrats.show');
    Route::get('/contrats/{contrat}/edit', [CommercialController::class, 'contratsEdit'])->name('contrats.edit');
    Route::put('/contrats/{contrat}', [CommercialController::class, 'contratsUpdate'])->name('contrats.update');
    Route::delete('/contrats/{contrat}', [CommercialController::class, 'contratsDestroy'])->name('contrats.destroy');

    // Gestion des clients
    Route::get('/clients', [CommercialController::class, 'clientsIndex'])->name('clients.index');
    Route::get('/clients/create', [CommercialController::class, 'clientsCreate'])->name('clients.create');
    Route::post('/clients', [CommercialController::class, 'clientsStore'])->name('clients.store');
    Route::get('/clients/{client}', [CommercialController::class, 'clientsShow'])->name('clients.show');
    Route::get('/clients/{client}/edit', [CommercialController::class, 'clientsEdit'])->name('clients.edit');
    Route::put('/clients/{client}', [CommercialController::class, 'clientsUpdate'])->name('clients.update');
    Route::delete('/clients/{client}', [CommercialController::class, 'clientsDestroy'])->name('clients.destroy');

    // Gestion des devis
    Route::get('/devis', [CommercialController::class, 'devisIndex'])->name('devis.index');
    Route::get('/devis/create', [CommercialController::class, 'devisCreate'])->name('devis.create');
    Route::post('/devis', [CommercialController::class, 'devisStore'])->name('devis.store');
    Route::get('/devis/{devis}', [CommercialController::class, 'devisShow'])->name('devis.show');
    Route::get('/devis/{devis}/edit', [CommercialController::class, 'devisEdit'])->name('devis.edit');
    Route::put('/devis/{devis}', [CommercialController::class, 'devisUpdate'])->name('devis.update');
    Route::delete('/devis/{devis}', [CommercialController::class, 'devisDestroy'])->name('devis.destroy');
    Route::post('/devis/{devis}/convert-facture', [CommercialController::class, 'convertDevisToFacture'])->name('devis.convert-facture');

    // Gestion des bons de commande et livraison
    Route::get('/bon-commande', [CommercialController::class, 'bonCommandeIndex'])->name('bon-commande.index');
    Route::get('/bon-commande/create', [CommercialController::class, 'bonCommandeCreate'])->name('bon-commande.create');
    Route::post('/bon-commande', [CommercialController::class, 'bonCommandeStore'])->name('bon-commande.store');
    Route::get('/bon-livraison', [CommercialController::class, 'bonLivraisonIndex'])->name('bon-livraison.index');
    Route::get('/bon-livraison/create', [CommercialController::class, 'bonLivraisonCreate'])->name('bon-livraison.create');

    // Commandes (Demande d'engins)
    Route::get('/commandes', [CommercialCommandeController::class, 'index'])->name('commandes.index');
    Route::get('/commandes/create', [CommercialCommandeController::class, 'create'])->name('commandes.create');
    Route::post('/commandes', [CommercialCommandeController::class, 'store'])->name('commandes.store');
    Route::get('/commandes/{id}', [CommercialCommandeController::class, 'show'])->name('commandes.show');
    Route::get('/commandes/{id}/edit', [CommercialCommandeController::class, 'edit'])->name('commandes.edit');
    Route::put('/commandes/{id}', [CommercialCommandeController::class, 'update'])->name('commandes.update');
    Route::post('/commandes/{id}/delete', [CommercialCommandeController::class, 'destroy'])->name('commandes.destroy');

    Route::post('/commandes/{id}/envoyer-logistique', [CommercialCommandeController::class, 'envoyerLogistique'])->name('commandes.envoyer-logistique');
    Route::get('/commandes/{id}/reponse', [CommercialCommandeController::class, 'createReponse'])->name('commandes.reponse.create');
    Route::post('/commandes/{id}/reponse', [CommercialCommandeController::class, 'storeReponse'])->name('commandes.reponse.store');
    Route::post('/commandes/{id}/valider', [CommercialCommandeController::class, 'valider'])->name('commandes.valider');
    Route::post('/commandes/{id}/refuser', [CommercialCommandeController::class, 'refuser'])->name('commandes.refuser');

    // Redirection vers la comptabilité pour les factures
    Route::get('/factures', function() {
        return redirect()->route('comptabilite.factures.index')->with('info', 'La gestion des factures est assurée par le service comptabilité.');
    })->name('factures.redirect');

    // API pour les graphiques et statistiques
    Route::get('/api/stats', [CommercialController::class, 'apiStats'])->name('api.stats');
    Route::get('/api/chart-data', [CommercialController::class, 'apiChartData'])->name('api.chart-data');

    // Gestion des opportunités
    Route::get('/opportunites', [OpportuniteController::class, 'index'])->name('opportunites.index');
    Route::get('/opportunites/create', [OpportuniteController::class, 'create'])->name('opportunites.create');
    Route::post('/opportunites', [OpportuniteController::class, 'store'])->name('opportunites.store');
    Route::get('/opportunites/{opportunite}', [OpportuniteController::class, 'show'])->name('opportunites.show');
    Route::get('/opportunites/{opportunite}/edit', [OpportuniteController::class, 'edit'])->name('opportunites.edit');
    Route::put('/opportunites/{opportunite}', [OpportuniteController::class, 'update'])->name('opportunites.update');
    Route::delete('/opportunites/{opportunite}', [OpportuniteController::class, 'destroy'])->name('opportunites.destroy');

    // Gestion des prospects
    Route::get('/prospects', [ProspectController::class, 'index'])->name('prospects.index');
    Route::get('/prospects/create', [ProspectController::class, 'create'])->name('prospects.create');
    Route::post('/prospects', [ProspectController::class, 'store'])->name('prospects.store');
    Route::get('/prospects/{prospect}', [ProspectController::class, 'show'])->name('prospects.show');
    Route::get('/prospects/{prospect}/edit', [ProspectController::class, 'edit'])->name('prospects.edit');
    Route::put('/prospects/{prospect}', [ProspectController::class, 'update'])->name('prospects.update');
    Route::delete('/prospects/{prospect}', [ProspectController::class, 'destroy'])->name('prospects.destroy');
});
