<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\Operation;
use App\Models\StockItem;
use App\Models\StockBalance;
use App\Models\AssetAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StockService
{
    /**
     * Créer un mouvement d'entrée de stock
     */
    public function createEntry(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $movement = StockMovement::create([
                'reference' => StockMovement::generateReference(),
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'supplier_id' => $data['supplier_id'] ?? null,
                'user_id' => Auth::check() ? Auth::id() : null,
                'type' => 'entree',
                'quantity' => $data['quantity'],
                'unit_price' => $data['unit_price'] ?? 0,
                'total_value' => ($data['quantity'] ?? 0) * ($data['unit_price'] ?? 0),
                'movement_date' => $data['movement_date'] ?? now(),
                'reason' => $data['reason'] ?? 'Entrée de stock',
                'document_reference' => $data['document_reference'] ?? null,
                'document_type' => $data['document_type'] ?? 'BL',
                'notes' => $data['notes'] ?? null,
            ]);

            // Mettre à jour les niveaux de stock
            $this->updateStockLevel($movement);

            Log::info('Stock entry created', [
                'movement_id' => $movement->id,
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity']
            ]);

            return $movement;
        });
    }

    /**
     * Créer un mouvement de sortie de stock
     */
    public function createExit(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            // Vérifier la disponibilité du stock
            $stockLevel = StockLevel::where([
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
            ])->first();

            if (!$stockLevel || $stockLevel->available_stock < $data['quantity']) {
                throw new \Exception('Stock insuffisant');
            }

            $movement = StockMovement::create([
                'reference' => StockMovement::generateReference(),
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'operation_id' => $data['operation_id'] ?? null,
                'user_id' => Auth::check() ? Auth::id() : null,
                'type' => 'sortie',
                'quantity' => $data['quantity'],
                'unit_price' => $stockLevel->average_cost,
                'total_value' => $data['quantity'] * $stockLevel->average_cost,
                'movement_date' => $data['movement_date'] ?? now(),
                'reason' => $data['reason'] ?? 'Sortie de stock',
                'document_reference' => $data['document_reference'] ?? null,
                'document_type' => $data['document_type'] ?? 'BL',
                'notes' => $data['notes'] ?? null,
            ]);

            // Mettre à jour les niveaux de stock
            $this->updateStockLevel($movement);

            Log::info('Stock exit created', [
                'movement_id' => $movement->id,
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity']
            ]);

            return $movement;
        });
    }

    /**
     * Transférer du stock entre entrepôts
     */
    public function transferStock(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Mouvement de sortie de l'entrepôt source
            $exitMovement = $this->createExit([
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['from_warehouse_id'],
                'quantity' => $data['quantity'],
                'reason' => 'Transfert vers ' . Warehouse::find($data['to_warehouse_id'])->name,
                'document_reference' => $data['document_reference'] ?? null,
            ]);

            // Mouvement d'entrée dans l'entrepôt de destination
            $entryMovement = StockMovement::create([
                'reference' => StockMovement::generateReference(),
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['to_warehouse_id'],
                'user_id' => Auth::check() ? Auth::id() : null,
                'type' => 'entree',
                'quantity' => $data['quantity'],
                'unit_price' => $exitMovement->unit_price,
                'total_value' => $exitMovement->total_value,
                'movement_date' => $data['movement_date'] ?? now(),
                'reason' => 'Transfert depuis ' . Warehouse::find($data['from_warehouse_id'])->name,
                'document_reference' => $data['document_reference'] ?? null,
                'document_type' => 'Transfert',
                'notes' => $data['notes'] ?? null,
            ]);

            // Mettre à jour les niveaux de stock pour l'entrée
            $this->updateStockLevel($entryMovement);

            Log::info('Stock transfer completed', [
                'product_id' => $data['product_id'],
                'from_warehouse' => $data['from_warehouse_id'],
                'to_warehouse' => $data['to_warehouse_id'],
                'quantity' => $data['quantity'],
                'exit_movement_id' => $exitMovement->id,
                'entry_movement_id' => $entryMovement->id,
            ]);

            return [$exitMovement, $entryMovement];
        });
    }

    /**
     * Mettre à jour le niveau de stock pour un mouvement
     */
    private function updateStockLevel(StockMovement $movement): void
    {
        $stockLevel = StockLevel::firstOrCreate([
            'product_id' => $movement->product_id,
            'warehouse_id' => $movement->warehouse_id,
        ], [
            'current_stock' => 0,
            'reserved_stock' => 0,
            'available_stock' => 0,
            'average_cost' => $movement->unit_price ?? 0,
            'total_value' => 0,
            'last_updated' => now(),
        ]);

        switch ($movement->type) {
            case 'entree':
                $stockLevel->addStock($movement->quantity, $movement->unit_price);
                break;
                
            case 'sortie':
                $stockLevel->removeStock($movement->quantity);
                break;
                
            case 'retour':
                $stockLevel->addStock($movement->quantity, $stockLevel->average_cost);
                break;
                
            case 'ajustement':
                if ($movement->quantity > 0) {
                    $stockLevel->addStock($movement->quantity);
                } else {
                    $stockLevel->removeStock(abs($movement->quantity));
                }
                break;
        }
    }

    /**
     * Obtenir les produits avec stock bas
     */
    public function getLowStockProducts(int $warehouseId = null): \Illuminate\Support\Collection
    {
        $query = StockLevel::with('product')
            ->where(function ($query) {
                $query->where('low_stock_alert', true)
                    ->orWhere('critical_stock_alert', true);
            });

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->get();
    }

    /**
     * Calculer la valeur totale du stock par entrepôt
     */
    public function getWarehouseStockValue(int $warehouseId = null): array
    {
        $query = StockLevel::with(['product', 'warehouse']);

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        $stockLevels = $query->get();

        return [
            'total_value' => $stockLevels->sum('total_value'),
            'total_products' => $stockLevels->count(),
            'low_stock_count' => $stockLevels->where('low_stock_alert', true)->count(),
            'critical_stock_count' => $stockLevels->where('critical_stock_alert', true)->count(),
        ];
    }

    /**
     * Créer une entrée de stock automatiquement depuis une opération validée
     */
    public static function createEntryFromOperation(Operation $operation): ?StockMovement
    {
        if (!$operation->produit_nom || !$operation->quantite) {
            Log::warning('Operation sans produit ou quantité', ['operation_id' => $operation->id]);
            return null;
        }

        return DB::transaction(function () use ($operation) {
            // 1. Créer ou récupérer StockItem
            $stockItem = StockItem::firstOrCreate([
                'name' => $operation->produit_nom,
            ], [
                'sku' => 'OP-' . $operation->id . '-' . now()->format('Ymd'),
                'category_id' => 1, // Catégorie par défaut
                'unit' => 'pcs',
                'min_stock' => 5,
            ]);

            // 2. Créer mouvement d'entrée
            $movement = \App\Models\StockMovement::create([
                'stock_item_id' => $stockItem->id,
                'type' => 'entry',
                'qty' => $operation->quantite,
                'from_location_type' => 'supplier',
                'from_location_id' => $operation->fournisseur_id,
                'to_location_type' => 'warehouse',
                'to_location_id' => 1, // Entrepôt principal
                'reference_type' => 'operation',
                'reference_id' => $operation->id,
                'user_id' => auth()->id(),
                'occurred_at' => now(),
            ]);

            // 3. Mettre à jour balance stock
            $balance = StockBalance::firstOrCreate([
                'stock_item_id' => $stockItem->id,
                'warehouse_id' => 1,
            ], [
                'qty_available' => 0,
                'qty_reserved' => 0,
                'unit_price' => $operation->montant_total ? ($operation->montant_total / $operation->quantite) : 0,
            ]);

            $balance->increment('qty_available', $operation->quantite);

            // 4. Mettre à jour statut livraison opération
            $operation->update([
                'statut_livraison' => 'complete',
                'date_livraison_effective' => now(),
            ]);

            Log::info('Stock entry created from operation', [
                'operation_id' => $operation->id,
                'stock_item_id' => $stockItem->id,
                'movement_id' => $movement->id,
                'quantity' => $operation->quantite,
            ]);

            return $movement;
        });
    }

    /**
     * Affecter un article à un utilisateur
     */
    public static function assignToUser(int $stockItemId, int $userId, string $assetTag, ?string $notes = null): AssetAssignment
    {
        return DB::transaction(function () use ($stockItemId, $userId, $assetTag, $notes) {
            // Vérifier disponibilité stock
            $balance = StockBalance::where('stock_item_id', $stockItemId)->first();
            if (!$balance || $balance->qty_available < 1) {
                throw new \Exception('Stock insuffisant pour affectation');
            }

            // Créer affectation
            $assignment = AssetAssignment::create([
                'stock_item_id' => $stockItemId,
                'user_id' => $userId,
                'asset_tag' => $assetTag,
                'assigned_at' => now(),
                'status' => 'assigned',
                'notes' => $notes,
            ]);

            // Réserver le stock
            $balance->decrement('qty_available');
            $balance->increment('qty_reserved');

            Log::info('Asset assigned to user', [
                'assignment_id' => $assignment->id,
                'stock_item_id' => $stockItemId,
                'user_id' => $userId,
                'asset_tag' => $assetTag,
            ]);

            return $assignment;
        });
    }
}
