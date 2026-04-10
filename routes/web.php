<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\EmailTestController;
use App\Http\Controllers\PendingValidationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParametrageController;
use App\Http\Controllers\FacialRecognitionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApprovisionnementCaisseController;
use App\Http\Controllers\ApprovisionnementDemandeController;
use App\Http\Controllers\DepenseCaisseController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\RecetteController;
use App\Http\Controllers\CommercialController;
use App\Http\Controllers\ComptabiliteController;
use App\Http\Controllers\TresorerieController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\RHController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AdminPermissionController;
use App\Http\Controllers\ModeratorPermissionController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseDashboardController;
use App\Http\Controllers\ParcController;
use App\Http\Controllers\ParcDashboardController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\MagasinController;
use App\Http\Controllers\VehicleAssignmentController;
use App\Http\Controllers\VehicleMissionController;
use App\Http\Controllers\VehicleIncidentController;
use App\Http\Controllers\ServiceOperationnelController;
use App\Http\Controllers\TypeOperationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AnalysesController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\CommandeFournisseurController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\FactureFournisseurController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\ControleurController;
use App\Http\Controllers\CentralValidationController;
use App\Http\Controllers\OperationValidationController;
use App\Http\Controllers\RoleBasedController;
use App\Http\Controllers\PlanificationController;
use App\Http\Controllers\EvaluationFournisseurController;
use App\Http\Controllers\LivraisonFournisseurController;
use App\Http\Controllers\DocumentFournisseurController;
use App\Http\Controllers\ContratFournisseurController;
use App\Http\Controllers\PaiementFournisseurController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AccountingExportController;
use App\Http\Controllers\EcritureComptableController;
use App\Http\Controllers\CnssController;
use App\Http\Controllers\ImpotRevenuController;
use App\Http\Controllers\EtatFinancierController;
use App\Http\Controllers\FactureComptableController;
use App\Http\Controllers\OperationsToEcrituresController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAgentController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ServicePersonneRessourceController;
use App\Http\Controllers\ServiceResponsableController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ParcAssuranceController;
use App\Http\Controllers\ParcHistoriqueController;
use App\Http\Controllers\ReparationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\StockEntreeController;
use App\Http\Controllers\StockSortieController;
use App\Http\Controllers\StockTransfertController;
use App\Http\Controllers\InventaireController;
use App\Http\Controllers\ProjetsDashboardController;
use App\Http\Controllers\EntrepotController;
use App\Http\Controllers\TestEmailController;
use App\Http\Controllers\RapportsController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ConformiteController;
use App\Http\Controllers\ControleAuditController;
use App\Http\Controllers\DocumentLegauxController;
use App\Http\Controllers\FacturationController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\EntretienController;
use App\Http\Controllers\Materiel\MaterielController;
use App\Http\Controllers\OperationCostController;
use App\Http\Controllers\OperationRequeteController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\ChrononomiqueCostControlController;
use App\Http\Controllers\PneumatiqueController;
use App\Http\Controllers\ServiceAuthController;
use App\Http\Controllers\TempAdminController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserPermissionTestController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\HistoricalImportController;
use App\Http\Controllers\ExportController;

// Page d'accueil - redirection vers login
Route::get('/', function () {
    return redirect('/login');
})->name('home');

