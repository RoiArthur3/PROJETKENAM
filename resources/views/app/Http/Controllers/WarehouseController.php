<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\StockBalance;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Liste des entrepôts
     */
    public function index()
    {
        $warehouses = Warehouse::withCount('stores')
            ->withCount('balances')
            ->get();

        return view('stock.warehouses.index', compact('warehouses'));
    }

    /**
     * Détails d'un entrepôt
     */
    public function show(Warehouse $warehouse)
    {
        // Statistiques
        $stats = [
            'total_products' => $warehouse->balances()->distinct('stock_item_id')->count('stock_item_id'),
            'total_value' => (float) $warehouse->balances()
                ->join('stock_items as si', 'si.id', '=', 'stock_balances.stock_item_id')
                ->selectRaw('COALESCE(SUM(stock_balances.qty_available * COALESCE(si.dernier_cout,0)),0) as total')
                ->value('total'),
            'total_quantity' => (float) $warehouse->balances()->sum('qty_available'),
            'low_stock_count' => $warehouse->balances()->whereNotNull('min')->whereColumn('qty_available','<','min')->count(),
            'critical_stock_count' => $warehouse->balances()->where('qty_available','<=',0)->count(),
        ];

        // Stock par catégorie
        $balances = $warehouse->balances()->with('item')->get();
        $stockByCategory = $balances
            ->groupBy(fn($b) => $b->item->categorie ?? 'Non catégorisé')
            ->map(function ($group) {
                $totalQty = $group->sum('qty_available');
                $totalValue = $group->sum(function ($b) { return ($b->item->dernier_cout ?? 0) * $b->qty_available; });
                return [
                    'products_count' => $group->pluck('stock_item_id')->unique()->count(),
                    'total_quantity' => $totalQty,
                    'total_value' => $totalValue,
                ];
            });

        // Mouvements récents
        $recentMovements = StockMovement::with(['item','user'])
            ->where(function($q) use ($warehouse) {
                $q->where(function($q1) use ($warehouse){
                    $q1->where('from_location_type', Warehouse::class)->where('from_location_id', $warehouse->id);
                })->orWhere(function($q2) use ($warehouse){
                    $q2->where('to_location_type', Warehouse::class)->where('to_location_id', $warehouse->id);
                });
            })
            ->orderByDesc('occurred_at')
            ->limit(20)
            ->get();

        return view('stock.warehouses.show', compact(
            'warehouse',
            'stats',
            'stockByCategory',
            'recentMovements'
        ));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('stock.warehouses.create');
    }

    /**
     * Créer un entrepôt
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:warehouses,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:principal,secondaire,depot,boutique',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'surface' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|numeric|min:0',
            'manager_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $warehouse = Warehouse::create($request->all());

        return redirect()
            ->route('warehouses.show', $warehouse)
            ->with('success', 'Entrepôt créé avec succès');
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Warehouse $warehouse)
    {
        return view('stock.warehouses.edit', compact('warehouse'));
    }

    /**
     * Mettre à jour un entrepôt
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:warehouses,code,' . $warehouse->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:principal,secondaire,depot,boutique',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'surface' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|numeric|min:0',
            'manager_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $warehouse->update($request->all());

        return redirect()
            ->route('warehouses.show', $warehouse)
            ->with('success', 'Entrepôt mis à jour avec succès');
    }

    /**
     * Supprimer un entrepôt
     */
    public function destroy(Warehouse $warehouse)
    {
        // Vérifier s'il y a du stock
        if ($warehouse->stockLevels()->where('current_stock', '>', 0)->exists()) {
            return back()->with('error', 'Impossible de supprimer un entrepôt contenant du stock');
        }

        $warehouse->delete();

        return redirect()
            ->route('warehouses.index')
            ->with('success', 'Entrepôt supprimé avec succès');
    }

    /**
     * Stock d'un entrepôt
     */
    public function stock(Warehouse $warehouse)
    {
        $stockLevels = $warehouse->balances()
            ->with('item')
            ->orderBy('stock_item_id')
            ->paginate(50);

        return view('stock.warehouses.stock', compact('warehouse', 'stockLevels'));
    }

    /**
     * API pour obtenir les statistiques d'un entrepôt
     */
    public function stats(Warehouse $warehouse)
    {
        $stats = [
            'total_products' => $warehouse->balances()->distinct('stock_item_id')->count('stock_item_id'),
            'total_value' => (float) $warehouse->balances()
                ->join('stock_items as si', 'si.id', '=', 'stock_balances.stock_item_id')
                ->selectRaw('COALESCE(SUM(stock_balances.qty_available * COALESCE(si.dernier_cout,0)),0) as total')
                ->value('total'),
            'total_quantity' => (float) $warehouse->balances()->sum('qty_available'),
            'low_stock_count' => $warehouse->balances()->whereNotNull('min')->whereColumn('qty_available','<','min')->count(),
            'critical_stock_count' => $warehouse->balances()->where('qty_available','<=',0)->count(),
            'utilization_rate' => $warehouse->capacite && $warehouse->capacite > 0 
                ? ($warehouse->balances()->sum('qty_available') / $warehouse->capacite) * 100 
                : 0,
        ];

        return response()->json($stats);
    }
}
