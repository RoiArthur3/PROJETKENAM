<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RHController;
use App\Http\Controllers\FournisseurController;
// use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\PaymentController; // Contrôleur non existant - à créer si nécessaire
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\TypeOperationController;
use App\Http\Controllers\VehicleAssignmentController;
use App\Http\Controllers\VehicleMissionController;
use App\Http\Controllers\VehicleIncidentController;
use App\Http\Controllers\Api\HikvisionEventController;
use App\Http\Controllers\Api\HikvisionSyncController;
use App\Http\Controllers\Api\HikvisionConfigController;
use App\Http\Controllers\Api\FacialRecognitionController;
use App\Http\Controllers\ReparationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\CommandeFournisseurController;
use App\Http\Controllers\LivraisonFournisseurController;
use App\Http\Controllers\EvaluationFournisseurController;
use App\Http\Controllers\ContratFournisseurController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\Commercial\ProspectController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FacialAttendanceIngestController;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// // Charger les routes API v1
// require __DIR__.'/api/v1.php';

Route::post('/facial/events', [FacialAttendanceIngestController::class, 'store'])->middleware('throttle:120,1');

// Webhook email entrant: creation automatique de demande d'approvisionnement.
Route::post('/approvisionnement-demandes/email-reception', [\App\Http\Controllers\ApprovisionnementDemandeController::class, 'receiveFromEmail'])
    ->middleware('throttle:30,1');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API pour récupérer les utilisateurs d'un service
Route::middleware('auth:sanctum')->get('/services/{service}/users', function (Service $service) {
    return $service->users()->select('id', 'name', 'email')->get();
});

// ===== API EMPLOYEES (Search & Get) =====
Route::middleware('auth:sanctum')->prefix('employees')->group(function () {
    Route::get('/search', [\App\Http\Controllers\Api\EmployeeController::class, 'search']);
    Route::get('/{employee}', [\App\Http\Controllers\Api\EmployeeController::class, 'show']);
});

