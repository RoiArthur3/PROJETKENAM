<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComptabiliteController;
use App\Http\Controllers\FacturationController;
use App\Http\Controllers\JournalComptableController;
use App\Http\Controllers\SageImportController;

/*
|--------------------------------------------------------------------------
| Routes Comptabilité
|--------------------------------------------------------------------------
|
| Routes pour le module comptabilité
|
*/

Route::prefix('comptabilite')->name('comptabilite.')->middleware(['auth'])->group(function () {
    // Route de diagnostic pour vérifier l'authentification
    Route::get('/debug-auth', function() {
        return response()->json([
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
            'user_role' => auth()->user()?->getRoleNames()->first(),
            'session_id' => session()->getId(),
        ]);
    });

    // Route de diagnostic pour vérifier la base de données
    Route::get('/debug-db', function() {
        try {
            $tables = [
                'factures' => \Illuminate\Support\Facades\Schema::hasTable('factures'),
                'operations' => \Illuminate\Support\Facades\Schema::hasTable('operations'),
                'depenses' => \Illuminate\Support\Facades\Schema::hasTable('depenses'),
                'recettes' => \Illuminate\Support\Facades\Schema::hasTable('recettes'),
                'compte_bancaires' => \Illuminate\Support\Facades\Schema::hasTable('compte_bancaires'),
                'caisses' => \Illuminate\Support\Facades\Schema::hasTable('caisses'),
                'paiements' => \Illuminate\Support\Facades\Schema::hasTable('paiements'),
                'clients' => \Illuminate\Support\Facades\Schema::hasTable('clients'),
                'fournisseurs' => \Illuminate\Support\Facades\Schema::hasTable('fournisseurs'),
            ];

            $counts = [];
            $sampleData = [];

            foreach ($tables as $table => $exists) {
                if ($exists) {
                    $counts[$table] = \Illuminate\Support\Facades\DB::table($table)->count();

                    // Prendre un échantillon de données pour voir ce qui existe
                    if ($counts[$table] > 0) {
                        $sampleData[$table] = \Illuminate\Support\Facades\DB::table($table)
                            ->limit(2)
                            ->get()
                            ->toArray();
                    }
                } else {
                    $counts[$table] = 'N/A';
                }
            }

            // Vérifier spécifiquement les opérations payées
            $operationsPayees = 0;
            if ($tables['operations']) {
                $operationsPayees = \Illuminate\Support\Facades\DB::table('operations')->where('is_paid', 1)->count();
            }

            return response()->json([
                'db_connection' => 'OK',
                'tables_exist' => $tables,
                'table_counts' => $counts,
                'operations_payees' => $operationsPayees,
                'sample_data' => $sampleData,
                'database' => config('database.connections.mysql.database'),
                'message' => 'Diagnostic complet de la base de données'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'db_connection' => 'ERROR',
                'error' => $e->getMessage(),
                'message' => 'Erreur de connexion à la base de données'
            ]);
        }
    });

    // Route AJAX pour filtrer les factures
    Route::get('/factures/filter', [ComptabiliteController::class, 'filterFactures'])->name('factures.filter');

    // ── IMPORT SAGE i7 ──────────────────────────────────────────────────────────
    Route::prefix('sage-import')->name('sage-import.')->group(function () {
        Route::get('/', [SageImportController::class, 'index'])->name('index');
        Route::post('/clients', [SageImportController::class, 'importClients'])->name('clients');
        Route::post('/fournisseurs', [SageImportController::class, 'importFournisseurs'])->name('fournisseurs');
        Route::post('/factures', [SageImportController::class, 'importFactures'])->name('factures');
        Route::post('/ecritures', [SageImportController::class, 'importEcritures'])->name('ecritures');
        Route::get('/template/{type}', [SageImportController::class, 'downloadTemplate'])->name('template');
    });

    // Tableau de bord comptabilité
    Route::get('/', [ComptabiliteController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [ComptabiliteController::class, 'dashboard'])->name('dashboard.main');

    // Importation des factures Excel
    Route::post('/import-factures', [ComptabiliteController::class, 'importFactures'])->name('importFactures');

    // Importation en ligne des parametres comptables
    Route::get('/import-param-compta', [ComptabiliteController::class, 'importParamComptaForm'])->name('import-param-compta.form');
    Route::post('/import-param-compta', [ComptabiliteController::class, 'importParamComptaStore'])->name('import-param-compta.store');

    // Gestion du grand journal et des écritures comptables
    Route::get('/ecritures', [JournalComptableController::class, 'grandJournal'])->name('ecritures.index');
    Route::get('/ecritures/create', [JournalComptableController::class, 'createEcriture'])->name('ecritures.create');
    Route::get('/ecritures/import-ventes', [JournalComptableController::class, 'importVentesForm'])->name('ecritures.import-ventes.form');
    Route::post('/ecritures/import-ventes', [JournalComptableController::class, 'importVentesStore'])->name('ecritures.import-ventes.store');
    Route::get('/ecritures/detail', [JournalComptableController::class, 'detailEcriture'])->name('ecritures.detail');
    Route::post('/ecritures', [JournalComptableController::class, 'storeEcriture'])->name('ecritures.store');
    Route::get('/ecritures/{ecriture}/edit', [JournalComptableController::class, 'editEcriture'])->name('ecritures.edit');
    Route::put('/ecritures/{ecriture}', [JournalComptableController::class, 'updateEcriture'])->name('ecritures.update');
    Route::delete('/ecritures/{ecriture}', [JournalComptableController::class, 'destroyEcriture'])->name('ecritures.destroy');

    // Gestion des journaux comptables
    Route::prefix('journaux')->name('journaux.')->group(function () {
        Route::get('/', [JournalComptableController::class, 'journauxIndex'])->name('index');
        Route::post('/', [JournalComptableController::class, 'storeJournal'])->name('store');
        Route::put('/{journal}', [JournalComptableController::class, 'updateJournal'])->name('update');
    });

    Route::get('/factures', [ComptabiliteController::class, 'facturesIndex'])->name('factures.index');
    Route::get('/facturation', [ComptabiliteController::class, 'facturesIndex'])->name('facturation');

    Route::get('/factures/create', [ComptabiliteController::class, 'facturesCreate'])->name('factures.create');
    Route::get('/facturation/create', [ComptabiliteController::class, 'facturesCreate'])->name('facturation.create');

    Route::post('/factures', [ComptabiliteController::class, 'facturesStore'])->name('factures.store');
    Route::post('/facturation', [ComptabiliteController::class, 'facturesStore'])->name('facturation.store');

    Route::get('/factures/{id}', [ComptabiliteController::class, 'facturesShow'])->name('factures.show');
    Route::get('/factures/{id}/edit', [ComptabiliteController::class, 'facturesEdit'])->name('factures.edit');
    Route::get('/factures/{id}/print', [ComptabiliteController::class, 'facturesPrint'])->name('factures.print');
    Route::put('/factures/{id}', [ComptabiliteController::class, 'facturesUpdate'])->name('factures.update');
    Route::patch('/factures/{id}/mark-paid', [ComptabiliteController::class, 'markAsPaid'])->name('factures.markPaid');
    Route::delete('/factures/{id}', [ComptabiliteController::class, 'facturesDestroy'])->name('factures.destroy');
    Route::get('/factures/statistics', [ComptabiliteController::class, 'facturesStatistics'])->name('factures.statistics');
    Route::get('/facturation/statistics', [ComptabiliteController::class, 'facturesStatistics'])->name('facturation.statistics');

    // ── MODULE FACTURATION COMPLET (NOUVEAU SYSTÈME) ──
    Route::prefix('facturesest')->name('facturesest.')->group(function() {
        Route::get('/', [FacturationController::class, 'etatsFactures'])->name('index');
        Route::get('/create', [FacturationController::class, 'create'])->name('create');
        Route::post('/', [FacturationController::class, 'store'])->name('store');
        Route::get('/{facture}', [FacturationController::class, 'show'])->name('show');
        Route::get('/{facture}/edit', [FacturationController::class, 'edit'])->name('edit');
        Route::put('/{facture}', [FacturationController::class, 'update'])->name('update');
        Route::post('/{facture}/depot', [FacturationController::class, 'depot'])->name('depot');
        Route::delete('/{facture}', [FacturationController::class, 'destroy'])->name('destroy');
    });

    // Gestion des paiements
    Route::get('/paiements', [ComptabiliteController::class, 'paiementsIndex'])->name('paiements.index');
    Route::get('/paiements/create', [ComptabiliteController::class, 'paiementsCreate'])->name('paiements.create');
    Route::post('/paiements', [ComptabiliteController::class, 'paiementsStore'])->name('paiements.store');
    Route::get('/paiements/{paiement}', [ComptabiliteController::class, 'paiementsShow'])->name('paiements.show');
    Route::get('/paiements/{paiement}/edit', [ComptabiliteController::class, 'paiementsEdit'])->name('paiements.edit');
    Route::put('/paiements/{paiement}', [ComptabiliteController::class, 'paiementsUpdate'])->name('paiements.update');
    Route::delete('/paiements/{paiement}', [ComptabiliteController::class, 'paiementsDestroy'])->name('paiements.destroy');

    // Gestion des dépenses
    Route::get('/depenses', [ComptabiliteController::class, 'depensesIndex'])->name('depenses.index');
    Route::get('/depenses/create', [ComptabiliteController::class, 'depensesCreate'])->name('depenses.create');
    Route::post('/depenses', [ComptabiliteController::class, 'depensesStore'])->name('depenses.store');
    Route::get('/depenses/{depense}', [ComptabiliteController::class, 'depensesShow'])->name('depenses.show');
    Route::get('/depenses/{depense}/edit', [ComptabiliteController::class, 'depensesEdit'])->name('depenses.edit');
    Route::put('/depenses/{depense}', [ComptabiliteController::class, 'depensesUpdate'])->name('depenses.update');
    Route::delete('/depenses/{depense}', [ComptabiliteController::class, 'depensesDestroy'])->name('depenses.destroy');

    // Gestion des recettes
    Route::get('/recettes', [ComptabiliteController::class, 'recettesIndex'])->name('recettes.index');
    Route::get('/recettes/create', [ComptabiliteController::class, 'recettesCreate'])->name('recettes.create');
    Route::post('/recettes', [ComptabiliteController::class, 'recettesStore'])->name('recettes.store');
    Route::get('/recettes/{recette}', [ComptabiliteController::class, 'recettesShow'])->name('recettes.show');
    Route::get('/recettes/{recette}/edit', [ComptabiliteController::class, 'recettesEdit'])->name('recettes.edit');
    Route::put('/recettes/{recette}', [ComptabiliteController::class, 'recettesUpdate'])->name('recettes.update');
    Route::delete('/recettes/{recette}', [ComptabiliteController::class, 'recettesDestroy'])->name('recettes.destroy');

    // Rapports et états financiers
    Route::prefix('rapports')->name('rapports.')->group(function() {
        Route::get('/bilan', [ComptabiliteController::class, 'bilan'])->name('bilan');
        Route::get('/bilan/detail/{poste}', [ComptabiliteController::class, 'bilanDetail'])->name('bilan-detail');
        Route::get('/compte-resultat', [ComptabiliteController::class, 'rapportCompteResultat'])->name('compte-resultat');
        Route::get('/indicateurs-financiers', [ComptabiliteController::class, 'rapportIndicateursFinanciers'])->name('indicateurs-financiers');
        Route::get('/analyse-activite', [ComptabiliteController::class, 'rapportAnalyseActivite'])->name('analyse-activite');
        Route::get('/analyse-rentabilite', [ComptabiliteController::class, 'rapportAnalyseRentabilite'])->name('analyse-rentabilite');
        Route::get('/analyse-variation-treso', [ComptabiliteController::class, 'rapportAnalyseVariationTreso'])->name('analyse-variation-treso');
        Route::get('/analyse-variation-dette', [ComptabiliteController::class, 'rapportAnalyseVariationDette'])->name('analyse-variation-dette');
        Route::get('/analyse-activite/export/excel', [ComptabiliteController::class, 'exportAnalyseActiviteExcel'])->name('analyse-activite.export-excel');
        Route::get('/analyse-activite/export/pdf', [ComptabiliteController::class, 'exportAnalyseActivitePdf'])->name('analyse-activite.export-pdf');
        Route::get('/analyse-rentabilite/export/excel', [ComptabiliteController::class, 'exportAnalyseRentabiliteExcel'])->name('analyse-rentabilite.export-excel');
        Route::get('/analyse-rentabilite/export/pdf', [ComptabiliteController::class, 'exportAnalyseRentabilitePdf'])->name('analyse-rentabilite.export-pdf');
        Route::get('/analyse-variation-treso/export/excel', [ComptabiliteController::class, 'exportAnalyseVariationTresoExcel'])->name('analyse-variation-treso.export-excel');
        Route::get('/analyse-variation-treso/export/pdf', [ComptabiliteController::class, 'exportAnalyseVariationTresoPdf'])->name('analyse-variation-treso.export-pdf');
        Route::get('/analyse-variation-dette/export/excel', [ComptabiliteController::class, 'exportAnalyseVariationDetteExcel'])->name('analyse-variation-dette.export-excel');
        Route::get('/analyse-variation-dette/export/pdf', [ComptabiliteController::class, 'exportAnalyseVariationDettePdf'])->name('analyse-variation-dette.export-pdf');
        Route::get('/indicateurs-financiers/export/excel', [ComptabiliteController::class, 'exportIndicateursFinanciersExcel'])->name('indicateurs-financiers.export-excel');
        Route::get('/indicateurs-financiers/export/pdf', [ComptabiliteController::class, 'exportIndicateursFinanciersPdf'])->name('indicateurs-financiers.export-pdf');
        Route::get('/tresorerie', [ComptabiliteController::class, 'rapportTresorerie'])->name('tresorerie');
        Route::get('/grand-journal', [JournalComptableController::class, 'grandJournal'])->name('grand-journal');
        Route::get('/grand-journal/export/excel', [JournalComptableController::class, 'exportGrandJournalExcel'])->name('grand-journal.export-excel');
        Route::get('/grand-journal/export/pdf', [JournalComptableController::class, 'exportGrandJournalPdf'])->name('grand-journal.export-pdf');
    });

    // Alias pour compatibilité - CORRIGÉ pour éviter les conflits
    Route::get('/bilan-direct', [ComptabiliteController::class, 'bilan'])->name('bilan.direct');
    Route::get('/tresorerie-direct', [ComptabiliteController::class, 'rapportTresorerie'])->name('tresorerie.direct');
    Route::get('/index', [ComptabiliteController::class, 'dashboard'])->name('index');
    Route::get('/depenses-list', [ComptabiliteController::class, 'depensesIndex'])->name('depenses');
    Route::get('/recettes-list', [ComptabiliteController::class, 'recettesIndex'])->name('recettes');

    // ── API AJAX pour page unifiée ──
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/facturation', [ComptabiliteController::class, 'apiFacturation'])->name('facturation');
        Route::get('/encaissements', [ComptabiliteController::class, 'apiEncaissements'])->name('encaissements');
        Route::get('/depenses', [ComptabiliteController::class, 'apiDepenses'])->name('depenses');
        Route::get('/caisse', [ComptabiliteController::class, 'apiCaisse'])->name('caisse');
        Route::get('/banque', [ComptabiliteController::class, 'apiBanque'])->name('banque');
        Route::get('/clients', [ComptabiliteController::class, 'apiClients'])->name('clients');
        Route::get('/fournisseurs', [ComptabiliteController::class, 'apiFournisseurs'])->name('fournisseurs');
        Route::get('/charges', [ComptabiliteController::class, 'apiCharges'])->name('charges');
        Route::get('/rapports', [ComptabiliteController::class, 'apiRapports'])->name('rapports');
        Route::get('/pieces', [ComptabiliteController::class, 'apiPieces'])->name('pieces');
        Route::get('/stats', [ComptabiliteController::class, 'apiStats'])->name('stats');
    });

    Route::get('/rapports', [ComptabiliteController::class, 'rapportsIndex'])->name('rapports');
    Route::get('/conformite', [ComptabiliteController::class, 'conformite'])->name('conformite');
    Route::get('/operations-to-ecritures', [ComptabiliteController::class, 'operationsToEcritures'])->name('operations-to-ecritures');
    Route::get('/etats-financiers', [ComptabiliteController::class, 'bilan'])->name('etats-financiers');
    Route::get('/charges', [ComptabiliteController::class, 'depensesIndex'])->name('charges');

    Route::prefix('classification')->name('classification.')->group(function() {
        Route::get('/', [ComptabiliteController::class, 'classificationIndex'])->name('index');
    });

    // -- MODULES FISCAUX DGI / CNPS C�TE D'IVOIRE ------------------------------

    // Imp�t sur les Soci�t�s (IS) � CGI Art. 63 � 25%
    Route::prefix('impot-societes')->name('impot-societes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ImpotSocietesController::class, 'index'])->name('index');
        Route::post('/calculer', [\App\Http\Controllers\ImpotSocietesController::class, 'calculer'])->name('calculer');
    });

    // TVA D�clarative mensuelle � CGI Art. 339
    Route::prefix('tva-declarative')->name('tva-declarative.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TvaDeclarativeController::class, 'index'])->name('index');
        Route::post('/calculer', [\App\Http\Controllers\TvaDeclarativeController::class, 'calculer'])->name('calculer');
    });

    // Plan Comptable SYSCOHADA
    Route::prefix('syscohada')->name('syscohada.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SyscohadaController::class, 'index'])->name('index');
        Route::get('/search', [\App\Http\Controllers\SyscohadaController::class, 'search'])->name('search');
    });

    // TFP & Taxe d'Apprentissage � FDFP
    Route::prefix('tfp')->name('tfp.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TfpController::class, 'index'])->name('index');
        Route::post('/calculer', [\App\Http\Controllers\TfpController::class, 'calculer'])->name('calculer');
    });

    // Retenue � la Source (RAS) � CGI Art. 165-180
    Route::prefix('retenue-source')->name('retenue-source.')->group(function () {
        Route::get('/', [\App\Http\Controllers\RetenueSourceController::class, 'index'])->name('index');
        Route::post('/calculer', [\App\Http\Controllers\RetenueSourceController::class, 'calculer'])->name('calculer');
        Route::post('/sauvegarder', [\App\Http\Controllers\RetenueSourceController::class, 'sauvegarderDeclaration'])->name('sauvegarder');
        Route::get('/historique', [\App\Http\Controllers\RetenueSourceController::class, 'historique'])->name('historique');
    });

    // Validation des demandes d'approvisionnement (Comptabilité)
    Route::prefix('approvisionnement-demandes')->name('approvisionnement-demandes.')->group(function () {
        Route::get('/pending', [\App\Http\Controllers\ApprovisionnementDemandeController::class, 'pending'])->name('pending');
        Route::post('/{demande}/approve', [\App\Http\Controllers\ApprovisionnementDemandeController::class, 'approve'])->name('approve');
        Route::post('/{demande}/reject', [\App\Http\Controllers\ApprovisionnementDemandeController::class, 'reject'])->name('reject');
        Route::post('/{demande}/execute', [\App\Http\Controllers\ApprovisionnementDemandeController::class, 'execute'])->name('execute');
    });

});
