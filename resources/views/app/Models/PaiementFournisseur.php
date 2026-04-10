<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaiementFournisseur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'fournisseur_id',
        'facture_id',
        'date_paiement',
        'montant',
        'mode_paiement',
        'bank_account_id',
        'reference_paiement',
        'date_encaissement',
        'est_encaisse',
        'est_annule',
        'motif_annulation',
        'notes',
        'validateur_id',
        'user_id',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'date_encaissement' => 'date',
        'montant' => 'decimal:2',
        'est_encaisse' => 'boolean',
        'est_annule' => 'boolean',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function facture()
    {
        return $this->belongsTo(FactureFournisseur::class);
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function depenseCaisse()
    {
        return $this->hasOne(DepenseCaisse::class, 'paiement_fournisseur_id');
    }

    public function getEstValideAttribute()
    {
        return $this->validateur_id !== null;
    }
}
