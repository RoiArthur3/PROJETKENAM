<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockLevel;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Liste des produits
     */
    public function index(Request $request)
    {
        // Filtres
        $query = Product::with(['category'])
            ->withSum('stockLevels', 'current_stock')
            ->withSum('stockLevels', 'total_value')
            ->withSum('stockLevels', 'reserved_stock');

        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(50);

        // Données pour KPIs
        $totalProducts = Product::count();
        $totalValue = Product::withSum('stockLevels', 'total_value')->get()->sum('stock_levels_sum_total_value') ?? 0;
        $activeProducts = Product::where('status', 'active')->count();
        $lowStockProducts = \App\Models\StockLevel::where('low_stock_alert', true)->distinct('product_id')->count('product_id');

        // Données pour filtres
        $categories = ProductCategory::orderBy('name')->get();

        return view('stock.products.index', compact('products', 'totalProducts', 'totalValue', 'activeProducts', 'lowStockProducts', 'categories'));
    }

    /**
     * Détails d'un produit
     */
    public function show(Product $product)
    {
        // Stock par entrepôt
        $stockByWarehouse = $product->stockLevels()
            ->with('warehouse')
            ->get()
            ->map(function ($stockLevel) {
                return [
                    'warehouse' => $stockLevel->warehouse,
                    'current_stock' => $stockLevel->current_stock,
                    'reserved_stock' => $stockLevel->reserved_stock,
                    'available_stock' => $stockLevel->available_stock,
                    'total_value' => $stockLevel->total_value,
                    'average_cost' => $stockLevel->average_cost,
                ];
            });

        // Statistiques globales
        $stats = [
            'total_stock' => $product->stockLevels()->sum('current_stock'),
            'total_value' => $product->stockLevels()->sum('total_value'),
            'warehouses_count' => $product->stockLevels()->count(),
            'low_stock_warehouses' => $product->stockLevels()->where('low_stock_alert', true)->count(),
            'critical_stock_warehouses' => $product->stockLevels()->where('critical_stock_alert', true)->count(),
        ];

        // Mouvements récents
        $recentMovements = $product->stockMovements()
            ->with(['warehouse', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('stock.products.show', compact(
            'product',
            'stockByWarehouse',
            'stats',
            'recentMovements'
        ));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $categories = ProductCategory::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('stock.products.create', compact('categories', 'suppliers'));
    }

    /**
     * Créer un produit
     */
    public function store(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|max:50|unique:products,reference',
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string|max:1000',
            'unit' => 'required|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'min_stock_level' => 'nullable|numeric|min:0',
            'max_stock_level' => 'nullable|numeric|min:0',
            'reorder_point' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'track_stock' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $product = Product::create($request->all());

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produit créé avec succès');
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Product $product)
    {
        $categories = ProductCategory::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('stock.products.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Mettre à jour un produit
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'reference' => 'required|string|max:50|unique:products,reference,' . $product->id,
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string|max:1000',
            'unit' => 'required|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'min_stock_level' => 'nullable|numeric|min:0',
            'max_stock_level' => 'nullable|numeric|min:0',
            'reorder_point' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'track_stock' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $product->update($request->all());

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produit mis à jour avec succès');
    }

    /**
     * Supprimer un produit
     */
    public function destroy(Product $product)
    {
        // Vérifier s'il y a du stock
        if ($product->stockLevels()->where('current_stock', '>', 0)->exists()) {
            return back()->with('error', 'Impossible de supprimer un produit contenant du stock');
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produit supprimé avec succès');
    }

    /**
     * Historique des mouvements d'un produit
     */
    public function movements(Product $product)
    {
        $movements = $product->stockMovements()
            ->with(['warehouse', 'user', 'supplier', 'operation'])
            ->orderBy('movement_date', 'desc')
            ->paginate(50);

        return view('stock.products.movements', compact('product', 'movements'));
    }

    /**
     * API pour obtenir le stock d'un produit
     */
    public function stock(Product $product)
    {
        $stockLevels = $product->stockLevels()
            ->with('warehouse')
            ->get()
            ->map(function ($stockLevel) {
                return [
                    'warehouse_id' => $stockLevel->warehouse_id,
                    'warehouse_name' => $stockLevel->warehouse->name,
                    'current_stock' => $stockLevel->current_stock,
                    'available_stock' => $stockLevel->available_stock,
                    'reserved_stock' => $stockLevel->reserved_stock,
                    'total_value' => $stockLevel->total_value,
                    'status' => $stockLevel->stock_status,
                ];
            });

        return response()->json([
            'product' => $product,
            'stock_levels' => $stockLevels,
            'total_stock' => $stockLevels->sum('current_stock'),
            'total_value' => $stockLevels->sum('total_value'),
        ]);
    }
}
