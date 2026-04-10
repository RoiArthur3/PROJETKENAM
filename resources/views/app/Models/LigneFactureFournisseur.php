<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LigneFactureFournisseur extends Model
{
    use HasFactory;

    protected $fillable = [
        'facture_id',
        'ligne_commande_id',
        'quantite',
        'prix_unitaire_ht',
        'tva_taux',
        'remise',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
        'description',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'prix_unitaire_ht' => 'decimal:2',
        'tva_taux' => 'decimal:2',
        'remise' => 'decimal:2',
        'montant_ht' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
    ];

    public function facture()
    {
        return $this->belongsTo(FactureFournisseur::class);
    }

    public function ligneCommande()
    {
        return $this->belongsTo(LigneCommandeFournisseur::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
