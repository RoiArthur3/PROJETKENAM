<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'nom',
        'unite',
        'categorie',
        'suit_serial',
        'suit_lot',
        'dernier_cout',
        'actif',
    ];

    protected $casts = [
        'suit_serial' => 'boolean',
        'suit_lot' => 'boolean',
        'dernier_cout' => 'decimal:2',
        'actif' => 'boolean',
    ];

    public function balances(): HasMany
    {
        return $this->hasMany(StockBalance::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // Compat: allow $item->name in legacy views
    public function getNameAttribute()
    {
        return $this->nom;
    }
}
