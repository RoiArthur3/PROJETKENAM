<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProformaItem extends Model
{
    protected $fillable = [
        'proforma_id',
        'designation',
        'tva',
        'prix_unitaire_ht',
        'quantite',
        'unite',
        'total_ht'
    ];

    protected $casts = [
        'prix_unitaire_ht' => 'decimal:2',
        'quantite' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'tva' => 'integer'
    ];

    /**
     * Relation avec la proforma
     */
    public function proforma(): BelongsTo
    {
        return $this->belongsTo(Proforma::class);
    }

    /**
     * Calculer le total HT automatiquement
     */
    public function calculateTotal(): float
    {
        return $this->prix_unitaire_ht * $this->quantite;
    }

    /**
     * Mettre à jour le total avant sauvegarde
     */
    protected static function booted()
    {
        static::saving(function ($item) {
            $item->total_ht = $item->calculateTotal();
        });
    }
}
