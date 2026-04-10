<?php

use App\Http\Controllers\ServicesController;
use App\Http\Controllers\ServiceEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
Routes pour les Services Opérationnels - Version Simplifiée
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Routes principales des services
    Route::prefix('services')->name('services.')->group(function () {

        // Index - Liste des services
        Route::get('/', [ServicesController::class, 'index'])
            ->name('index');

        // Create - Formulaire de création
        Route::get('/create', [ServicesController::class, 'create'])
            ->name('create');

        // Store - Enregistrement d'un service
        Route::post('/', [ServicesController::class, 'store'])
            ->name('store');

        // Show - Détail d'un service
        Route::get('/{service}', [ServicesController::class, 'show'])
            ->name('show');

        // Edit - Formulaire de modification
        Route::get('/{service}/edit', [ServicesController::class, 'edit'])
            ->name('edit');

        // Update - Mise à jour du service
        Route::put('/{service}', [ServicesController::class, 'update'])
            ->name('update');

        // Destroy - Supprimer un service
        Route::delete('/{service}', [ServicesController::class, 'destroy'])
            ->name('destroy');

        // Routes pour les emails
        Route::prefix('email')->name('email.')->group(function () {
            Route::get('/create', [ServiceEmailController::class, 'create'])
                ->name('create');
            Route::post('/send', [ServiceEmailController::class, 'send'])
                ->name('send');
            Route::get('/api/services', [ServiceEmailController::class, 'apiServices'])
                ->name('api.services');
        });

        // Route de test d'intégration
        Route::get('/test-integration', function() {
            return view('services.test-integration');
        })->name('test-integration');
    });
});

// Routes API pour les services
Route::prefix('api/services')->name('api.services.')->group(function() {
    Route::get('/', [App\Http\Controllers\Api\ServiceController::class, 'index']);
    Route::get('/{id}', [App\Http\Controllers\Api\ServiceController::class, 'show']);
});
