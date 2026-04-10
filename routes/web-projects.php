<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::prefix('projets')->name('projets.')->middleware(['auth'])->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/dashboard', [ProjectController::class, 'dashboard'])->name('dashboard');
    Route::get('/list', [ProjectController::class, 'list'])->name('list');
    Route::get('/create', [ProjectController::class, 'create'])->name('create');
    Route::post('/', [ProjectController::class, 'store'])->name('store');
    Route::get('/reports', [ProjectController::class, 'dashboard'])->name('reports');
    Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
    Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
    Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
    Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
    Route::post('/{project}/validate-project', [ProjectController::class, 'validateProject'])->name('validateProject');
    Route::post('/{project}/start-project', [ProjectController::class, 'startProject'])->name('startProject');
    Route::post('/{project}/close-project', [ProjectController::class, 'closeProject'])->name('closeProject');
    Route::post('/{project}/archive-project', [ProjectController::class, 'archiveProject'])->name('archiveProject');
    Route::post('/{project}/update-progress', [ProjectController::class, 'updateProgress'])->name('updateProgress');
});
