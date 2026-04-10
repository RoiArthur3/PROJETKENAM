<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportingController;

Route::middleware(['auth'])->prefix('reporting')->name('reporting.')->group(function () {
    Route::get('/', [ReportingController::class, 'index'])->name('index');
    Route::get('/dashboard', [ReportingController::class, 'dashboard'])->name('dashboard');
    Route::get('/financier', [ReportingController::class, 'financier'])->name('financier');
    Route::get('/operations', [ReportingController::class, 'operations'])->name('operations');
    Route::get('/performance', [ReportingController::class, 'performance'])->name('performance');
    Route::get('/services', [ReportingController::class, 'services'])->name('services');
    Route::get('/export', [ReportingController::class, 'export'])->name('export');
});
