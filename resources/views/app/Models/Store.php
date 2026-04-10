<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'nom',
        'warehouse_id',
        'manager_id',
        'type',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function balances()
    {
        return $this->morphMany(StockBalance::class, 'location');
    }

    public function movements()
    {
        return $this->morphMany(StockMovement::class, 'to_location');
    }
}
