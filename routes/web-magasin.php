<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MagasinController;
use App\Http\Controllers\ShopController;

Route::prefix('magasin')->name('magasin.')->middleware(['web', 'auth', 'module:magasin'])->group(function () {
    Route::get('/', [MagasinController::class, 'index'])->name('index');
    Route::get('/dashboard', [MagasinController::class, 'dashboard'])->name('dashboard');
    Route::get('/inventaire', [MagasinController::class, 'inventaire'])->name('inventaire');
    Route::get('/produits/create', [MagasinController::class, 'createProduit'])->name('produits.create');
    Route::get('/entrees', [MagasinController::class, 'entrees'])->name('entrees');
    Route::get('/entrees/create', [MagasinController::class, 'createEntree'])->name('entrees.create');
    Route::post('/entrees', [MagasinController::class, 'storeEntree'])->name('entrees.store');
    Route::get('/rapports', [MagasinController::class, 'rapports'])->name('rapports');

    // Routes pour la gestion des produits
    Route::prefix('produits')->name('produits.')->group(function () {
        Route::get('/create', [MagasinController::class, 'createProduit'])->name('create');
        Route::post('/', [MagasinController::class, 'storeProduit'])->name('store');
        Route::get('/{produit}', [MagasinController::class, 'showProduit'])->name('show');
        Route::get('/{produit}/edit', [MagasinController::class, 'editProduit'])->name('edit');
        Route::put('/{produit}', [MagasinController::class, 'updateProduit'])->name('update');
        Route::delete('/{produit}', [MagasinController::class, 'destroyProduit'])->name('destroy');
    });

    // Routes pour la gestion des sorties
    Route::prefix('sorties')->name('sorties.')->group(function () {
        Route::get('/', [MagasinController::class, 'sorties'])->name('index');
        Route::get('/create', [MagasinController::class, 'createSortie'])->name('create');
        Route::post('/', [MagasinController::class, 'storeSortie'])->name('store');
        Route::get('/{sortie}', [MagasinController::class, 'showSortie'])->name('show');
        Route::get('/{sortie}/edit', [MagasinController::class, 'editSortie'])->name('edit');
        Route::put('/{sortie}', [MagasinController::class, 'updateSortie'])->name('update');
        Route::delete('/{sortie}', [MagasinController::class, 'destroySortie'])->name('destroy');
    });

    // Alias APRES le groupe pour que route('magasin.sorties') fonctionne dans les vues
    Route::get('/sorties', [MagasinController::class, 'sorties'])->name('sorties');
});

// Routes pour la Boutique (Shop) - Accessibles via /magasin/boutique ou /shop
Route::prefix('magasin/boutique')->name('stock.shop.')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/sales', [ShopController::class, 'sales'])->name('sales');
    Route::post('/sell', [ShopController::class, 'store'])->name('store');
    Route::get('/receipt/{sale}', [ShopController::class, 'receipt'])->name('receipt');
    Route::get('/{sale}/edit', [ShopController::class, 'editSale'])->name('edit');
    Route::put('/{sale}', [ShopController::class, 'updateSale'])->name('update');
});

// API Routes pour la Boutique
Route::prefix('api/shop')->name('stock.api.shop.')->middleware(['web', 'auth'])->group(function () {
    Route::post('/transfer', [ShopController::class, 'transferToShop'])->name('transfer');
    Route::get('/warehouses-with-stock', [ShopController::class, 'getWarehousesWithStock'])->name('warehouses-with-stock');
    Route::get('/check-warehouse-stock', [ShopController::class, 'checkWarehouseStock'])->name('check-warehouse-stock');
    Route::post('/finalize-sale', [ShopController::class, 'finalizeSale'])->name('finalize-sale');
});

// Alias pour compatibilité avec les vues existantes
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::post('/shop/sell', [ShopController::class, 'store'])->name('shop.store');
Route::get('/magasin/boutique/historique', [ShopController::class, 'sales'])->name('magasin.boutique.sales');
