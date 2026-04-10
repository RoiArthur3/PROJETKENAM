<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockSortie extends Model
{
    use HasFactory;

    protected $table = 'stock_sorties';

    protected $fillable = [
        'date',
        'type',
        'produit_id',
        'produit_nom',
        'quantite',
        'destinataire',
        'demandeur',
        'motif',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'quantite' => 'decimal:2',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
