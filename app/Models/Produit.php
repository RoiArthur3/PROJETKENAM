<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $table = 'produits';

    protected $fillable = [
        'code',
        'designation',
        'categorie',
        'unite',
        'stock_min',
        'stock_actuel',
        'prix_unitaire',
        'emplacement',
        'description',
        'actif',
    ];

    protected $casts = [
        'stock_min' => 'integer',
        'stock_actuel' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'actif' => 'boolean',
    ];

    public function entrees()
    {
        return $this->hasMany(StockEntree::class);
    }

    public function sorties()
    {
        return $this->hasMany(StockSortie::class);
    }
}
