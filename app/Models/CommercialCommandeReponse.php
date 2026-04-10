<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommercialCommandeReponse extends Model
{
    protected $table = 'commercial_commande_reponses';

    protected $fillable = [
        'commande_id',
        'commentaire',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function commande()
    {
        return $this->belongsTo(CommercialCommande::class, 'commande_id');
    }

    public function fournisseurs()
    {
        return $this->hasMany(CommercialCommandeFournisseur::class, 'reponse_id');
    }
}
