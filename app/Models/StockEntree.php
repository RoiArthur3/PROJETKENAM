<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntree extends Model
{
    use HasFactory;

    protected $table = 'stock_entrees';

    protected $fillable = [
        'date',
        'type',
        'produit_id',
        'produit_nom',
        'quantite',
        'prix_unitaire',
        'montant_total',
        'fournisseur',
        'reference',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'quantite' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'montant_total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
