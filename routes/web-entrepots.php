<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntrepotController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockTransfertController;

Route::prefix('warehouse')->name('warehouse.')->middleware(['web', 'auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [EntrepotController::class, 'dashboard'])->name('dashboard');

    // Liste des entrepôts (index)
    Route::get('/', [EntrepotController::class, 'index'])->name('list');
    Route::get('/list', [EntrepotController::class, 'index']); // Alias pour /entrepots/

    // Stock
    Route::get('/stock', [EntrepotController::class, 'stock'])->name('stock');
    Route::get('/inventaire', [EntrepotController::class, 'stock'])->name('inventaire'); // Alias pour sidebar
    Route::get('/entrees', [EntrepotController::class, 'stock'])->name('entrees'); // Alias pour sidebar
    Route::get('/entrees/create', [\App\Http\Controllers\MagasinController::class, 'createEntree'])->name('entrees.create');
    Route::post('/entrees', [\App\Http\Controllers\MagasinController::class, 'storeEntree'])->name('entrees.store');
    Route::get('/sorties', [EntrepotController::class, 'stock'])->name('sorties'); // Alias pour sidebar

    // Transferts
    Route::get('/transferts', [StockTransfertController::class, 'index'])->name('transferts');
    Route::get('/transferts/create', [StockTransfertController::class, 'create'])->name('transferts.create');
    Route::post('/transferts', [StockTransfertController::class, 'store'])->name('transferts.store');

    // Rapports
    Route::get('/rapports', [EntrepotController::class, 'rapports'])->name('rapports');

    // CRUD Entrepôts
    Route::get('/create', [EntrepotController::class, 'create'])->name('create');
    Route::post('/', [EntrepotController::class, 'store'])->name('store');
    Route::get('/{entrepot}', [EntrepotController::class, 'show'])->name('show');
    Route::get('/{entrepot}/edit', [EntrepotController::class, 'edit'])->name('edit');
    Route::put('/{entrepot}', [EntrepotController::class, 'update'])->name('update');
    Route::delete('/{entrepot}', [EntrepotController::class, 'destroy'])->name('destroy');
});

Route::prefix('entrepots')->name('entrepots.')->middleware(['web', 'auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [EntrepotController::class, 'dashboard'])->name('dashboard');

    // Liste des entrepôts (index)
    Route::get('/', [EntrepotController::class, 'index'])->name('list');
    Route::get('/list', [EntrepotController::class, 'index']); // Alias pour /entrepots/

    // Stock (utilisant EntrepotController pour éviter les erreurs)
    Route::get('/stock', [EntrepotController::class, 'stock'])->name('stock');

    // Transferts
    Route::get('/transferts', [StockTransfertController::class, 'index'])->name('transferts');

    // Rapports
    Route::get('/rapports', [EntrepotController::class, 'rapports'])->name('rapports');

    // CRUD Entrepôts (si nécessaire, mais souvent géré via 'stock.entrepots' dans web.php,
    // on peut ajouter des alias ici si le dashboard utilise ces noms spécifiques)
    Route::get('/create', [EntrepotController::class, 'create'])->name('create');
    Route::post('/', [EntrepotController::class, 'store'])->name('store');
    Route::get('/{entrepot}', [EntrepotController::class, 'show'])->name('show');
    Route::get('/{entrepot}/edit', [EntrepotController::class, 'edit'])->name('edit');
    Route::put('/{entrepot}', [EntrepotController::class, 'update'])->name('update');
    Route::delete('/{entrepot}', [EntrepotController::class, 'destroy'])->name('destroy');
});
