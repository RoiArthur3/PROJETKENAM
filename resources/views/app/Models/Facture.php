<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_facture',
        'client_id',
        'operation_id',
        'date_facture',
        'date_echeance',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
        'statut', // 'payée', 'impayée', 'partiellement_payée', 'annulée'
        'mode_paiement',
        'date_paiement',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date_facture' => 'date',
        'date_echeance' => 'date',
        'date_paiement' => 'date',
        'montant_ht' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
    ];

    /**
     * Relation avec le client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec l'opération
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    /**
     * Relation avec l'utilisateur qui a créé la facture
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope pour les factures payées
     */
    public function scopePayee($query)
    {
        return $query->where('statut', 'payée');
    }

    /**
     * Scope pour les factures impayées
     */
    public function scopeImpayee($query)
    {
        return $query->where('statut', 'impayée');
    }

    /**
     * Scope pour les factures en retard
     */
    public function scopeEnRetard($query)
    {
        return $query->where('statut', 'impayée')
                    ->where('date_echeance', '<', now());
    }

    /**
     * Obtenir le format du numéro de facture
     */
    public function getFormattedNumeroAttribute()
    {
        return 'FAC-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Vérifier si la facture est en retard
     */
    public function estEnRetard(): bool
    {
        return $this->statut === 'impayée' && $this->date_echeance < now();
    }
}
