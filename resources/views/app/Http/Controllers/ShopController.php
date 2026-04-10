<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use App\Models\StockLevel;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ShopController extends Controller
{
    /**
     * Page principale du magasin - affiche les articles disponibles à la vente
     */
    public function index()
    {
        // Récupérer le magasin par défaut
        $store = Store::where('code', 'SHOP')->first();

        if (!$store) {
            // Si aucun magasin n'existe, créer le magasin par défaut
            // On aura besoin d'un warehouse par défaut pour le magasin
            $defaultWarehouse = Warehouse::first(); // Premier warehouse disponible

            if (!$defaultWarehouse) {
                $products = collect();
                $productsByCategory = collect();
                $warehouses = collect();

                return view('stock.shop', compact('products', 'productsByCategory', 'warehouses'))
                    ->with('error', 'Aucun entrepôt configuré. Veuillez créer un entrepôt d\'abord.');
            }

            $store = Store::create([
                'code' => 'SHOP',
                'nom' => 'Magasin Central',
                'warehouse_id' => $defaultWarehouse->id,
                'type' => 'magasin',
                'actif' => true
            ]);
        }

        // Récupérer tous les produits qui ont du stock dans le magasin
        $products = Product::where('status', 'active')
            ->whereHas('stockLevels', function($query) use ($store) {
                $query->where('warehouse_id', $store->warehouse_id)
                      ->where('current_stock', '>', 0);
            })
            ->with(['category', 'stockLevels' => function($query) use ($store) {
                $query->where('warehouse_id', $store->warehouse_id);
            }])
            ->withSum(['stockLevels' => function($query) use ($store) {
                $query->where('warehouse_id', $store->warehouse_id);
            }], 'current_stock')
            ->having('stock_levels_sum_current_stock', '>', 0)
            ->get();

        // Grouper les produits par catégorie pour l'affichage
        $productsByCategory = $products->groupBy(function($product) {
            return $product->category ? $product->category->name : 'Sans catégorie';
        });

        // Récupérer tous les entrepôts pour les transferts
        $warehouses = Warehouse::where('actif', true)->get();

        return view('stock.shop', compact('products', 'productsByCategory', 'store', 'warehouses'));
    }

    /**
     * Transférer des articles de l'entrepôt vers le magasin
     */
    public function transferToShop(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);
            $warehouse = Warehouse::findOrFail($request->warehouse_id);

            // Vérifier que le stock est disponible dans l'entrepôt
            $stockLevel = StockLevel::where('product_id', $product->id)
                ->where('warehouse_id', $warehouse->id)
                ->first();

            if (!$stockLevel || $stockLevel->available_stock < $request->quantity) {
                throw new \Exception('Stock insuffisant dans l\'entrepôt sélectionné');
            }

            // Créer ou récupérer le magasin (on suppose qu'il y a un magasin par défaut)
            $store = Store::firstOrCreate([
                'code' => 'SHOP'
            ], [
                'nom' => 'Magasin Central',
                'warehouse_id' => $warehouse->id, // Associer au warehouse source par défaut
                'type' => 'magasin',
                'actif' => true
            ]);

            // Effectuer le transfert
            $this->transferStockToStore($product, $warehouse, $store, $request->quantity, $request->user());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transfert vers le magasin effectué avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Effectuer le transfert physique du stock
     */
    private function transferStockToStore($product, $warehouse, $store, $quantity, $user)
    {
        // Réduire le stock de l'entrepôt
        $warehouseStock = StockLevel::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        if ($warehouseStock) {
            $warehouseStock->current_stock -= $quantity;
            $warehouseStock->available_stock -= $quantity;
            $warehouseStock->save();
        }

        // Ajouter au stock du magasin
        $storeStock = StockLevel::firstOrNew([
            'product_id' => $product->id,
            'warehouse_id' => $store->warehouse_id,
        ]);

        $storeStock->current_stock += $quantity;
        $storeStock->available_stock += $quantity;
        $storeStock->total_value = ($storeStock->total_value ?? 0) + ($product->unit_price * $quantity);
        $storeStock->save();

        // Enregistrer le mouvement
        StockMovement::create([
            'product_id' => $product->id,
            'from_warehouse_id' => $warehouse->id,
            'to_warehouse_id' => $store->warehouse_id,
            'quantity' => $quantity,
            'movement_type' => 'transfer',
            'movement_date' => now(),
            'user_id' => $user->id ?? null,
            'notes' => 'Transfert vers magasin',
        ]);
    }

    /**
     * API pour vérifier le stock disponible dans un entrepôt
     */
    public function checkWarehouseStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        $stockLevel = StockLevel::where('product_id', $request->product_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->first();

        return response()->json([
            'available' => $stockLevel ? $stockLevel->available_stock : 0,
            'current' => $stockLevel ? $stockLevel->current_stock : 0,
        ]);
    }

    /**
     * API pour récupérer les entrepôts avec stock disponible pour un produit
     */
    public function getWarehousesWithStock(Request $request)
    {
        $productId = $request->query('product_id');

        if (!$productId) {
            return response()->json([]);
        }

        $warehouses = Warehouse::whereHas('stockLevels', function($query) use ($productId) {
            $query->where('product_id', $productId)
                  ->where('available_stock', '>', 0);
        })
        ->with(['stockLevels' => function($query) use ($productId) {
            $query->where('product_id', $productId);
        }])
        ->get();

        return response()->json($warehouses->map(function($warehouse) use ($productId) {
            $stockLevel = $warehouse->stockLevels->first();
            return [
                'id' => $warehouse->id,
                'name' => $warehouse->nom,
                'available_stock' => $stockLevel ? $stockLevel->available_stock : 0,
            ];
        }));
    }

    /**
     * Finaliser une vente
     */
    public function finalizeSale(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'payment_method' => 'required|in:cash,card,transfer,check',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Créer la vente
            $sale = Sale::create([
                'reference' => Sale::generateReference(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid', // Par défaut payé pour le magasin
                'sale_date' => now(),
                'user_id' => Auth::id(),
                'discount_amount' => $request->discount_amount ?? 0,
            ]);

            // Ajouter les articles
            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);

                // Trouver un entrepôt avec du stock disponible (priorité au magasin)
                $stockLevel = StockLevel::where('product_id', $product->id)
                    ->where('available_stock', '>=', $itemData['quantity'])
                    ->first();

                if (!$stockLevel) {
                    throw new \Exception("Stock insuffisant pour le produit: {$product->name}");
                }

                // Créer l'article de vente
                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $product->unit_price,
                    'total_amount' => $itemData['quantity'] * $product->unit_price,
                    'warehouse_id' => $stockLevel->warehouse_id,
                ]);

                // Réduire le stock
                $stockLevel->current_stock -= $itemData['quantity'];
                $stockLevel->available_stock -= $itemData['quantity'];
                $stockLevel->save();

                // Enregistrer le mouvement de sortie
                StockMovement::create([
                    'product_id' => $product->id,
                    'from_warehouse_id' => $stockLevel->warehouse_id,
                    'quantity' => $itemData['quantity'],
                    'movement_type' => 'exit',
                    'movement_date' => now(),
                    'user_id' => Auth::id(),
                    'notes' => 'Vente magasin - ' . $sale->reference,
                    'reason' => 'sale',
                    'document_reference' => $sale->reference,
                ]);
            }

            // Calculer les totaux
            $sale->calculateTotals();
            $sale->save();

            // Générer la facture automatiquement
            try {
                $invoice = $sale->generateInvoice();
            } catch (\Exception $e) {
                // Log l'erreur mais ne pas annuler la vente
                Log::error('Erreur lors de la génération de la facture pour la vente ' . $sale->reference . ': ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vente finalisée avec succès',
                'sale' => $sale->load('items.product'),
                'invoice_number' => $invoice->invoice_number ?? null,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtenir les détails d'une vente
     */
    public function getSale($id)
    {
        $sale = Sale::with(['items.product', 'user'])->findOrFail($id);

        return response()->json($sale);
    }

    /**
     * Afficher un reçu imprimable pour une vente magasin
     */
    public function receipt(Sale $sale)
    {
        $sale->load(['items.product', 'user', 'invoice']);

        return view('stock.shop.receipt', compact('sale'));
    }

    /**
     * Formulaire d'édition d'une vente magasin
     */
    public function editSale(Sale $sale)
    {
        $sale->load(['items.product', 'user', 'invoice']);

        return view('stock.shop.sale-edit', compact('sale'));
    }

    /**
     * Mettre à jour une vente magasin (infos client / paiement uniquement)
     */
    public function updateSale(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'payment_method' => 'required|in:cash,card,transfer,check',
            'payment_status' => 'required|in:pending,paid,partial,cancelled',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $sale->fill($data);
        $sale->save();

        return redirect()->route('stock.shop.sales')
            ->with('status', 'Vente mise à jour avec succès');
    }

    /**
     * Envoyer la vente à la comptabilité: générer/mettre à jour la facture et envoyer un email
     */
    public function sendSaleToAccounting(Sale $sale)
    {
        try {
            DB::beginTransaction();

            // Générer ou récupérer la facture
            if (!$sale->invoice) {
                $invoice = $sale->generateInvoice();
            } else {
                $invoice = $sale->invoice;
            }

            // Marquer la facture comme à valider / en attente côté compta si champ status existe
            if (isset($invoice->status)) {
                $invoice->status = 'a_valider';
                $invoice->save();
            }

            DB::commit();

            // Envoi email à la comptabilité (adresse configurable)
            $accountingEmail = config('mail.accounting_address', env('ACCOUNTING_EMAIL'));
            if ($accountingEmail) {
                try {
                    Mail::send('emails.accounting.sale-invoice-notification', [
                        'sale' => $sale->fresh('invoice', 'items.product'),
                        'invoice' => $invoice,
                    ], function ($message) use ($accountingEmail, $invoice) {
                        $subject = 'Nouvelle facture magasin à valider';
                        if ($invoice && $invoice->invoice_number) {
                            $subject .= ' - ' . $invoice->invoice_number;
                        }
                        $message->to($accountingEmail)
                            ->subject($subject);
                    });
                } catch (\Throwable $e) {
                    Log::error('Erreur envoi email compta pour vente '.$sale->id.' : '.$e->getMessage());
                }
            }

            return redirect()->route('stock.shop.sales')
                ->with('status', 'Vente envoyée à la comptabilité');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'envoi à la comptabilité: '.$e->getMessage());
        }
    }

    /**
     * Lister les ventes
     */
    public function sales(Request $request)
    {
        $sales = Sale::with(['items.product', 'user', 'invoice'])
            ->when($request->date_from, fn($q) => $q->whereDate('sale_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('sale_date', '<=', $request->date_to))
            ->when($request->payment_status, fn($q) => $q->where('payment_status', $request->payment_status))
            ->orderBy('sale_date', 'desc')
            ->paginate(25);

        return view('stock.shop.sales', compact('sales'));
    }

    /**
     * Traiter le formulaire de vente depuis la page web
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'quantities' => 'required|array|min:1',
            'prices' => 'required|array|min:1',
            'payment_method' => 'required|in:cash,card,transfer,check',
            'sale_date' => 'required|date',
        ]);

        // Préparer les données pour finalizeSale
        $saleData = [
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'payment_method' => $request->payment_method,
            'discount_amount' => 0, // Peut être ajouté plus tard
            'items' => [],
        ];

        foreach ($request->products as $index => $productId) {
            $saleData['items'][] = [
                'product_id' => $productId,
                'quantity' => $request->quantities[$index],
            ];
        }

        // Appeler finalizeSale
        $fakeRequest = new Request($saleData);
        $response = $this->finalizeSale($fakeRequest);

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getContent(), true);
            if ($data['success']) {
                return redirect()
                    ->route('shop')
                    ->with('success', 'Vente enregistrée avec succès. Facture générée et envoyée à la comptabilité pour validation.');
            }
        }

        // En cas d'erreur
        $errorData = json_decode($response->getContent(), true);
        return back()
            ->withInput()
            ->with('error', $errorData['message'] ?? 'Erreur lors de l\'enregistrement de la vente');
    }
}
