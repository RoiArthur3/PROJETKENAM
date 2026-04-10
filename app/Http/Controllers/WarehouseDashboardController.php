<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockItem;

class WarehouseDashboardController extends Controller
{
    public function index()
    {
        // Placeholders with safe defaults until stock module tables exist
        // Build last 12 months labels
        $months = collect(range(0, 11))
            ->map(fn($i) => now()->subMonths(11 - $i))
            ->values();

        $labels = $months->map(fn($d) => $d->format('M Y'))->all();

        // Defaults
        $entrees = array_fill(0, 12, 0);
        $sorties = array_fill(0, 12, 0);
        $totalValue = 0; // FCFA
        $totalProducts = 0; // count of active references
        $lowStockCount = 0; // stocks < min
        $criticalStockCount = 0; // ruptures (qty_available <= 0)
        $recentMovements = collect();

        try {
            // KPIs from balances
            $balances = StockBalance::query();

            // Distinct items in stock and out of stock
            $inStockCount = (clone $balances)
                ->where('qty_available', '>', 0)
                ->distinct('stock_item_id')
                ->count('stock_item_id');

            $outOfStockCount = (clone $balances)
                ->where(function ($q) {
                    $q->where('qty_available', '<=', 0)
                      ->orWhere(function ($q2) {
                          $q2->whereNotNull('min')->whereColumn('qty_available', '<', 'min');
                      });
                })
                ->distinct('stock_item_id')
                ->count('stock_item_id');

            $criticalStockCount = $outOfStockCount;

            $lowStockCount = (clone $balances)
                ->whereNotNull('min')
                ->whereColumn('qty_available', '<', 'min')
                ->distinct('stock_item_id')
                ->count('stock_item_id');

            // Total stock value = sum(qty_available * dernier_cout)
            $totalValue = DB::table('stock_balances as sb')
                ->join('stock_items as si', 'si.id', '=', 'sb.stock_item_id')
                ->selectRaw('COALESCE(SUM(sb.qty_available * COALESCE(si.dernier_cout, 0)), 0) as total')
                ->value('total') ?? 0;

            // Total active references (items with any balance record)
            $totalProducts = (clone $balances)->distinct('stock_item_id')->count('stock_item_id');

            // Movements last 12 months aggregated by month/type
            $start = now()->startOfMonth()->subMonths(11);
            $movAgg = StockMovement::query()
                ->where('occurred_at', '>=', $start)
                ->selectRaw("strftime('%Y-%m', occurred_at) as ym, type, SUM(qty) as total")
                ->groupBy('ym', 'type')
                ->get()
                ->groupBy('ym');

            // Fill arrays matching labels
            foreach ($months as $idx => $dt) {
                $ym = $dt->format('Y-m');
                $g = $movAgg->get($ym, collect());
                $entrees[$idx] = (float) ($g->firstWhere('type', 'entry')->total ?? 0);
                $sorties[$idx] = (float) ($g->firstWhere('type', 'exit')->total ?? 0);
            }

            // Recent movements (latest 10)
            $recentMovements = StockMovement::with('item')
                ->orderByDesc('occurred_at')
                ->limit(10)
                ->get()
                ->map(function ($m) {
                    // Map types to legacy labels used by the view for colors
                    $legacy = [
                        'entry' => 'entree',
                        'exit' => 'sortie',
                        'transfer' => 'transfert',
                        'adjust' => 'ajustement',
                        'inventory' => 'inventaire',
                    ];
                    $m->type = $legacy[$m->type] ?? $m->type;
                    // Provide created_at fallback from occurred_at
                    if (!$m->created_at) { $m->created_at = $m->occurred_at; }
                    return $m;
                });
        } catch (\Throwable $e) {
            // Keep defaults if tables not present or any error occurs
        }

        return view('stock.dashboard', compact(
            'labels',
            'entrees',
            'sorties',
            'totalValue',
            'totalProducts',
            'lowStockCount',
            'criticalStockCount',
            'recentMovements'
        ));
    }
}