// Routes protégées par authentification
Route::middleware(['auth:sanctum'])->group(function () {

    // ===== MODULE HIKVISION =====
    Route::prefix('hikvision')->group(function () {
        // Routes existantes
        Route::get('/events/{deviceId}', [HikvisionEventController::class, 'getEvents']);
        Route::post('/subscribe/{deviceId}', [HikvisionEventController::class, 'subscribe']);
        Route::get('/test/{deviceId}', [HikvisionEventController::class, 'testConnection']);

        // Routes de synchronisation
        Route::post('/sync-employees', [HikvisionSyncController::class, 'syncEmployees']);
        Route::post('/sync-all-employees', [HikvisionSyncController::class, 'syncAllEmployees']);
        Route::post('/test-photo', [HikvisionSyncController::class, 'testPhoto']);

        // Routes de configuration
        Route::post('/test-connection', [HikvisionConfigController::class, 'testConnection']);
        Route::post('/test-env', [HikvisionConfigController::class, 'testEnv']);
        Route::post('/fetch-events', [HikvisionConfigController::class, 'fetchEvents']);
        Route::post('/fetch-events-env', [HikvisionConfigController::class, 'fetchEventsEnv']);
        Route::post('/save-config', [HikvisionConfigController::class, 'saveConfig']);
    });

    // ===== MODULE FACIAL RECOGNITION =====
    Route::prefix('facial-recognition')->group(function () {
        Route::post('/record', [FacialRecognitionController::class, 'record']);
        Route::post('/sync-to-camera', [FacialRecognitionController::class, 'syncToCamera']);
        Route::get('/records', [FacialRecognitionController::class, 'getRecords']);
        Route::post('/sync-pending', [FacialRecognitionController::class, 'syncPendingRecords']);
        Route::get('/stats', [FacialRecognitionController::class, 'getStats']);
    });

    // ===== MODULE TABLEAU DE BORD =====
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/stats', [DashboardController::class, 'stats']);
        Route::get('/kpis', [DashboardController::class, 'kpis']);
        Route::get('/charts/{type}', [DashboardController::class, 'chart']);
    });

    // ===== MODULE OPÉRATIONS =====
    Route::prefix('operations')->group(function () {
        Route::get('/', [OperationController::class, 'index']);
        Route::post('/', [OperationController::class, 'store']);
        Route::get('/{operation}', [OperationController::class, 'show']);
        Route::put('/{operation}', [OperationController::class, 'update']);
        Route::delete('/{operation}', [OperationController::class, 'destroy']);

        // Statuts et workflow
        Route::post('/{operation}/status', [OperationController::class, 'updateStatus']);
        Route::get('/{operation}/history', [OperationController::class, 'history']);

        // Affectations
        Route::post('/{operation}/assign-vehicle', [OperationController::class, 'assignVehicle']);
        Route::post('/{operation}/assign-staff', [OperationController::class, 'assignStaff']);
        Route::delete('/{operation}/assignments/{assignment}', [OperationController::class, 'removeAssignment']);
    });

    // ===== MODULE PARC AUTO =====
    Route::prefix('fleet')->name('api.fleet.')->group(function () {
        Route::apiResource('vehicles', VehiculeController::class);
        Route::get('/vehicles/{vehicle}/availability', [VehiculeController::class, 'checkAvailability']);
        Route::post('/vehicles/{vehicle}/maintenance', [VehiculeController::class, 'scheduleMaintenance']);

        // Missions et affectations
        Route::apiResource('missions', VehicleMissionController::class);
        Route::apiResource('assignments', VehicleAssignmentController::class);

        // Incidents et réparations
        Route::apiResource('incidents', VehicleIncidentController::class);
        Route::apiResource('repairs', ReparationController::class);
    });

    // ===== MODULE ENTREPÔT & STOCK =====
    Route::prefix('warehouse')->name('api.warehouse.')->group(function () {
        Route::apiResource('warehouses', WarehouseController::class);
        Route::apiResource('products', ProductController::class);

        // Mouvements de stock
        Route::post('/stock/entries', [StockController::class, 'createEntry']);
        Route::post('/stock/exits', [StockController::class, 'createExit']);
        Route::post('/stock/transfers', [StockController::class, 'transferStock']);
        Route::get('/stock/movements', [StockController::class, 'movements']);

        // Niveaux de stock
        Route::get('/stock/levels', [StockController::class, 'stockLevels']);
        Route::get('/stock/alerts', [StockController::class, 'lowStockAlerts']);
    });

    // ===== MODULE RESSOURCES HUMAINES =====
    Route::prefix('hr')->name('api.hr.')->group(function () {
        Route::apiResource('agents', RHController::class);
        Route::get('/agents/{agent}/operations', [RHController::class, 'agentOperations']);
        Route::post('/pointages', [RHController::class, 'createPointage']);
        Route::get('/pointages/today', [RHController::class, 'todayPointages']);

        // Congés et absences
        Route::apiResource('conges', CongeController::class);
        Route::apiResource('pointages', AttendanceController::class);
    });

    // ===== MODULE COMMERCIAL =====
    Route::prefix('commercial')->name('api.commercial.')->group(function () {
        // Route::apiResource('clients', ClientController::class);
        Route::apiResource('prospects', ProspectController::class);
        Route::apiResource('devis', DevisController::class);

        // Contrats et opportunités
        Route::apiResource('contrats', ContratController::class);
        // Route::get('/pipeline', [ClientController::class, 'pipeline']);
    });

    // ===== MODULE FOURNISSEURS =====
    Route::prefix('suppliers')->name('api.suppliers.')->group(function () {
        Route::apiResource('fournisseurs', FournisseurController::class);
        Route::apiResource('commandes', CommandeFournisseurController::class);
        Route::apiResource('livraisons', LivraisonFournisseurController::class);

        // Évaluations et contrats
        Route::apiResource('evaluations', EvaluationFournisseurController::class);
        Route::apiResource('contrats-fournisseurs', ContratFournisseurController::class);
    });

    // ===== MODULE COMPTABILITÉ =====
    Route::prefix('accounting')->name('api.accounting.')->group(function () {
        Route::apiResource('invoices', InvoiceController::class);
        Route::apiResource('expenses', ExpenseController::class);
        // Route::apiResource('payments', PaymentController::class); // Contrôleur non existant - à décommenter quand créé

        // Validation et approbation
        Route::post('/invoices/{invoice}/validate', [InvoiceController::class, 'validate']);
        Route::post('/expenses/{expense}/approve', [ExpenseController::class, 'approve']);

        // États financiers
        Route::get('/balance', [AccountingController::class, 'balance']);
        Route::get('/reports/{type}', [AccountingController::class, 'report']);
    });

    // ===== MODULE DÉPENSES =====
    Route::prefix('expenses')->group(function () {
        // Lister toutes les dépenses (avec filtres)
        Route::get('/', [ExpenseController::class, 'index']);

        // Créer une nouvelle dépense
        Route::post('/', [ExpenseController::class, 'store']);

        // Afficher une dépense spécifique
        Route::get('/{expense}', [ExpenseController::class, 'show'])
            ->middleware('can:view,expense');

        // Mettre à jour une dépense
        Route::put('/{expense}', [ExpenseController::class, 'update'])
            ->middleware('can:update,expense');

        // Supprimer une dépense (soft delete)
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])
            ->middleware('can:delete,expense');

        // Approuver une dépense (admin/finances)
        Route::post('/{expense}/approve', [ExpenseController::class, 'approve'])
            ->middleware('can:approve,expense');

        // Rejeter une dépense (admin/finances)
        Route::post('/{expense}/reject', [ExpenseController::class, 'reject'])
            ->middleware('can:reject,expense');

        // Télécharger le justificatif
        Route::get('/{expense}/download', [ExpenseController::class, 'downloadJustificatif'])
            ->name('expenses.download')
            ->middleware('can:view,expense');
    });

    // ===== MODULE REPORTING =====
    Route::prefix('reports')->group(function () {
        // Dépenses par catégorie
        Route::get('/expenses-by-category', [ExpenseController::class, 'expensesByCategory']);

        // Dépenses par statut
        Route::get('/expenses-by-status', [ExpenseController::class, 'expensesByStatus']);

        // Dépenses par période
        Route::get('/expenses-by-period', [ExpenseController::class, 'expensesByPeriod']);

        // Rapports opérationnels
        Route::get('/operations/{period}', [ReportingController::class, 'operationsReport']);
        Route::get('/fleet/{period}', [ReportingController::class, 'fleetReport']);
        Route::get('/warehouse/{period}', [ReportingController::class, 'warehouseReport']);
        Route::get('/financial/{period}', [ReportingController::class, 'financialReport']);

        // Exports
        Route::get('/export/{type}/{format}', [ReportingController::class, 'export']);
    });

    // ===== MODULE PARAMÉTRAGES =====
    Route::prefix('settings')->group(function () {
        Route::apiResource('types-operations', TypeOperationController::class)->names([
            'index' => 'api.types-operations.index',
            'store' => 'api.types-operations.store',
            'show' => 'api.types-operations.show',
            'update' => 'api.types-operations.update',
            'destroy' => 'api.types-operations.destroy'
        ]);
        // Route::apiResource('vehicle-types', VehicleTypeController::class); // Contrôleur non existant
        // Route::apiResource('cost-centers', CostCenterController::class); // Contrôleur non existant
        // Route::apiResource('supplier-categories', SupplierCategoryController::class); // Contrôleur non existant
    });

    // ===== RECHERCHE GLOBALE =====
    Route::get('/search', [DashboardController::class, 'globalSearch']);
    Route::get('/autocomplete/{type}', [DashboardController::class, 'autocomplete']);

    // ===== OPÉRATIONS - DÉPENSES =====
    Route::get('/operations/{operation}/depenses', [OperationController::class, 'getOperationDepenses']);
});
