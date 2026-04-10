<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

class DiagnosticCompteResultat extends Command
{
    protected $signature = 'kenam:diagnostic-compte-resultat';
    protected $description = 'Diagnostic du Compte de Résultat pour identifier les problèmes';

    public function handle()
    {
        $this->info('🔍 Diagnostic du Compte de Résultat KENAM');
        $this->info('=====================================');
        $this->line('');

        // 1. Vérifier les tables qui existent
        $this->info('📊 Vérification des tables de la base de données...');
        try {
            $tables = DB::select('SHOW TABLES');
            $tableNames = [];
            foreach ($tables as $table) {
                $tableNames[] = array_values((array)$table)[0];
            }
            $this->info('✅ Tables trouvées: ' . implode(', ', $tableNames));
        } catch (Exception $e) {
            $this->error('❌ Erreur lors de la vérification des tables: ' . $e->getMessage());
        }
        $this->line('');

        // 2. Vérifier les tables spécifiques
        $requiredTables = ['users', 'depenses', 'factures', 'operations', 'ecritures_comptables', 'recettes'];
        foreach ($requiredTables as $table) {
            try {
                $count = DB::table($table)->count();
                $this->info("✅ Table '$table': $count enregistrements");
            } catch (Exception $e) {
                $this->error("❌ Table '$table' n'existe pas ou erreur: " . $e->getMessage());
            }
        }
        $this->line('');

        // 3. Vérifier les colonnes
        $this->info('🔍 Vérification des colonnes importantes...');
        
        // Table users
        try {
            $columns = DB::select("DESCRIBE users");
            $userColumns = array_column($columns, 'Field');
            $this->info('✅ Colonnes users: ' . implode(', ', $userColumns));
            if (!in_array('salaire_base', $userColumns)) {
                $this->warn('⚠️  Colonne \'salaire_base\' manquante dans users');
            }
        } catch (Exception $e) {
            $this->error('❌ Erreur vérification colonnes users: ' . $e->getMessage());
        }

        // Table depenses
        try {
            $columns = DB::select("DESCRIBE depenses");
            $depenseColumns = array_column($columns, 'Field');
            $this->info('✅ Colonnes depenses: ' . implode(', ', $depenseColumns));
            if (!in_array('categorie', $depenseColumns)) {
                $this->warn('⚠️  Colonne \'categorie\' manquante dans depenses');
            }
            if (!in_array('date_depense', $depenseColumns)) {
                $this->warn('⚠️  Colonne \'date_depense\' manquante dans depenses');
            }
        } catch (Exception $e) {
            $this->error('❌ Erreur vérification colonnes depenses: ' . $e->getMessage());
        }

        // Table operations
        try {
            $columns = DB::select("DESCRIBE operations");
            $operationColumns = array_column($columns, 'Field');
            $this->info('✅ Colonnes operations: ' . implode(', ', $operationColumns));
            if (!in_array('statut_courant', $operationColumns)) {
                $this->warn('⚠️  Colonne \'statut_courant\' manquante dans operations');
            }
            if (!in_array('montant_total', $operationColumns)) {
                $this->warn('⚠️  Colonne \'montant_total\' manquante dans operations');
            }
        } catch (Exception $e) {
            $this->error('❌ Erreur vérification colonnes operations: ' . $e->getMessage());
        }
        $this->line('');

        // 4. Tester les requêtes
        $this->info('🧪 Test des requêtes du compte de résultat...');
        
        $currentYear = date('Y');
        $currentMonth = date('m');

        // Test charges personnel
        try {
            $start = microtime(true);
            $result = DB::table('users')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->sum('salaire_base');
            $end = microtime(true);
            $this->info("✅ Charges personnel: $result (temps: " . round(($end - $start) * 1000, 2) . "ms)");
        } catch (Exception $e) {
            $this->error('❌ Erreur charges personnel: ' . $e->getMessage());
        }

        // Test loyer
        try {
            $start = microtime(true);
            $result = DB::table('depenses')
                ->whereYear('date_depense', $currentYear)
                ->whereMonth('date_depense', $currentMonth)
                ->where('categorie', 'loyer')
                ->sum('montant');
            $end = microtime(true);
            $this->info("✅ Loyer: $result (temps: " . round(($end - $start) * 1000, 2) . "ms)");
        } catch (Exception $e) {
            $this->error('❌ Erreur loyer: ' . $e->getMessage());
        }

        // Test ventes services
        try {
            $start = microtime(true);
            $result = DB::table('factures')
                ->whereYear('date_facture', $currentYear)
                ->whereMonth('date_facture', $currentMonth)
                ->where('type', 'vente')
                ->sum('montant_total');
            $end = microtime(true);
            $this->info("✅ Ventes services: $result (temps: " . round(($end - $start) * 1000, 2) . "ms)");
        } catch (Exception $e) {
            $this->error('❌ Erreur ventes services: ' . $e->getMessage());
        }

        // Test prestations services
        try {
            $start = microtime(true);
            $result = DB::table('operations')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->where('statut_courant', 'terminee')
                ->sum('montant_total');
            $end = microtime(true);
            $this->info("✅ Prestations services: $result (temps: " . round(($end - $start) * 1000, 2) . "ms)");
        } catch (Exception $e) {
            $this->error('❌ Erreur prestations services: ' . $e->getMessage());
        }
        $this->line('');

        // 5. Tester l'évolution mensuelle
        $this->info('📈 Test de l\'évolution mensuelle (12 requêtes)...');
        $start = microtime(true);
        $evolution = [];
        for ($month = 1; $month <= 12; $month++) {
            try {
                $result = DB::table('operations')
                    ->whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $month)
                    ->where('statut_courant', 'terminee')
                    ->sum('montant_total');
                $evolution[] = $result;
                $this->line("  Mois $month: $result");
            } catch (Exception $e) {
                $this->error("  ❌ Mois $month: " . $e->getMessage());
                break;
            }
        }
        $end = microtime(true);
        $this->info('⏱️  Temps total évolution: ' . round(($end - $start) * 1000, 2) . 'ms');
        $this->line('');

        $this->info('🎯 Recommandations:');
        $this->line('1. Si des tables manquent, créez-les avec les migrations');
        $this->line('2. Si des colonnes manquent, ajoutez-les avec des migrations');
        $this->line('3. Si les requêtes sont lentes, ajoutez des indexes');
        $this->line('4. Désactivez temporairement l\'évolution mensuelle si nécessaire');
        $this->line('');

        $this->info('✅ Diagnostic terminé!');

        return 0;
    }
}
