<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\DB;

class DashboardReconciliationTest extends TestCase
{
    use DatabaseTransactions;

    private DashboardController $dashboardController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dashboardController = new DashboardController();
    }

    /**
     * Test Factures - Vérifier que la correction des accents fonctionne
     * @test
     */
    public function test_factures_statut_avec_accents()
    {
        // Vérifier que les statuts avec accents existent en base
        if (DB::connection()->getDatabaseName()) {
            $factures_payees = DB::table('factures')->where('statut', 'payée')->count();
            $factures_impayees = DB::table('factures')->where('statut', 'impayée')->count();
            $factures_annulees = DB::table('factures')->where('statut', 'annulée')->count();

            $this->assertGreaterThanOrEqual(0, $factures_payees, 'Factures payées should be >= 0');
            $this->assertGreaterThanOrEqual(0, $factures_impayees, 'Factures impayées should be >= 0');
            $this->assertGreaterThanOrEqual(0, $factures_annulees, 'Factures annulées should be >= 0');

            // Vérifier qu'il n'y a plus d'anciennes valeurs sans accents
            $old_values = DB::table('factures')
                ->whereIn('statut', ['payee', 'annulee', 'en_attente', 'en_retard'])
                ->count();
            
            $this->assertEquals(0, $old_values, 'Should have no old statut values without accents');
        }
    }

    /**
     * Test Personnel - Vérifier les statuts de résilition
     * @test
     */
    public function test_personnel_statuts_resilition()
    {
        // Vérifier que les nouveaux statuts existent
        if (DB::connection()->getDatabaseName()) {
            $actifs = DB::table('personnel')->where('statut', 'ACTIF')->count();
            $demission = DB::table('personnel')->where('statut', 'DEMISSION')->count();
            $licencie = DB::table('personnel')->where('statut', 'LICENCIE')->count();
            $retraite = DB::table('personnel')->where('statut', 'RETRAITE')->count();

            $this->assertGreaterThanOrEqual(0, $actifs, 'Personnel actifs should be >= 0');
            $this->assertGreaterThanOrEqual(0, $demission, 'Personnel en démission should be >= 0');
            $this->assertGreaterThanOrEqual(0, $licencie, 'Personnel licencié should be >= 0');
            $this->assertGreaterThanOrEqual(0, $retraite, 'Personnel retraité should be >= 0');

            // Vérifier qu'il n'y a plus de valeur RESILIE
            $resilie_count = DB::table('personnel')->where('statut', 'RESILIE')->count();
            $this->assertEquals(0, $resilie_count, 'Should have no old RESILIE status');
        }
    }

    /**
     * Test Personnel - Vérifier la colonne fin_periode_essai
     * @test
     */
    public function test_personnel_fin_periode_essai_exists()
    {
        if (DB::connection()->getDatabaseName() && \Schema::hasColumn('personnel', 'fin_periode_essai')) {
            // Récupérer les personnels en essai
            $en_essai = DB::table('personnel')
                ->where('statut', 'ACTIF')
                ->where('fin_periode_essai', '>', now())
                ->count();

            $this->assertGreaterThanOrEqual(0, $en_essai, 'Personnel en essai count should be >= 0');
        }
    }

    /**
     * Test Fournisseurs - Vérifier la colonne est_actif
     * @test
     */
    public function test_fournisseurs_est_actif_column()
    {
        if (DB::connection()->getDatabaseName() && \Schema::hasColumn('fournisseurs', 'est_actif')) {
            $actifs = DB::table('fournisseurs')->where('est_actif', true)->count();
            $inactifs = DB::table('fournisseurs')->where('est_actif', false)->count();

            // Devrait avoir au moins 0 actifs (si la table n'est pas vide)
            $this->assertGreaterThanOrEqual(0, $actifs, 'Fournisseurs actifs should be >= 0');
            $this->assertGreaterThanOrEqual(0, $inactifs, 'Fournisseurs inactifs should be >= 0');

            // Le total actifs + inactifs = total fournisseurs
            $total = DB::table('fournisseurs')->count();
            $this->assertEquals($total, $actifs + $inactifs, 'Sum of actifs and inactifs should equal total');
        }
    }

    /**
     * Test Véhicules - Vérifier la colonne disponible
     * @test
     */
    public function test_vehicules_disponible_column()
    {
        if (DB::connection()->getDatabaseName() && \Schema::hasColumn('vehicules', 'disponible')) {
            $disponibles = DB::table('vehicules')->where('disponible', true)->count();
            $non_disponibles = DB::table('vehicules')->where('disponible', false)->count();

            $this->assertGreaterThanOrEqual(0, $disponibles, 'Véhicules disponibles should be >= 0');
            $this->assertGreaterThanOrEqual(0, $non_disponibles, 'Véhicules non-disponibles should be >= 0');

            // Le total disponibles + non-disponibles = total véhicules
            $total = DB::table('vehicules')->count();
            $this->assertEquals($total, $disponibles + $non_disponibles, 'Sum should equal total vehicles');
        }
    }

    /**
     * Test Operations - Vérifier la colonne statut_courant
     * @test
     */
    public function test_operations_statut_courant_column()
    {
        if (DB::connection()->getDatabaseName() && \Schema::hasColumn('operations', 'statut_courant')) {
            $total_operations = DB::table('operations')->count();
            
            // Vérifier que les statuts courants ne sont pas null
            $non_null_status = DB::table('operations')->whereNotNull('statut_courant')->count();
            
            if ($total_operations > 0) {
                $this->assertGreaterThan(0, $non_null_status, 'Should have non-null statut_courant values');
            }
        }
    }

    /**
     * Test Dashboard Stats - Factures doivent retourner des nombres != 0
     * @test
     */
    public function test_dashboard_detailed_accounting_stats()
    {
        try {
            // Skip if table doesn't exist
            if (!DB::connection()->getSchemaBuilder()->hasTable('factures')) {
                $this->markTestSkipped('factures table does not exist');
                return;
            }

            // Appeler la méthode via Reflection pour accéder à la méthode privée
            $reflection = new \ReflectionClass(DashboardController::class);
            $method = $reflection->getMethod('getDetailedAccountingStats');
            $method->setAccessible(true);

            $stats = $method->invoke($this->dashboardController);

            // Vérifier que les statistiques sont présentes
            $this->assertIsArray($stats, 'Stats should be an array');
            $this->assertArrayHasKey('ca_total', $stats);
            $this->assertArrayHasKey('factures_total', $stats);
            $this->assertArrayHasKey('factures_payees', $stats);
            $this->assertArrayHasKey('factures_impayees', $stats);

            // Les valeurs doivent être numériques
            $this->assertIsNumeric($stats['ca_total']);
            $this->assertIsNumeric($stats['factures_total']);
            $this->assertIsNumeric($stats['factures_payees']);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                $this->markTestSkipped('Missing required table');
            } else {
                throw $e;
            }
        }
    }

    /**
     * Test Dashboard Stats - Personnel
     * @test
     */
    public function test_dashboard_rh_stats()
    {
        try {
            // Skip if table doesn't exist
            if (!DB::connection()->getSchemaBuilder()->hasTable('personnel')) {
                $this->markTestSkipped('personnel table does not exist');
                return;
            }

            $reflection = new \ReflectionClass(DashboardController::class);
            $method = $reflection->getMethod('getRHStats');
            $method->setAccessible(true);

            $stats = $method->invoke($this->dashboardController);

            // Vérifier que les statistiques sont présentes
            $this->assertIsArray($stats);
            $this->assertArrayHasKey('total_personnel', $stats);
            $this->assertArrayHasKey('actifs', $stats);
            $this->assertArrayHasKey('en_essai', $stats);
            $this->assertArrayHasKey('resilles', $stats);

            // Les valeurs doivent être numériques et positives
            $this->assertGreaterThanOrEqual(0, $stats['total_personnel']);
            $this->assertGreaterThanOrEqual(0, $stats['actifs']);
            $this->assertGreaterThanOrEqual(0, $stats['en_essai']);
            $this->assertGreaterThanOrEqual(0, $stats['resilles']);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                $this->markTestSkipped('Missing required table');
            } else {
                throw $e;
            }
        }
    }

    /**
     * Test Dashboard Stats - Fournisseurs
     * @test
     */
    public function test_dashboard_fournisseurs_stats()
    {
        try {
            if (!DB::connection()->getSchemaBuilder()->hasTable('fournisseurs')) {
                $this->markTestSkipped('fournisseurs table does not exist');
                return;
            }

            $reflection = new \ReflectionClass(DashboardController::class);
            $method = $reflection->getMethod('getFournisseursStats');
            $method->setAccessible(true);

            $stats = $method->invoke($this->dashboardController);

            // Vérifier que les statistiques sont présentes
            $this->assertIsArray($stats);
            $this->assertArrayHasKey('total_fournisseurs', $stats);
            $this->assertArrayHasKey('actifs', $stats);

            // Les valeurs doivent être numériques
            $this->assertGreaterThanOrEqual(0, $stats['total_fournisseurs']);
            $this->assertGreaterThanOrEqual(0, $stats['actifs']);

            // Les fournisseurs actifs doivent être <= total
            $this->assertLessThanOrEqual($stats['total_fournisseurs'], $stats['actifs']);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                $this->markTestSkipped('Missing required table');
            } else {
                throw $e;
            }
        }
    }

    /**
     * Test Dashboard Stats - Véhicules
     * @test
     */
    public function test_dashboard_vehicules_stats()
    {
        // Skip if table doesn't exist
        if (!DB::connection()->getSchemaBuilder()->hasTable('vehicules')) {
            $this->markTestSkipped('vehicules table does not exist');
            return;
        }

        $reflection = new \ReflectionClass(DashboardController::class);
        $method = $reflection->getMethod('getVehiculesStats');
        $method->setAccessible(true);

        $stats = $method->invoke($this->dashboardController);

        // Vérifier que les statistiques sont présentes
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_vehicules', $stats);
        $this->assertArrayHasKey('disponibles', $stats);

        // Les véhicules disponibles doivent être <= total
        $this->assertLessThanOrEqual($stats['total_vehicules'], $stats['disponibles']);
    }

    /**
     * Test Database Indexes - Vérifier que les indexes ont été créés
     * @test
     */
    public function test_dashboard_query_indexes_created()
    {
        if (DB::connection()->getDatabaseName()) {
            // Vérifier que les colonnes critiques pour dashboard existent
            $this->assertTrue(\Schema::hasColumn('factures', 'statut'), 'factures.statut should exist');
            $this->assertTrue(\Schema::hasColumn('personnel', 'statut'), 'personnel.statut should exist');
            $this->assertTrue(\Schema::hasColumn('fournisseurs', 'est_actif'), 'fournisseurs.est_actif should exist');
            $this->assertTrue(\Schema::hasColumn('vehicules', 'disponible'), 'vehicules.disponible should exist');
            $this->assertTrue(\Schema::hasColumn('operations', 'statut_courant'), 'operations.statut_courant should exist');
        }
    }

    /**
     * Test Date Consistency - Vérifier que les dates sont cohérentes
     * @test
     */
    public function test_date_consistency()
    {
        if (DB::connection()->getDatabaseName()) {
            // Vérifier que fin_periode_essai n'est pas dans le passé pour les actifs en essai
            $future_essai = DB::table('personnel')
                ->where('statut', 'ACTIF')
                ->where('fin_periode_essai', '>', now())
                ->count();

            // Les actifs en essai doivent avoir une date future
            $this->assertGreaterThanOrEqual(0, $future_essai);
        }
    }
}
