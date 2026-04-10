<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\PersonnelCongeController;
use App\Http\Controllers\PersonnelPaieController;
use App\Http\Controllers\PersonnelDocumentController;

// Routes pour la gestion du Personnel (séparé des Users système)
// Le préfixe /rh/personnel et le nom personnel. sont définis dans web.php

// Évite les collisions de routes (ex: /rh/paie interprété comme {personnel}).
Route::pattern('personnel', '[0-9]+');

// Dashboard
Route::get('/dashboard', [PersonnelController::class, 'dashboard'])->name('dashboard');

// Index et recherche
Route::get('/', [PersonnelController::class, 'index'])->name('index');
Route::get('/search', [PersonnelController::class, 'search'])->name('search');
Route::get('/export', [PersonnelController::class, 'export'])->name('export');

// CRUD Personnel
Route::get('/create', [PersonnelController::class, 'create'])->name('create');
Route::post('/create-on-camera', [PersonnelController::class, 'createOnCamera'])->name('create-on-camera');

Route::post('/', [PersonnelController::class, 'store'])->name('store');
Route::get('/{personnel}', [PersonnelController::class, 'show'])->name('show');
Route::get('/{personnel}/edit', [PersonnelController::class, 'edit'])->name('edit');
Route::put('/{personnel}', [PersonnelController::class, 'update'])->name('update');
Route::delete('/{personnel}', [PersonnelController::class, 'destroy'])->name('destroy');

// Export et PDF
Route::get('/{personnel}/fiche/pdf', [PersonnelController::class, 'fichePdf'])->name('fiche.pdf');
Route::get('/{personnel}/contrat-pdf', [PersonnelController::class, 'contratPdf'])->name('contrat.pdf');

// Gestion des Congés
Route::get('/{personnel}/conges', [PersonnelCongeController::class, 'index'])->name('personnel.conges');
Route::post('/{personnel}/conges', [PersonnelCongeController::class, 'store'])->name('personnel.conges.store');
Route::delete('/{personnel}/conges/{conge}', [PersonnelCongeController::class, 'destroy'])->name('personnel.conges.destroy');
Route::post('/{personnel}/conges/{conge}/valider', [PersonnelCongeController::class, 'valider'])->name('personnel.conges.valider');

// Gestion des Paies
Route::get('/{personnel}/paies', [PersonnelPaieController::class, 'index'])->name('paies');
Route::post('/{personnel}/paies', [PersonnelPaieController::class, 'store'])->name('paies.store');
Route::post('/{personnel}/paies/{paie}/marquer-paye', [PersonnelPaieController::class, 'marquerPaye'])->name('paies.marquer-paye');
Route::get('/{personnel}/paies/{paie}/bulletin-pdf', [PersonnelPaieController::class, 'bulletinPdf'])->name('paies.bulletin-pdf');
Route::get('/{personnel}/paies/export', [PersonnelPaieController::class, 'export'])->name('paies.export');
Route::delete('/{personnel}/paies/{paie}', [PersonnelPaieController::class, 'destroy'])->name('paies.destroy');

// Gestion des Documents
Route::get('/{personnel}/documents', [PersonnelDocumentController::class, 'index'])->name('personnel.documents');
Route::post('/{personnel}/documents', [PersonnelDocumentController::class, 'store'])->name('personnel.documents.store');
Route::post('/{personnel}/documents/{document}/valider', [PersonnelDocumentController::class, 'valider'])->name('personnel.documents.valider');
Route::get('/{personnel}/documents/{document}/download', [PersonnelDocumentController::class, 'download'])->name('personnel.documents.download');
Route::delete('/{personnel}/documents/{document}', [PersonnelDocumentController::class, 'destroy'])->name('personnel.documents.destroy');

// Gestion des Contrats (Nouveau)
use App\Http\Controllers\PersonnelContratController;
Route::get('/contrats/liste', [PersonnelContratController::class, 'index'])->name('contrats.index');
Route::get('/contrats/create', [PersonnelContratController::class, 'create'])->name('contrats.create');
Route::post('/contrats', [PersonnelContratController::class, 'store'])->name('contrats.store');
Route::get('/contrats/{contrat}', [PersonnelContratController::class, 'show'])->name('contrats.show');
Route::get('/contrats/{contrat}/edit', [PersonnelContratController::class, 'edit'])->name('contrats.edit');
Route::put('/contrats/{contrat}', [PersonnelContratController::class, 'update'])->name('contrats.update');

// Routes administratives (gestion globale)
Route::prefix('admin')->name('admin.')->middleware(['role:admin,superadmin'])->group(function () {
    Route::get('/documents/expiration', [PersonnelDocumentController::class, 'verifierExpiration'])->name('personnel.documents.expiration');
    Route::post('/documents/update-expiration', [PersonnelDocumentController::class, 'mettreAJourExpiration'])->name('personnel.documents.update-expiration');
});
