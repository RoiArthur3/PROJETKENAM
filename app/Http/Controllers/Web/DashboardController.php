<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PermissionController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $authorizedModules = (new PermissionController())->getAuthorizedModules($user);
        $businessKpis = $this->buildBusinessKpis($user);
        $primaryDashboardUrl = $user->getFirstAccessibleModuleUrl();

        return view('dashboard.index', [
            'authorizedModules' => $authorizedModules,
            'totalAvailableModules' => count(config('submodules', [])),
            'businessKpis' => $businessKpis,
            'primaryDashboardUrl' => $primaryDashboardUrl,
        ]);
    }

    private function buildBusinessKpis(User $user): array
    {
        $kpis = [];

        if (
            $user->canAccessModule('comptabilite')
            || $user->canAccessModule('tresorerie')
            || $user->canAccessModule('commercial')
        ) {
            $kpis['finance'] = $this->buildFinanceKpis();
        }

        if ($user->canAccessModule('operations')) {
            $kpis['operations'] = $this->buildOperationsKpis();
        }

        if ($user->canAccessModule('rh')) {
            $kpis['rh'] = $this->buildRhKpis();
        }

        if ($user->canAccessModule('fournisseurs')) {
            $kpis['fournisseurs'] = $this->buildFournisseursKpis();
        }

        return $kpis;
    }

    private function buildFinanceKpis(): array
    {
        $totalFactures = 0;
        $chiffreAffaires = 0.0;
        $caEncaisse = 0.0;
        $facturesImpayees = 0;

        if (Schema::hasTable('factures')) {
            $amountColumn = null;
            foreach (['montant_ttc', 'montant_total', 'total_ttc', 'montant'] as $candidate) {
                if (Schema::hasColumn('factures', $candidate)) {
                    $amountColumn = $candidate;
                    break;
                }
            }

            $statusExists = Schema::hasColumn('factures', 'statut');

            $facturesQuery = DB::table('factures');

            if ($statusExists) {
                $facturesQuery->where(function ($query) {
                    $query->whereNull('statut')
                        ->orWhereRaw('TRIM(statut) = ?',[ '' ])
                        ->orWhereRaw('LOWER(TRIM(statut)) NOT IN (?, ?, ?)', ['annulee', 'annulée', 'cancelled']);
                });
            }

            $totalFactures = (clone $facturesQuery)->count();

            if ($amountColumn) {
                $chiffreAffaires = (float) (clone $facturesQuery)->sum($amountColumn);

                if ($statusExists) {
                    $caEncaisse = (float) (clone $facturesQuery)
                        ->whereRaw('LOWER(TRIM(statut)) IN (?, ?, ?, ?)', ['payee', 'payée', 'paid', 'encaissee'])
                        ->sum($amountColumn);

                    $facturesImpayees = (int) (clone $facturesQuery)
                        ->whereRaw('LOWER(TRIM(statut)) IN (?, ?, ?, ?, ?, ?)', [
                            'en_attente',
                            'en_retard',
                            'impayee',
                            'impayée',
                            'partiellement_payee',
                            'partiellement_payée',
                        ])
                        ->count();
                }
            }
        }

        $totalClients = Schema::hasTable('clients') ? (int) DB::table('clients')->count() : 0;
        $totalFournisseurs = Schema::hasTable('fournisseurs') ? (int) DB::table('fournisseurs')->count() : 0;

        return [
            'title' => 'Indicateurs Finance',
            'items' => [
                ['label' => 'Chiffre d\'affaires', 'value' => $chiffreAffaires, 'format' => 'money'],
                ['label' => 'CA encaissé', 'value' => $caEncaisse, 'format' => 'money'],
                ['label' => 'Factures', 'value' => $totalFactures, 'format' => 'int'],
                ['label' => 'Factures impayées', 'value' => $facturesImpayees, 'format' => 'int'],
                ['label' => 'Clients', 'value' => $totalClients, 'format' => 'int'],
                ['label' => 'Fournisseurs', 'value' => $totalFournisseurs, 'format' => 'int'],
            ],
        ];
    }

    private function buildOperationsKpis(): array
    {
        if (!Schema::hasTable('operations') || !Schema::hasColumn('operations', 'statut_courant')) {
            return [
                'title' => 'Indicateurs Opérations',
                'items' => [
                    ['label' => 'Total opérations', 'value' => 0, 'format' => 'int'],
                ],
            ];
        }

        $baseQuery = DB::table('operations');

        return [
            'title' => 'Indicateurs Opérations',
            'items' => [
                ['label' => 'Total opérations', 'value' => (int) (clone $baseQuery)->count(), 'format' => 'int'],
                ['label' => 'En attente', 'value' => (int) (clone $baseQuery)->whereRaw('LOWER(TRIM(statut_courant)) IN (?, ?)', ['pending_validation', 'en_attente'])->count(), 'format' => 'int'],
                ['label' => 'Approuvées', 'value' => (int) (clone $baseQuery)->whereRaw('LOWER(TRIM(statut_courant)) IN (?, ?, ?)', ['approuvee', 'approuve', 'approuvé_en_attente_paiement'])->count(), 'format' => 'int'],
                ['label' => 'Payées', 'value' => (int) (clone $baseQuery)->whereRaw('LOWER(TRIM(statut_courant)) IN (?, ?)', ['payee', 'payée'])->count(), 'format' => 'int'],
            ],
        ];
    }

    private function buildRhKpis(): array
    {
        $totalPersonnel = Schema::hasTable('personnel') ? (int) DB::table('personnel')->count() : 0;

        $personnelActif = 0;
        if (Schema::hasTable('personnel') && Schema::hasColumn('personnel', 'statut')) {
            $personnelActif = (int) DB::table('personnel')
                ->whereRaw('LOWER(TRIM(statut)) IN (?, ?)', ['actif', 'active'])
                ->count();
        }

        return [
            'title' => 'Indicateurs RH',
            'items' => [
                ['label' => 'Personnel total', 'value' => $totalPersonnel, 'format' => 'int'],
                ['label' => 'Personnel actif', 'value' => $personnelActif, 'format' => 'int'],
            ],
        ];
    }

    private function buildFournisseursKpis(): array
    {
        $total = Schema::hasTable('fournisseurs') ? (int) DB::table('fournisseurs')->count() : 0;
        $actifs = 0;

        if (Schema::hasTable('fournisseurs')) {
            if (Schema::hasColumn('fournisseurs', 'est_actif')) {
                $actifs = (int) DB::table('fournisseurs')->where('est_actif', true)->count();
            } elseif (Schema::hasColumn('fournisseurs', 'statut')) {
                $actifs = (int) DB::table('fournisseurs')
                    ->whereRaw('LOWER(TRIM(statut)) IN (?, ?)', ['actif', 'active'])
                    ->count();
            }
        }

        return [
            'title' => 'Indicateurs Fournisseurs',
            'items' => [
                ['label' => 'Fournisseurs total', 'value' => $total, 'format' => 'int'],
                ['label' => 'Fournisseurs actifs', 'value' => $actifs, 'format' => 'int'],
            ],
        ];
    }
}
