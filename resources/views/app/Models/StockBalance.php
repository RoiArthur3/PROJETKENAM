<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_item_id',
        'location_type',
        'location_id',
        'qty_available',
        'qty_reserved',
        'min',
        'max',
    ];

    protected $casts = [
        'qty_available' => 'decimal:3',
        'qty_reserved' => 'decimal:3',
        'min' => 'decimal:3',
        'max' => 'decimal:3',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function location(): MorphTo
    {
        return $this->morphTo();
    }
}
