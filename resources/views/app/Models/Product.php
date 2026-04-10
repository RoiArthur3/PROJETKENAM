<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'produits';

    protected $fillable = [
        'code',
        'designation',
        'description',
        'categorie',
        'unite',
        'prix_unitaire',
        'stock_min',
        'stock_actuel',
        'emplacement',
        'actif',
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'stock_min' => 'integer',
        'stock_actuel' => 'integer',
        'actif' => 'boolean',
    ];

    /**
     * Relation avec la catégorie
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    /**
     * Relation avec le fournisseur
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relations avec les niveaux de stock
     */
    public function stockLevels(): HasMany
    {
        return $this->hasMany(StockLevel::class);
    }

    /**
     * Relations avec les mouvements de stock
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Scope pour les produits actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Obtenir le stock total tous entrepôts confondus
     */
    public function getTotalStockAttribute(): float
    {
        return $this->stockLevels()->sum('current_stock');
    }

    /**
     * Obtenir la valeur totale du stock
     */
    public function getTotalStockValueAttribute(): float
    {
        return $this->stockLevels()->sum('total_value');
    }

    /**
     * Vérifier si le stock est bas
     */
    public function isLowStock(): bool
    {
        return $this->stockLevels()
            ->where('low_stock_alert', true)
            ->exists();
    }

    /**
     * Vérifier si le stock est critique
     */
    public function isCriticalStock(): bool
    {
        return $this->stockLevels()
            ->where('critical_stock_alert', true)
            ->exists();
    }
}
