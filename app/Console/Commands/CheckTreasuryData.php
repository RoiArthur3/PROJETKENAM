<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Caisse;
use App\Models\CompteBancaire;
use App\Models\Banque;
use Illuminate\Support\Facades\DB;

class CheckTreasuryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'treasury:check {--seed : Seed test data if empty}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Check treasury data status (Caisses and Comptes Bancaires)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== TREASURY DATA STATUS ===');
        $this->newLine();

        // Check Caisses
        $this->checkCaisses();
        $this->newLine();

        // Check Comptes Bancaires
        $this->checkCompteBancaires();
        $this->newLine();

        // Summary
        $this->showSummary();

        // Seed data if requested and no data exists
        if ($this->option('seed') && Caisse::count() === 0 && CompteBancaire::count() === 0) {
            $this->seedTreasuryData();
        }
    }

    /**
     * Check Caisses table status
     */
    private function checkCaisses()
    {
        $this->info('📊 CAISSES STATUS:');
        
        $totalCaisses = Caisse::count();
        $activeCaisses = Caisse::where('est_active', true)->count();
        $totalBalance = Caisse::sum('solde_actuel');
        $activeBalance = Caisse::where('est_active', true)->sum('solde_actuel');

        $this->line("  Total caisses: {$totalCaisses}");
        $this->line("  Active caisses: {$activeCaisses}");
        $this->line("  Total balance: " . number_format($totalBalance ?? 0, 2, ',', ' ') . " FCFA");
        $this->line("  Active balance: " . number_format($activeBalance ?? 0, 2, ',', ' ') . " FCFA");

        if ($activeCaisses === 0 && $totalCaisses > 0) {
            $this->warn("  ⚠ WARNING: Caisses exist but none are marked as active!");
        }

        if ($totalCaisses === 0) {
            $this->error("  ✗ NO CAISSES FOUND");
        } else if ($activeBalance === 0 && $activeCaisses > 0) {
            $this->warn("  ⚠ WARNING: Active caisses exist but have zero balance!");
        } else {
            $this->info("  ✓ Status OK");
        }
    }

    /**
     * Check Comptes Bancaires table status
     */
    private function checkCompteBancaires()
    {
        $this->info('🏦 COMPTES BANCAIRES STATUS:');

        try {
            $totalComptes = CompteBancaire::count();
            $activeComptes = CompteBancaire::where('est_actif', true)->count();
            $totalBalance = CompteBancaire::sum('solde');
            $activeBalance = CompteBancaire::where('est_actif', true)->sum('solde');

            $this->line("  Total comptes: {$totalComptes}");
            $this->line("  Active comptes: {$activeComptes}");
            $this->line("  Total balance: " . number_format($totalBalance ?? 0, 2, ',', ' ') . " FCFA");
            $this->line("  Active balance: " . number_format($activeBalance ?? 0, 2, ',', ' ') . " FCFA");

            if ($activeComptes === 0 && $totalComptes > 0) {
                $this->warn("  ⚠ WARNING: Comptes exist but none are marked as active!");
            }

            if ($totalComptes === 0) {
                $this->error("  ✗ NO COMPTES BANCAIRES FOUND");
            } else if ($activeBalance === 0 && $activeComptes > 0) {
                $this->warn("  ⚠ WARNING: Active comptes exist but have zero balance!");
            } else {
                $this->info("  ✓ Status OK");
            }
        } catch (\Exception $e) {
            $this->error("  ✗ ERROR: " . $e->getMessage());
        }
    }

    /**
     * Show treasury summary
     */
    private function showSummary()
    {
        $this->info('💰 TREASURY SUMMARY:');

        try {
            $caisseTotal = Caisse::where('est_active', true)->sum('solde_actuel') ?? 0;
            $banqueTotal = CompteBancaire::where('est_actif', true)->sum('solde') ?? 0;
            $grandTotal = $caisseTotal + $banqueTotal;

            $this->line("  Caisses actives: " . number_format($caisseTotal, 2, ',', ' ') . " FCFA");
            $this->line("  Comptes bancaires actifs: " . number_format($banqueTotal, 2, ',', ' ') . " FCFA");
            $this->line("  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->line("  TOTAL: " . number_format($grandTotal, 2, ',', ' ') . " FCFA");

            if ($grandTotal === 0) {
                $this->warn("  ⚠ WARNING: Total treasury is zero!");
                $this->line("  Use option --seed to create test data");
            }
        } catch (\Exception $e) {
            $this->error("  ✗ ERROR: " . $e->getMessage());
        }
    }

    /**
     * Seed test data
     */
    private function seedTreasuryData()
    {
        $this->info('');
        $this->info('Seeding treasury test data...');

        $this->call('db:seed', ['--class' => 'TreasurySeeder']);

        $this->newLine();
        $this->info('✓ Treasury test data seeded successfully!');
        
        // Show summary again
        $this->newLine();
        $this->showSummary();
    }
}