// Routes d'authentification
Route::get('/login', [App\Http\Controllers\Auth\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login.submit');
Route::get('/phone-login', [App\Http\Controllers\Auth\AuthController::class, 'showLoginForm'])->name('phone.login');
Route::post('/phone-login', [App\Http\Controllers\Auth\AuthController::class, 'login'])->name('phone.login.submit');
Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

// Route AJAX fournisseur engins
Route::get('/ajax/fournisseur-engins', [App\Http\Controllers\AjaxFournisseurEnginsController::class, 'getEngins'])->name('ajax.fournisseur.engins');

// Téléchargement sécurisé des pièces jointes
Route::get('/operations/files/{id}/download', [App\Http\Controllers\OperationController::class, 'downloadFile'])->name('operations.files.download');

// Lien public pour marquer une opération comme payée
Route::get('/operations/{operationId}/mark-paid', [\App\Http\Controllers\OperationController::class, 'markAsPaidByLink'])->name('operations.markAsPaidByLink');

// Liens email signes pour le workflow d'approvisionnement (hors authentification).
Route::get('/approvisionnement-demandes/{demande}/email/forward-dg', [\App\Http\Controllers\ApprovisionnementDemandeController::class, 'emailForwardToDg'])
    ->middleware('signed')
    ->name('approvisionnement-demandes.email.forward');

Route::get('/approvisionnement-demandes/{demande}/email/approve-dg', [\App\Http\Controllers\ApprovisionnementDemandeController::class, 'emailApproveByDg'])
    ->middleware('signed')
    ->name('approvisionnement-demandes.email.approve-dg');

// Route pour servir les fichiers du stockage public
Route::get('/files/{path}', function ($path) {
    $disk = Storage::disk('public');
    if (!$disk->exists($path)) {
        abort(404, 'Fichier introuvable');
    }
    $fullPath = $disk->path($path);
    $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';
    $fileName = basename($path);
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . $fileName . '"',
    ]);
})->where('path', '.*')->name('storage.file');

// Endpoint webhook terminal facial (sans CSRF)
Route::post('/facial/events/ingest', [App\Http\Controllers\Api\FacialAttendanceIngestController::class, 'store'])
    ->withoutMiddleware([App\Http\Middleware\VerifyCsrfToken::class])
    ->middleware('throttle:120,1')
    ->name('facial.events.ingest');

