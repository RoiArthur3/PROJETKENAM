<?php

namespace App\Listeners;

use App\Events\StockMovementForOperation;
use App\Models\Operation;
use Illuminate\Support\Facades\Log;

class UpdateOperationCostsOnStockMovement
{
    /**
     * Handle the event.
     */
    public function handle(StockMovementForOperation $event): void
    {
        try {
            $operation = $event->operation;
            
            if (!$operation) {
                return;
            }

            // Calculer le coût total des mouvements de stock pour cette opération
            $totalStockCost = $operation->stockMovements()
                ->where('type', 'sortie')
                ->sum('cout_total');

            // Mettre à jour le coût réel de l'opération
            $operation->update([
                'cout_reel' => $totalStockCost
            ]);

            Log::info('Coûts d\'opération mis à jour', [
                'operation_id' => $operation->id,
                'stock_cost' => $totalStockCost
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des coûts d\'opération', [
                'stock_movement_id' => $event->stockMovement->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
