<?php

namespace App\Listeners;

use App\Events\StockMovementCreated;
use App\Models\StockLevel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateStockLevels
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(StockMovementCreated $event): void
    {
        try {
            $movement = $event->movement;
            $product = $movement->product;
            $warehouse = $movement->warehouse;

            if (!$product || !$warehouse) {
                Log::warning('Stock movement without product or warehouse', [
                    'movement_id' => $movement->id
                ]);
                return;
            }

            // Obtenir ou créer le niveau de stock
            $stockLevel = StockLevel::firstOrCreate([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
            ], [
                'current_stock' => 0,
                'reserved_stock' => 0,
                'available_stock' => 0,
                'average_cost' => $movement->unit_price ?? 0,
                'total_value' => 0,
                'last_updated' => now(),
            ]);

            // Mettre à jour selon le type de mouvement
            switch ($movement->type) {
                case 'entree':
                    $stockLevel->addStock($movement->quantity, $movement->unit_price);
                    break;
                    
                case 'sortie':
                    if (!$stockLevel->removeStock($movement->quantity)) {
                        Log::warning('Insufficient stock for movement', [
                            'movement_id' => $movement->id,
                            'requested' => $movement->quantity,
                            'available' => $stockLevel->available_stock
                        ]);
                        return;
                    }
                    break;
                    
                case 'transfert':
                    // Pour les transferts, la sortie est gérée par un mouvement séparé
                    break;
                    
                case 'retour':
                    $stockLevel->addStock($movement->quantity, $stockLevel->average_cost);
                    break;
                    
                case 'ajustement':
                    // Ajustement positif ou négatif
                    if ($movement->quantity > 0) {
                        $stockLevel->addStock($movement->quantity);
                    } else {
                        $stockLevel->removeStock(abs($movement->quantity));
                    }
                    break;
            }

            Log::info('Stock levels updated', [
                'movement_id' => $movement->id,
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'new_stock' => $stockLevel->current_stock,
                'movement_type' => $movement->type
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update stock levels', [
                'movement_id' => $event->movement->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
