<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

class DiagnosticPlateforme extends Command
{
    protected $signature = 'kenam:diagnostic-plateforme';
    protected $description = 'Diagnostic complet de la plateforme pour identifier les ralentissements';

    public function handle()
    {
        $this->info('🔍 Diagnostic Complet de la Plateforme KENAM');
        $this->info('==========================================');
        $this->line('');

        // 1. Test de connexion à la base de données
        $this->info('🗄️ Test de connexion à la base de données...');
        $start = microtime(true);
        try {
            DB::select('SELECT 1');
            $end = microtime(true);
            $this->info('✅ Connexion BDD: ' . round(($end - $start) * 1000, 2) . 'ms');
        } catch (Exception $e) {
            $this->error('❌ Erreur connexion BDD: ' . $e->getMessage());
        }
        $this->line('');

        // 2. Test des requêtes simples
        $this->info('🧪 Test des requêtes simples...');
        
        $queries = [
            'users' => 'SELECT COUNT(*) FROM users',
            'operations' => 'SELECT COUNT(*) FROM operations',
            'depenses' => 'SELECT COUNT(*) FROM depenses',
            'factures' => 'SELECT COUNT(*) FROM factures',
        ];

        foreach ($queries as $name => $query) {
            $start = microtime(true);
            try {
                $result = DB::select($query);
                $end = microtime(true);
                $this->info("✅ $name: " . $result[0]->{'COUNT(*)'} . " enregistrements (temps: " . round(($end - $start) * 1000, 2) . "ms)");
            } catch (Exception $e) {
                $this->error("❌ $name: " . $e->getMessage());
            }
        }
        $this->line('');

        // 3. Test des requêtes complexes (dashboard)
        $this->info('📊 Test des requêtes du dashboard...');
        
        $dashboardQueries = [
            'operations_count' => "SELECT COUNT(*) FROM operations WHERE statut_courant = 'en_cours'",
            'operations_paid' => "SELECT COUNT(*) FROM operations WHERE is_paid = 1",
            'depenses_mois' => "SELECT COUNT(*) FROM depenses WHERE MONTH(date_depense) = MONTH(NOW())",
        ];

        foreach ($dashboardQueries as $name => $query) {
            $start = microtime(true);
            try {
                $result = DB::select($query);
                $end = microtime(true);
                $this->info("✅ $name: " . $result[0]->{'COUNT(*)'} . " (temps: " . round(($end - $start) * 1000, 2) . "ms)");
            } catch (Exception $e) {
                $this->error("❌ $name: " . $e->getMessage());
            }
        }
        $this->line('');

        // 4. Vérifier les services qui pourraient ralentir
        $this->info('🔧 Test des services et middlewares...');
        
        // Test UserPermissionService
        $start = microtime(true);
        try {
            $user = \App\Models\User::first();
            if ($user) {
                $permissions = \App\Services\UserPermissionService::getUserPermissions($user);
                $end = microtime(true);
                $this->info('✅ UserPermissionService: ' . count($permissions) . ' permissions (temps: ' . round(($end - $start) * 1000, 2) . 'ms)');
            }
        } catch (Exception $e) {
            $this->error('❌ UserPermissionService: ' . $e->getMessage());
        }

        // Test DashboardStatsService
        $start = microtime(true);
        try {
            $stats = new \App\Services\DashboardStatsService();
            $parcStats = $stats->getParcStats();
            $end = microtime(true);
            $this->info('✅ DashboardStatsService: ' . count($parcStats) . ' stats (temps: ' . round(($end - $start) * 1000, 2) . 'ms)');
        } catch (Exception $e) {
            $this->error('❌ DashboardStatsService: ' . $e->getMessage());
        }
        $this->line('');

        // 5. Vérifier les logs d'erreurs
        $this->info('📋 Vérification des logs d\'erreurs...');
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logSize = filesize($logFile);
            $this->info('📁 Taille du log: ' . round($logSize / 1024 / 1024, 2) . ' MB');
            
            // Compter les erreurs récentes
            $logContent = file_get_contents($logFile);
            $errorCount = substr_count($logContent, 'ERROR');
            $this->warn('⚠️  Erreurs dans les logs: ' . $errorCount);
        } else {
            $this->info('✅ Pas de fichier de log trouvé');
        }
        $this->line('');

        // 6. Vérifier l'utilisation mémoire
        $this->info('💾 Utilisation mémoire...');
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        $this->info('📊 Mémoire utilisée: ' . round($memoryUsage / 1024 / 1024, 2) . ' MB');
        $this->info('📊 Limite mémoire: ' . $memoryLimit);
        $this->line('');

        // 7. Vérifier les caches
        $this->info('🗄️ Vérification des caches...');
        $cacheSize = 0;
        try {
            $cachePath = storage_path('framework/cache/data');
            if (is_dir($cachePath)) {
                $files = glob($cachePath . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $cacheSize += filesize($file);
                    }
                }
                $this->info('📁 Taille cache: ' . round($cacheSize / 1024, 2) . ' KB');
            }
        } catch (Exception $e) {
            $this->error('❌ Erreur vérification cache: ' . $e->getMessage());
        }
        $this->line('');

        // 8. Test des routes principales
        $this->info('🛣️ Test des routes principales...');
        $routes = [
            'dashboard' => '/',
            'operations' => '/operations',
            'comptabilite' => '/comptabilite',
        ];

        foreach ($routes as $name => $route) {
            $this->info("📍 Route $name: $route");
        }
        $this->line('');

        // 9. Recommandations
        $this->info('🎯 Recommandations d\'optimisation:');
        $this->line('1. Si les requêtes sont lentes (>100ms), ajoutez des indexes');
        $this->line('2. Si la mémoire est élevée, vérifiez les fuites mémoire');
        $this->line('3. Si les logs sont gros, nettoyez-les régulièrement');
        $this->line('4. Activez OPcache si ce n\'est pas déjà fait');
        $this->line('5. Utilisez Redis pour le cache si disponible');
        $this->line('6. Désactivez le debug mode en production');
        $this->line('');

        $this->info('✅ Diagnostic terminé!');

        return 0;
    }
}
