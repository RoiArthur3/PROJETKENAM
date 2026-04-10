<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'total_amount',
        'warehouse_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Calculer le total de l'article
     */
    public function calculateTotal(): float
    {
        return ($this->quantity * $this->unit_price) - $this->discount_amount;
    }

    /**
     * Vérifier la disponibilité en stock
     */
    public function isAvailable(): bool
    {
        if (!$this->warehouse_id) {
            return false;
        }

        $stockLevel = StockLevel::where('product_id', $this->product_id)
            ->where('warehouse_id', $this->warehouse_id)
            ->first();

        return $stockLevel && $stockLevel->available_stock >= $this->quantity;
    }
}
