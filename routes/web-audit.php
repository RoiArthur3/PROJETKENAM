<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuditController;

Route::prefix('audit')->name('audit.')->group(function () {
    Route::get('/', [AuditController::class, 'index'])->name('index');
    Route::get('/dashboard', [AuditController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports', [AuditController::class, 'reports'])->name('reports');
    // Alias pour les routes utilisées dans la vue
    Route::get('/controles', [AuditController::class, 'index'])->name('controles');
    Route::get('/rapports', [AuditController::class, 'reports'])->name('rapports');
    Route::get('/alertes', [AuditController::class, 'dashboard'])->name('alertes');
    Route::get('/historique', [AuditController::class, 'historique'])->name('historique');
    
    Route::get('/{audit}', [AuditController::class, 'show'])->name('show');
});
