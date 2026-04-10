<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfert extends Model
{
    use HasFactory;

    protected $table = 'stock_transferts';

    protected $fillable = [
        'date',
        'produit_id',
        'produit_nom',
        'quantite',
        'entrepot_source',
        'entrepot_destination',
        'motif',
        'statut',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'quantite' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
