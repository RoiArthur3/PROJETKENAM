<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;

Route::prefix('stock')->name('stock.')->middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', [StockController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [StockController::class, 'index'])->name('index');

    // Entrepôts
    Route::get('/warehouses', [StockController::class, 'warehouses'])->name('warehouses.index');
    Route::get('/warehouses/create', [StockController::class, 'createWarehouse'])->name('warehouses.create');
    Route::post('/warehouses', [StockController::class, 'storeWarehouse'])->name('warehouses.store');
    Route::get('/warehouses/{warehouse}', [StockController::class, 'showWarehouse'])->name('warehouses.show');
    Route::get('/warehouses/{warehouse}/edit', [StockController::class, 'editWarehouse'])->name('warehouses.edit');
    Route::put('/warehouses/{warehouse}', [StockController::class, 'updateWarehouse'])->name('warehouses.update');
    Route::delete('/warehouses/{warehouse}', [StockController::class, 'destroyWarehouse'])->name('warehouses.destroy');
    Route::get('/warehouses/export', [StockController::class, 'exportWarehouses'])->name('warehouses.export');

    // Produits
    Route::get('/products', [StockController::class, 'products'])->name('products.index');
    Route::get('/products/export', [StockController::class, 'exportProducts'])->name('products.export');

    // Entrées
    Route::get('/entries', [StockController::class, 'entries'])->name('entries');
    Route::get('/entries/create', [StockController::class, 'createEntry'])->name('entries.create');
    Route::post('/entries', [StockController::class, 'storeEntry'])->name('entries.store');
    Route::get('/entries/export', [StockController::class, 'exportEntries'])->name('entries.export');

    // Sorties
    Route::get('/exits', [StockController::class, 'exits'])->name('exits');
    Route::get('/exits/create', [StockController::class, 'createExit'])->name('exits.create');
    Route::post('/exits', [StockController::class, 'storeExit'])->name('exits.store');
    Route::get('/exits/export', [StockController::class, 'exportExits'])->name('exits.export');

    // Transferts
    Route::get('/transfers', [StockController::class, 'transfers'])->name('transfers');
    Route::get('/transfers/create', [StockController::class, 'createTransfer'])->name('transfers.create');
    Route::post('/transfers', [StockController::class, 'storeTransfer'])->name('transfers.store');
    Route::get('/transfers/export', [StockController::class, 'exportTransfers'])->name('transfers.export');

    // Inventaire
    Route::get('/inventory', [StockController::class, 'inventory'])->name('inventory');
    Route::get('/inventory/export', [StockController::class, 'exportInventory'])->name('inventory.export');

    // Magasin
    Route::get('/magasin', [StockController::class, 'magasin'])->name('magasin');
    Route::get('/magasin/export', [StockController::class, 'exportMagasin'])->name('magasin.export');

    // Mouvements
    Route::get('/mouvements', [StockController::class, 'mouvements'])->name('mouvements');
    Route::get('/mouvements/export', [StockController::class, 'exportMouvements'])->name('mouvements.export');

    // Alertes
    Route::get('/alertes', [StockController::class, 'alertes'])->name('alertes');
    Route::get('/alertes/export', [StockController::class, 'exportAlertes'])->name('alertes.export');

    // API
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/check-stock', [StockController::class, 'checkStock'])->name('check-stock');
    });
});
