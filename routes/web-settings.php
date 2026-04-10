<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsController;

// Routes pour le module des paramètres
Route::prefix('settings')->name('settings.')->middleware(['auth'])->group(function () {
    // Tableau de bord des paramètres
    Route::get('/', [SettingsController::class, 'index'])->name('index');

    // Route pour la création de services (POST)
    Route::post('/', [SettingsController::class, 'storeService'])->name('storeService');

    // Gestion des utilisateurs
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [SettingsController::class, 'usersIndex'])->name('index');
        Route::get('/create', [SettingsController::class, 'createUser'])->name('create');
        Route::post('/', [SettingsController::class, 'storeUser'])->name('store');
        Route::get('/{user}/edit', [SettingsController::class, 'editUser'])->name('edit');
        Route::put('/{user}', [SettingsController::class, 'updateUser'])->name('update');
        Route::delete('/{user}', [SettingsController::class, 'destroyUser'])->name('destroy');
    });

    // Rôles et permissions
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [SettingsController::class, 'rolesIndex'])->name('index');
        Route::get('/create', [SettingsController::class, 'createRole'])->name('create');
        Route::post('/', [SettingsController::class, 'storeRole'])->name('store');
        Route::get('/{role}/edit', [SettingsController::class, 'editRole'])->name('edit');
        Route::put('/{role}', [SettingsController::class, 'updateRole'])->name('update');
        Route::delete('/{role}', [SettingsController::class, 'destroyRole'])->name('destroy');
    });

    // Paramètres généraux
    Route::prefix('general')->name('general.')->group(function () {
        Route::get('/', [SettingsController::class, 'generalSettings'])->name('index');
        Route::post('/', [SettingsController::class, 'updateGeneralSettings'])->name('update');
    });

    // Paramètres de l'entreprise
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/', [SettingsController::class, 'companySettings'])->name('index');
        Route::post('/', [SettingsController::class, 'updateCompanySettings'])->name('update');
    });

    // Paramètres de notification
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [SettingsController::class, 'notificationSettings'])->name('index');
        Route::post('/', [SettingsController::class, 'updateNotificationSettings'])->name('update');
    });

    // Gestionnaire de fichiers
    Route::prefix('files')->name('file.')->group(function () {
        Route::get('/', [SettingsController::class, 'fileManager'])->name('manager');
        Route::post('/upload', [SettingsController::class, 'uploadFile'])->name('upload');
        Route::get('/download/{fileId}', [SettingsController::class, 'downloadFile'])->name('download');
        Route::delete('/{fileId}', [SettingsController::class, 'deleteFile'])->name('delete');
    });

    // Gestion des logs système
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [SettingsController::class, 'logsIndex'])->name('index');
        Route::get('/download', [SettingsController::class, 'downloadLogs'])->name('download');
        Route::get('/clear', [SettingsController::class, 'clearLogs'])->name('clear');
        Route::post('/cleanup', [SettingsController::class, 'cleanupLogs'])->name('cleanup');
    });

    // Nettoyage des données par date
    Route::prefix('cleanup')->name('cleanup.')->group(function () {
        Route::get('/', [SettingsController::class, 'cleanupIndex'])->name('index');
        Route::post('/preview', [SettingsController::class, 'previewCleanup'])->name('preview');
        Route::post('/execute', [SettingsController::class, 'executeCleanup'])->name('execute');
    });
});

// Services opérationnels (admin)
Route::prefix('admin/services')->name('admin.services.')->middleware(['auth', 'web'])->group(function () {
    Route::get('/', [\App\Http\Controllers\ServiceOperationnelController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\ServiceOperationnelController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\ServiceOperationnelController::class, 'store'])->name('store');
    Route::get('/{service}/edit', [\App\Http\Controllers\ServiceOperationnelController::class, 'edit'])->name('edit');
    Route::put('/{service}', [\App\Http\Controllers\ServiceOperationnelController::class, 'update'])->name('update');
    Route::delete('/{service}', [\App\Http\Controllers\ServiceOperationnelController::class, 'destroy'])->name('destroy');
});
