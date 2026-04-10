<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_item_id',
        'type', // entry, exit, transfer, adjust, inventory
        'qty',
        'from_location_type',
        'from_location_id',
        'to_location_type',
        'to_location_id',
        'reference_type',
        'reference_id',
        'user_id',
        'occurred_at',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'occurred_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromLocation(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'from_location_type', 'from_location_id');
    }

    public function toLocation(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'to_location_type', 'to_location_id');
    }

    /**
     * Scope pour les entrées
     */
    public function scopeEntries($query)
    {
        return $query->where('type', 'entry');
    }

    /**
     * Scope pour les sorties
     */
    public function scopeExits($query)
    {
        return $query->where('type', 'exit');
    }

    /**
     * Scope pour les transferts
     */
    public function scopeTransfers($query)
    {
        return $query->where('type', 'transfert');
    }

    // Compat helpers for existing dashboard view if needed
    public function getQuantityAttribute()
    {
        return $this->qty;
    }

    public function getProductAttribute()
    {
        return $this->item; // alias
    }
}
