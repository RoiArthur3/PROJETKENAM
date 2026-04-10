<?php

namespace App\Events;

use App\Models\StockMovement;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockMovementForOperation
{
    use Dispatchable, SerializesModels;

    public $stockMovement;
    public $operation;

    /**
     * Create a new event instance.
     */
    public function __construct(StockMovement $stockMovement)
    {
        $this->stockMovement = $stockMovement;
        $this->operation = $stockMovement->operation;
    }
}
