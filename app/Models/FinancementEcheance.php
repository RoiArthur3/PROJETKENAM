<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancementEcheance extends Model
{
    use HasFactory;

    protected $table = 'financement_echeances';

    protected $fillable = [
        'offre_id',
        'dossier_id',
        'numero_echeance',
        'date_echeance',
        'capital',
        'interet',
        'mensualite',
        'montant',
        'solde_restant',
        'statut',
        'notes',
        'date_paiement',
        'created_by',
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'date_paiement' => 'date',
        'capital' => 'decimal:2',
        'interet' => 'decimal:2',
        'mensualite' => 'decimal:2',
        'montant' => 'decimal:2',
        'solde_restant' => 'decimal:2',
    ];

    public function offre(): BelongsTo
    {
        return $this->belongsTo(FinancementOffreBancaire::class, 'offre_id');
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(FinancementDossier::class, 'dossier_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope pour les échéances à payer
     */
    public function scopeAPayer($query)
    {
        return $query->where('statut', 'a_payer');
    }

    /**
     * Scope pour les échéances en retard
     */
    public function scopeEnRetard($query)
    {
        return $query->where('statut', 'a_payer')
                    ->where('date_echeance', '<', now());
    }

    /**
     * Scope pour les échéances payées
     */
    public function scopePayees($query)
    {
        return $query->where('statut', 'paye');
    }

    /**
     * Marquer comme payée
     */
    public function marquerCommePayee()
    {
        $this->update([
            'statut' => 'paye',
            'date_paiement' => now(),
        ]);
    }
}
