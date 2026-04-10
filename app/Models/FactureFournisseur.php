<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FactureFournisseur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'fournisseur_id',
        'commande_id',
        'numero_facture',
        'date_facture',
        'date_echeance',
        'conditions_paiement',
        'frais_livraison',
        'remise',
        'type_remise',
        'montant_ht',
        'tva',
        'montant_ttc',
        'montant_paye',
        'reste_a_payer',
        'statut',
        'date_paiement',
        'notes',
        'motif_annulation',
        'user_id',
    ];

    protected $casts = [
        'date_facture' => 'date',
        'date_echeance' => 'date',
        'date_paiement' => 'date',
        'frais_livraison' => 'decimal:2',
        'remise' => 'decimal:2',
        'montant_ht' => 'decimal:2',
        'tva' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'reste_a_payer' => 'decimal:2',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function commande()
    {
        return $this->belongsTo(CommandeFournisseur::class);
    }

    public function lignes()
    {
        return $this->hasMany(LigneFactureFournisseur::class);
    }

    public function paiements()
    {
        return $this->hasMany(PaiementFournisseur::class);
    }

    public function depensesCaisses()
    {
        return $this->hasMany(DepenseCaisse::class, 'facture_fournisseur_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getEstPayeeAttribute()
    {
        return $this->statut === 'payee';
    }

    public function getEstPartiellementPayeeAttribute()
    {
        return $this->statut === 'partiellement_payee';
    }

    public function getEstNonPayeeAttribute()
    {
        return $this->statut === 'non_payee';
    }

    public function getEstAnnuleeAttribute()
    {
        return $this->statut === 'annulee';
    }

    public function getEstEnRetardAttribute()
    {
        return $this->date_echeance < now() && !$this->est_payee;
    }
}
