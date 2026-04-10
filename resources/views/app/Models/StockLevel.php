<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'current_stock',
        'reserved_stock',
        'available_stock',
        'average_cost',
        'total_value',
        'last_entry_date',
        'last_exit_date',
        'last_updated',
        'low_stock_alert',
        'critical_stock_alert',
        'overstock_alert',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'reserved_stock' => 'decimal:2',
        'available_stock' => 'decimal:2',
        'average_cost' => 'decimal:2',
        'total_value' => 'decimal:2',
        'last_entry_date' => 'date',
        'last_exit_date' => 'date',
        'last_updated' => 'datetime',
        'low_stock_alert' => 'boolean',
        'critical_stock_alert' => 'boolean',
        'overstock_alert' => 'boolean',
    ];

    /**
     * Relation avec le produit
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relation avec l'entrepôt
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Mettre à jour le stock disponible
     */
    public function updateAvailableStock(): void
    {
        $this->available_stock = $this->current_stock - $this->reserved_stock;
        $this->save();
    }

    /**
     * Mettre à jour les alertes de stock
     */
    public function updateAlerts(): void
    {
        if (!$this->product) return;

        $minStock = $this->product->min_stock_level;
        $maxStock = $this->product->max_stock_level;

        $this->low_stock_alert = $this->current_stock <= $minStock && $this->current_stock > 0;
        $this->critical_stock_alert = $this->current_stock <= 0;
        $this->overstock_alert = $maxStock && $this->current_stock >= $maxStock;

        $this->save();
    }

    /**
     * Ajouter du stock (entrée)
     */
    public function addStock(float $quantity, float $unitCost = null): void
    {
        $this->current_stock += $quantity;
        
        if ($unitCost) {
            // Calculer le coût moyen pondéré
            $totalCost = $this->total_value + ($quantity * $unitCost);
            $this->current_stock > 0 
                ? $this->average_cost = $totalCost / $this->current_stock
                : $this->average_cost = $unitCost;
        }

        $this->total_value = $this->current_stock * $this->average_cost;
        $this->last_entry_date = now();
        $this->last_updated = now();

        $this->updateAvailableStock();
        $this->updateAlerts();
    }

    /**
     * Retirer du stock (sortie)
     */
    public function removeStock(float $quantity): bool
    {
        if ($this->available_stock < $quantity) {
            return false; // Stock insuffisant
        }

        $this->current_stock -= $quantity;
        $this->total_value = $this->current_stock * $this->average_cost;
        $this->last_exit_date = now();
        $this->last_updated = now();

        $this->updateAvailableStock();
        $this->updateAlerts();

        return true;
    }

    /**
     * Réserver du stock
     */
    public function reserveStock(float $quantity): bool
    {
        if ($this->available_stock < $quantity) {
            return false;
        }

        $this->reserved_stock += $quantity;
        $this->updateAvailableStock();
        $this->updateAlerts();

        return true;
    }

    /**
     * Libérer du stock réservé
     */
    public function releaseReservedStock(float $quantity): void
    {
        $this->reserved_stock = max(0, $this->reserved_stock - $quantity);
        $this->updateAvailableStock();
        $this->updateAlerts();
    }

    /**
     * Obtenir le statut du stock
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->critical_stock_alert) {
            return 'critical';
        } elseif ($this->low_stock_alert) {
            return 'low';
        } elseif ($this->overstock_alert) {
            return 'overstock';
        } elseif ($this->current_stock > 0) {
            return 'normal';
        } else {
            return 'out_of_stock';
        }
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getStockStatusLabelAttribute(): string
    {
        $labels = [
            'critical' => 'Stock critique',
            'low' => 'Stock bas',
            'normal' => 'Stock normal',
            'overstock' => 'Surstock',
            'out_of_stock' => 'Rupture de stock',
        ];

        return $labels[$this->stock_status] ?? 'Inconnu';
    }
}
