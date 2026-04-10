<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserPermissionTestController;

// Routes pour tester les permissions (temporaire, à supprimer en production)
Route::middleware(['auth', 'check.admin:admin'])->prefix('test-permissions')->name('test.permissions.')->group(function () {
    Route::get('/', [UserPermissionTestController::class, 'index'])->name('index');
    Route::post('/create-users', [UserPermissionTestController::class, 'createTestUsers'])->name('create-users');
    Route::get('/test/{user}', [UserPermissionTestController::class, 'testUser'])->name('test-user');
});