// Routes protégées
Route::middleware(['auth'])->group(function () {

    // Espace profil utilisateur
    Route::get('/mon-profil', [ProfileController::class, 'dashboard'])->name('profile.dashboard');
    Route::get('/profile', [ProfileController::class, 'dashboard'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');

    // Préférence UI: vue sidebar par métier (activable pour admin)
    Route::get('/ui/sidebar-view/{mode}', function (string $mode) {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'superadmin'], true)) {
            abort(403);
        }

        if (!in_array($mode, ['metier', 'classic'], true)) {
            abort(404);
        }

        // Superadmin: toujours en vue métier
        if ($user->role === 'superadmin') {
            session(['sidebar_grouped_by_metier' => true]);
            return redirect()->back();
        }

        session(['sidebar_grouped_by_metier' => $mode === 'metier']);

        return redirect()->back();
    })->name('ui.sidebar-view');

    // Module Paramétrage
    Route::prefix('parametrage')->name('parametrage.')->group(function () {
        Route::get('/', [ParametrageController::class, 'index'])->name('index');
        Route::get('/entreprise', [ParametrageController::class, 'entreprise'])->name('entreprise');
        Route::post('/entreprise', [ParametrageController::class, 'saveEntreprise'])->name('entreprise.save');
        Route::get('/email', [ParametrageController::class, 'email'])->name('email');
        Route::post('/email', [ParametrageController::class, 'saveEmail'])->name('email.save');
        Route::post('/email/test', [ParametrageController::class, 'testEmail'])->name('email.test');
        Route::get('/general', [ParametrageController::class, 'general'])->name('general');
        Route::post('/general', [ParametrageController::class, 'saveGeneral'])->name('general.save');
        Route::get('/systeme', [ParametrageController::class, 'systeme'])->name('systeme');
        Route::post('/systeme', [ParametrageController::class, 'saveSysteme'])->name('systeme.save');
        Route::post('/sms', [ParametrageController::class, 'saveSms'])->name('sms.save');
        Route::get('/services', [ParametrageController::class, 'services'])->name('services');
        Route::post('/services', [ParametrageController::class, 'saveServices'])->name('services.save');
        Route::post('/facial_recognition/save', [FacialRecognitionController::class, 'saveConfig'])->name('facial_recognition.save');
        Route::post('/facial_recognition/test_camera', [FacialRecognitionController::class, 'testCamera'])->name('facial_recognition.test_camera');
        Route::post('/facial_recognition/test_recognition', [FacialRecognitionController::class, 'testRecognition'])->name('facial_recognition.test_recognition');
        Route::post('/facial_recognition/diagnostic', [FacialRecognitionController::class, 'diagnostic'])->name('facial_recognition.diagnostic');
        Route::post('/facial_recognition/calibrate', [FacialRecognitionController::class, 'calibrate'])->name('facial_recognition.calibrate');
        Route::get('/facial_recognition/stats', [FacialRecognitionController::class, 'getStats'])->name('facial_recognition.stats');
        Route::get('/facial_recognition/devices', [FacialRecognitionController::class, 'devices'])->name('facial_recognition.devices');
        Route::post('/facial_recognition/devices', [FacialRecognitionController::class, 'saveDevice'])->name('facial_recognition.devices.save');
        Route::delete('/facial_recognition/devices/{device}', [FacialRecognitionController::class, 'deleteDevice'])->name('facial_recognition.devices.delete');
        Route::post('/facial_recognition/devices/{device}/rotate-token', [FacialRecognitionController::class, 'rotateDeviceToken'])->name('facial_recognition.devices.rotate_token');
        Route::post('/facial_recognition/devices/{device}/test', [FacialRecognitionController::class, 'testDevice'])->name('facial_recognition.devices.test');
        Route::get('/facial_recognition/events', [FacialRecognitionController::class, 'recentEvents'])->name('facial_recognition.events');

        // Configuration Hikvision
        Route::get('/hikvision/config', [ParametrageController::class, 'hikvisionConfig'])->name('hikvision.config');
        Route::post('/hikvision/config', [ParametrageController::class, 'saveHikvisionConfig'])->name('hikvision.save');
        
        // Routes Hikvision (Paramétrage et Diagnostic)
        Route::get('/hikvision/test', [App\Http\Controllers\ParametrageController::class, 'testHikvision'])->name('hikvision.test');
        Route::get('/hikvision/diagnostic', [App\Http\Controllers\ParametrageController::class, 'hikvisionDiagnostic'])->name('hikvision.diagnostic');
        Route::get('/hikvision/diagnostic/run', [App\Http\Controllers\ParametrageController::class, 'runHikvisionDiagnostic'])->name('hikvision.diagnostic.run');
        Route::post('/hikvision/save', [App\Http\Controllers\ParametrageController::class, 'saveHikvisionConfig'])->name('hikvision.save');
        
        // Route pour la synchronisation manuelle
        Route::post('/hikvision/sync', [App\Http\Controllers\ParametrageController::class, 'hikvisionSync'])->name('hikvision.sync');
    });

    // Surveillance et Status (Hors du groupe parametrage)
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/hikvision/live', [App\Http\Controllers\FacialPointageController::class, 'live'])->name('hikvision.live');
        Route::get('/hikvision/snapshot', [App\Http\Controllers\ParametrageController::class, 'proxySnapshot'])->name('hikvision.snapshot');
        Route::get('/hikvision/status', [App\Http\Controllers\ParametrageController::class, 'getDeviceStatus'])->name('hikvision.status');
    });

    // Alias Hikvision (compatibilité URL directe /hikvision/...)
    Route::prefix('hikvision')->group(function () {
        Route::get('/config', [ParametrageController::class, 'hikvisionConfig'])->name('hikvision.config');
        Route::post('/config', [ParametrageController::class, 'saveHikvisionConfig'])->name('hikvision.config.save');
        Route::post('/test', [ParametrageController::class, 'testHikvision'])->name('hikvision.test');
        Route::get('/diagnostic', [ParametrageController::class, 'hikvisionDiagnostic'])->name('hikvision.diagnostic');
        Route::post('/diagnostic', [ParametrageController::class, 'runHikvisionDiagnostic'])->name('hikvision.diagnostic.run');
        Route::post('/sync', [ParametrageController::class, 'hikvisionSync'])->name('hikvision.sync');
    });

    // Types d'opérations
    Route::prefix('types-operations')->name('types-operations.')->group(function () {
        Route::get('/', [TypeOperationController::class, 'index'])->name('index');
        Route::get('/create', [TypeOperationController::class, 'create'])->name('create');
        Route::post('/', [TypeOperationController::class, 'store'])->name('store');
        Route::get('/{typeOperation}', [TypeOperationController::class, 'show'])->name('show');
        Route::get('/{typeOperation}/edit', [TypeOperationController::class, 'edit'])->name('edit');
        Route::put('/{typeOperation}', [TypeOperationController::class, 'update'])->name('update');
        Route::delete('/{typeOperation}', [TypeOperationController::class, 'destroy'])->name('destroy');
    });

    // Module Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [ParametrageController::class, 'index'])->name('index');
        Route::get('/company', [ParametrageController::class, 'entreprise'])->name('company.index');
        Route::post('/company', [ParametrageController::class, 'saveEntreprise'])->name('company.update');
        Route::get('/users', function() { return redirect()->route('admin.comptes.users.index'); })->name('users.index');
        Route::get('/users/create', function() { return redirect()->route('admin.comptes.users.create'); })->name('users.create');
        Route::get('/admin/users', function() { return redirect()->route('admin.comptes.users.index'); })->name('admin.users');
        Route::get('/roles', function() { return redirect()->route('admin.permissions'); })->name('roles.index');
        Route::get('/roles/create', function() { return redirect()->route('main.permissions.create'); })->name('roles.create');
    });

    // Alias legacy: conserve les noms de routes admin.comptes.users.* attendus par les vues.
    Route::prefix('admin/comptes')->name('admin.comptes.')->group(function () {
        Route::get('/users', [CompteController::class, 'index'])->name('users.index');
        Route::get('/users/create', [CompteController::class, 'create'])->name('users.create');
        Route::post('/users', [CompteController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}', [CompteController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [CompteController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [CompteController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [CompteController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/{user}/submodules', [UserController::class, 'editSubmodules'])->name('users.submodules.edit');
        Route::put('/users/{user}/submodules', [UserController::class, 'updateSubmodules'])->name('users.submodules.update');
    });

    // Alias legacy: conserve les noms de routes admin.users.* utilisés par les vues admin.
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [CompteController::class, 'index'])->name('users.index');
        Route::get('/users/create', [CompteController::class, 'create'])->name('users.create');
        Route::post('/users', [CompteController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [CompteController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [CompteController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [CompteController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [CompteController::class, 'destroy'])->name('users.destroy');
    });

    // Routes pour la gestion du Personnel RH
    Route::get('personnel', function () {
        return redirect()->route('rh.personnel.index');
    })->name('personnel.index');

    Route::get('personnel/export', function () {
        return redirect()->route('rh.personnel.export');
    })->name('personnel.export');

    Route::prefix('rh/personnel')->name('rh.personnel.')->middleware(['auth'])->group(function () {
        require __DIR__.'/personnel.php';
    });

    // Global Dashboard
    Route::get('/dashboard-global', [DashboardController::class, 'globalDashboard'])->name('dashboard.global');

    // Operations routes
    Route::prefix('operations')->name('operations.')->middleware('module:operations')->group(function () {

        // Routes de validation (legacy)
        Route::prefix('valider')->name('valider.')->group(function () {
            Route::get('/', [OperationController::class, 'valider'])->name('index');
            Route::post('/{id}/approve', [OperationController::class, 'approveOperation'])->name('approve');
            Route::post('/{id}/reject', [OperationController::class, 'rejectOperation'])->name('reject');
        });

        // Routes des opérations validées
        Route::prefix('validated')->name('validated.')->group(function () {
            Route::get('/', [OperationController::class, 'validated'])->name('index');
            Route::get('/{id}', [OperationController::class, 'showValidated'])->name('show');
        });

        // Routes des validations
        Route::prefix('validations')->name('validations.')->group(function () {
            Route::get('/', [ValidationController::class, 'index'])->name('index');
            Route::get('/pending', [PendingValidationController::class, 'pending'])->name('pending');
            Route::get('/approved', [PendingValidationController::class, 'approved'])->name('approved');
            Route::get('/rejected', [PendingValidationController::class, 'rejected'])->name('rejected');
            Route::get('/history', [PendingValidationController::class, 'history'])->name('history');
            Route::get('/{id}', [ValidationController::class, 'show'])->name('show');
        });

        Route::get('/history', [PendingValidationController::class, 'history'])->name('history');

        // Routes d'exécution
        Route::get('/{operation}/execution', [OperationController::class, 'execution'])->name('operations.execution');
        Route::get('/{operation}/controle', [OperationController::class, 'controle'])->name('operations.controle');
        Route::get('/{operation}/couts', [OperationController::class, 'couts'])->name('operations.couts');

        // Admin permissions
        Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
            Route::resource('users', CompteController::class)->parameters(['users' => 'user']);
            Route::post('/users/store', [CompteController::class, 'store'])->name('admin.users.store.compat');
            Route::put('/users/{user}/update', [CompteController::class, 'updateUser'])->name('admin.users.update.compat');

            Route::prefix('comptes')->name('comptes.')->group(function() {
                Route::get('/', [CompteController::class, 'index'])->name('index');
                Route::get('/create', [CompteController::class, 'create'])->name('create');
                Route::post('/', [CompteController::class, 'store'])->name('store');

                Route::prefix('users')->name('users.')->group(function() {
                    Route::get('/', [CompteController::class, 'index'])->name('index');
                    Route::get('/create', [CompteController::class, 'create'])->name('create');
                    Route::post('/', [CompteController::class, 'storeUser'])->name('store');
                    Route::get('/{user}/edit', [CompteController::class, 'edit'])->name('edit');
                    Route::put('/{user}', [CompteController::class, 'updateUser'])->name('update');
                    Route::delete('/{user}', [CompteController::class, 'destroy'])->name('destroy');
                    Route::get('/{user}/submodules', [UserController::class, 'editSubmodules'])->name('submodules.edit');
                    Route::put('/{user}/submodules', [UserController::class, 'updateSubmodules'])->name('submodules.update');
                });
            });
        });

        // Routes Magasin
        Route::prefix('magasin')->name('magasin.')->middleware(['web', 'auth', 'module:magasin'])->group(function () {
            Route::get('/', [MagasinController::class, 'index'])->name('index');
            Route::get('/dashboard', [MagasinController::class, 'dashboard'])->name('dashboard');
            Route::get('/inventaire', [MagasinController::class, 'inventaire'])->name('inventaire');
            Route::get('/entrees', [MagasinController::class, 'entrees'])->name('entrees');
            Route::get('/entrees/create', [MagasinController::class, 'createEntree'])->name('entrees.create');
            Route::post('/entrees', [MagasinController::class, 'storeEntree'])->name('entrees.store');
            Route::get('/sorties', [MagasinController::class, 'sorties'])->name('sorties');
            Route::get('/sorties/index', [MagasinController::class, 'sorties'])->name('sorties.index');
            Route::get('/sorties/create', [MagasinController::class, 'createSortie'])->name('sorties.create');
            Route::post('/sorties', [MagasinController::class, 'storeSortie'])->name('sorties.store');
            Route::get('/rapports', [MagasinController::class, 'rapports'])->name('rapports');
            Route::get('/produits/create', [MagasinController::class, 'createProduit'])->name('produits.create');
        });

        // Approvisionnements de caisse
        Route::prefix('approvisionnements')->name('approvisionnements.')->middleware(['auth', 'module:tresorerie'])->group(function () {
            Route::get('/', [ApprovisionnementCaisseController::class, 'index'])->name('index');
            Route::get('/create', [ApprovisionnementCaisseController::class, 'create'])->name('create');
            Route::get('/create-simple', [ApprovisionnementCaisseController::class, 'createSimple'])->name('create-simple');
            Route::post('/', [ApprovisionnementCaisseController::class, 'store'])->name('store');
            Route::post('/simple', [ApprovisionnementCaisseController::class, 'storeSimple'])->name('store-simple');
            Route::get('/{approvisionnement}', [ApprovisionnementCaisseController::class, 'show'])->name('show');
        });

    }); // Fin du groupe operations

    // Dashboard principal (accessible globalement)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

    // Import historique Excel (chargement de donnees passees)
    Route::prefix('imports')->name('imports.')->group(function () {
        Route::get('/historique', [HistoricalImportController::class, 'index'])->name('historique.index');
        Route::post('/historique', [HistoricalImportController::class, 'store'])->name('historique.store');
        Route::get('/historique/template/{entity}', [HistoricalImportController::class, 'template'])->name('historique.template');
        Route::get('/audit', [HistoricalImportController::class, 'audit'])->name('audit');
    });

    // Exports (Excel, CSV, etc)
    Route::prefix('exports')->name('exports.')->group(function () {
        Route::get('/clients', [ExportController::class, 'exportClients'])->name('clients');
        Route::get('/csv/{table}', [ExportController::class, 'exportTableCsv'])->name('csv.table');

        // Raccourcis metier
        Route::get('/creances/clients.csv', [ExportController::class, 'exportTableCsv'])
            ->defaults('table', 'clients')
            ->name('creances.clients.csv');
        Route::get('/creances/factures.csv', [ExportController::class, 'exportTableCsv'])
            ->defaults('table', 'factures')
            ->name('creances.factures.csv');
        Route::get('/creances/encaissements.csv', [ExportController::class, 'exportTableCsv'])
            ->defaults('table', 'encaissements')
            ->name('creances.encaissements.csv');
        Route::get('/caisse/caisses.csv', [ExportController::class, 'exportTableCsv'])
            ->defaults('table', 'caisses')
            ->name('caisse.caisses.csv');
        Route::get('/caisse/mouvements.csv', [ExportController::class, 'exportTableCsv'])
            ->defaults('table', 'mouvements_caisse')
            ->name('caisse.mouvements.csv');

        Route::get('/dettes/fournisseurs.csv', [ExportController::class, 'exportTableCsv'])
            ->defaults('table', 'fournisseurs')
            ->name('dettes.fournisseurs.csv');
        Route::get('/dettes/commandes-fournisseurs.csv', [ExportController::class, 'exportTableCsv'])
            ->defaults('table', 'commande_fournisseurs')
            ->name('dettes.commandes_fournisseurs.csv');
        Route::get('/dettes/factures-fournisseurs.csv', [ExportController::class, 'exportTableCsv'])
            ->defaults('table', 'facture_fournisseurs')
            ->name('dettes.factures_fournisseurs.csv');
        Route::get('/dettes/paiements-fournisseurs.csv', [ExportController::class, 'exportTableCsv'])
            ->defaults('table', 'paiement_fournisseurs')
            ->name('dettes.paiements_fournisseurs.csv');

        Route::get('/param-compta/comptes.csv', [ExportController::class, 'exportTableCsv'])
            ->defaults('table', 'comptes_comptables')
            ->name('param_compta.comptes.csv');
        Route::get('/param-compta/journaux.csv', [ExportController::class, 'exportTableCsv'])
            ->defaults('table', 'journal_comptables')
            ->name('param_compta.journaux.csv');
        Route::get('/param-compta/projets.csv', [ExportController::class, 'exportTableCsv'])
            ->defaults('table', 'projets')
            ->name('param_compta.projets.csv');
    });

    // Modules complémentaires (inclus EN DEHORS du groupe operations)
    $modules = [
        '/web-materiel.php',
        '/web-fleet.php',
        '/web-entrepots.php',
        '/web-projects.php',
        '/web-services.php',
        '/tresorerie.php',
        '/web-comptabilite.php',
        '/web-rh.php',
        '/web-commercial.php',
        '/web-magasin.php',
        '/web-stock.php',
        '/web-achat.php',
        '/web-fournisseurs.php',
        '/web-audit.php',
        '/web-analyses.php',
        '/web-reporting.php',
        '/web-settings.php',
        '/web-operations.php',
        '/web-validation-corrected.php',
        '/web-juridique.php',
    ];

    foreach ($modules as $module) {
        if (file_exists(__DIR__ . $module)) {
            require __DIR__ . $module;
        }
    }

    // Alias de compatibilité pour le plan comptable SYSCOHADA
    Route::get('/syscohada', function () {
        return redirect('/comptabilite/syscohada');
    });
    Route::get('/syscoada', function () {
        return redirect('/comptabilite/syscohada');
    });
    Route::get('/syscohada/search', function () {
        return redirect('/comptabilite/syscohada/search');
    });
    Route::get('/syscoada/search', function () {
        return redirect('/comptabilite/syscohada/search');
    });

}); // Fin du groupe auth
