<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommercialCommandeFournisseur extends Model
{
    protected $table = 'commercial_commande_fournisseurs';

    protected $fillable = [
        'reponse_id',
        'fournisseur_nom',
        'engin_disponible',
        'prix',
        'devise',
        'details',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reponse()
    {
        return $this->belongsTo(CommercialCommandeReponse::class, 'reponse_id');
    }
}
