<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\StockLevel;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Tableau de bord du stock
     */
    public function dashboard()
    {
        // KPIs - Utiliser les vraies colonnes de la table produits
        $totalProducts = \App\Models\Product::where('actif', 1)->count();

        // Pour la valeur totale, calculer à partir des produits actuels
        $totalValue = \App\Models\Product::where('actif', 1)
            ->selectRaw('SUM(stock_actuel * COALESCE(prix_unitaire, 0)) as total_value')
            ->value('total_value') ?? 0;

        // Alertes basées sur les niveaux de stock minimums configurés
        $lowStockCount = \App\Models\Product::where('stock_min', '>', 0)->count();
        $criticalStockCount = \App\Models\Product::whereRaw('stock_actuel <= stock_min')->count();

        // Mouvements récents - Utiliser les mouvements de caisse disponibles
        $recentMovements = collect(); // Vide pour l'instant

        // Alertes de stock - Produits avec niveaux de stock configurés
        $stockAlerts = \App\Models\Product::where('stock_min', '>', 0)
            ->whereRaw('stock_actuel <= stock_min')
            ->orderBy('stock_actuel', 'asc')
            ->limit(10)
            ->get();

        return view('stock.dashboard', compact(
            'totalValue',
            'totalProducts',
            'lowStockCount',
            'criticalStockCount',
            'recentMovements',
            'stockAlerts'
        ));
    }

    /**
     * Page d'index du stock
     */
    public function index()
    {
        $products = \App\Models\Product::where('actif', 1)
            ->with(['category', 'warehouse'])
            ->paginate(20);

        return view('stock.index', compact('products'));
    }

    /**
     * Liste des entrepôts
     */
    public function warehouses(Request $request)
    {
        $query = Warehouse::withCount('balances');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $warehouses = $query->paginate(15)->withQueryString();

        return view('stock.warehouses.index', compact('warehouses'));
    }

    /**
     * Formulaire de création d'entrepôt
     */
    public function createWarehouse()
    {
        return view('stock.warehouses.create');
    }

    /**
     * Enregistrer un nouvel entrepôt
     */
    public function storeWarehouse(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            Warehouse::create([
                'name' => $request->name,
                'location' => $request->location,
                'capacity' => $request->capacity,
                'description' => $request->description,
                'actif' => true, // Par défaut actif
            ]);

            return redirect()
                ->route('stock.warehouses.index')
                ->with('success', 'Entrepôt créé avec succès');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher un entrepôt
     */
    public function showWarehouse(Warehouse $warehouse)
    {
        $warehouse->load(['balances.product']);

        return view('stock.warehouses.show', compact('warehouse'));
    }

    /**
     * Formulaire d'édition d'entrepôt
     */
    public function editWarehouse(Warehouse $warehouse)
    {
        return view('stock.warehouses.edit', compact('warehouse'));
    }

    /**
     * Mettre à jour un entrepôt
     */
    public function updateWarehouse(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'actif' => 'boolean',
        ]);

        try {
            $warehouse->update([
                'name' => $request->name,
                'location' => $request->location,
                'capacity' => $request->capacity,
                'description' => $request->description,
                'actif' => $request->has('actif'),
            ]);

            return redirect()
                ->route('stock.warehouses.show', $warehouse)
                ->with('success', 'Entrepôt mis à jour avec succès');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un entrepôt
     */
    public function destroyWarehouse(Warehouse $warehouse)
    {
        try {
            // Vérifier si l'entrepôt a du stock
            if ($warehouse->balances()->count() > 0) {
                return back()->with('error', 'Impossible de supprimer un entrepôt qui contient du stock');
            }

            $warehouse->delete();

            return redirect()
                ->route('stock.warehouses.index')
                ->with('success', 'Entrepôt supprimé avec succès');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Liste des produits
     */
    public function products()
    {
        $products = \App\Models\Product::with(['category', 'stockLevels'])
            ->withSum('stockLevels', 'current_stock')
            ->withSum('stockLevels', 'total_value')
            ->get();

        return view('stock.products.index', compact('products'));
    }

    /**
     * Mouvements d'entrée
     */
    public function entries()
    {
        // Utiliser StockEntree si StockMovement n'existe pas
        try {
            $entries = \App\Models\StockMovement::where('movement_type', 'entry')
                ->with(['product', 'warehouse', 'user'])
                ->orderBy('movement_date', 'desc')
                ->paginate(25);
        } catch (\Exception $e) {
            // Utiliser StockEntree à la place
            $entries = \App\Models\StockEntree::orderBy('date', 'desc')
                ->paginate(25);
        }

        return view('stock.entries.index', compact('entries'));
    }

    /**
     * Formulaire d'entrée de stock
     */
    public function createEntry()
    {
        $products = \App\Models\Product::where('status', 'active')->get();

        // Pour les entrepôts, utiliser une approche simple
        try {
            $warehouses = Warehouse::where('actif', true)->get();
        } catch (\Exception $e) {
            $warehouses = collect([
                (object)['id' => 1, 'name' => 'Entrepôt Principal'],
                (object)['id' => 2, 'name' => 'Entrepôt Secondaire'],
            ]);
        }

        // Pour les fournisseurs, utiliser Fournisseur si Supplier n'existe pas
        try {
            $suppliers = \App\Models\Supplier::all();
        } catch (\Exception $e) {
            $suppliers = \App\Models\Fournisseur::withTrashed()->get();
        }

        return view('stock.entries.create', compact('products', 'warehouses', 'suppliers'));
    }

    /**
     * Enregistrer une entrée de stock
     */
    public function storeEntry(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'nullable|numeric|min:0',
            'movement_date' => 'required|date',
            'reason' => 'nullable|string|max:255',
            'document_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $movement = $this->stockService->createEntry($request->all());

            return redirect()
                ->route('stock.entries')
                ->with('success', 'Entrée de stock enregistrée avec succès');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    /**
     * Mouvements de sortie
     */
    public function exits()
    {
        // Utiliser StockSortie si StockMovement n'existe pas
        try {
            $exits = \App\Models\StockMovement::where('movement_type', 'exit')
                ->with(['product', 'warehouse', 'user'])
                ->orderBy('movement_date', 'desc')
                ->paginate(25);
        } catch (\Exception $e) {
            // Utiliser StockSortie à la place
            $exits = \App\Models\StockSortie::with(['produit'])
                ->orderBy('date_sortie', 'desc')
                ->paginate(25);
        }

        return view('stock.exits.index', compact('exits'));
    }

    /**
     * Formulaire de sortie de stock
     */
    public function createExit()
    {
        $products = \App\Models\Product::where('status', 'active')->get();

        // Pour les entrepôts, utiliser une approche simple
        try {
            $warehouses = Warehouse::where('actif', true)->get();
        } catch (\Exception $e) {
            $warehouses = collect([
                (object)['id' => 1, 'name' => 'Entrepôt Principal'],
                (object)['id' => 2, 'name' => 'Entrepôt Secondaire'],
            ]);
        }

        // Pour les opérations
        try {
            $operations = \App\Models\Operation::where('statut_courant', 'en_cours')->get();
        } catch (\Exception $e) {
            $operations = collect();
        }

        return view('stock.exits.create', compact('products', 'warehouses', 'operations'));
    }

    /**
     * Enregistrer une sortie de stock
     */
    public function storeExit(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'operation_id' => 'nullable|exists:operations,id',
            'quantity' => 'required|numeric|min:0.01',
            'movement_date' => 'required|date',
            'reason' => 'nullable|string|max:255',
            'document_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $movement = $this->stockService->createExit($request->all());

            return redirect()
                ->route('stock.exits')
                ->with('success', 'Sortie de stock enregistrée avec succès');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    /**
     * Transferts de stock
     */
    public function transfers()
    {
        $transfers = \App\Models\StockMovement::transfers()
            ->with(['product', 'warehouse', 'user'])
            ->orderBy('movement_date', 'desc')
            ->paginate(25);

        return view('stock.transfers.index', compact('transfers'));
    }

    /**
     * Formulaire de transfert
     */
    public function createTransfer()
    {
        $products = \App\Models\Product::where('status', 'active')->get();

        // Pour les entrepôts, utiliser une approche simple si la table warehouses est vide
        try {
            $warehouses = Warehouse::where('actif', true)->get();
        } catch (\Exception $e) {
            // Créer des entrepôts de démonstration si la table est vide
            $warehouses = collect([
                (object)['id' => 1, 'name' => 'Entrepôt Principal'],
                (object)['id' => 2, 'name' => 'Entrepôt Secondaire'],
                (object)['id' => 3, 'name' => 'Magasin Central'],
            ]);
        }

        return view('stock.transfers.create', compact('products', 'warehouses'));
    }

    /**
     * Effectuer un transfert de stock
     */
    public function storeTransfer(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_warehouse_id' => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0.01',
            'movement_date' => 'required|date',
            'document_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->stockService->transferStock($request->all());

            return redirect()
                ->route('stock.transfers')
                ->with('success', 'Transfert de stock effectué avec succès');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors du transfert: ' . $e->getMessage());
        }
    }

    /**
     * Inventaire
     */
    public function inventory()
    {
        $stockLevels = StockLevel::with(['product', 'warehouse'])
            ->orderBy('warehouse_id')
            ->orderBy('product_id')
            ->paginate(50);

        return view('stock.inventory.index', compact('stockLevels'));
    }

    /**
     * API pour vérifier la disponibilité du stock
     */
    public function checkStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        $stockLevel = StockLevel::where([
            'product_id' => $request->product_id,
            'warehouse_id' => $request->warehouse_id,
        ])->first();

        return response()->json([
            'available' => $stockLevel ? $stockLevel->available_stock : 0,
            'current' => $stockLevel ? $stockLevel->current_stock : 0,
            'reserved' => $stockLevel ? $stockLevel->reserved_stock : 0,
        ]);
    }
}
