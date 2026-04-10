<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'operation_id',
        'payment_type',
        'payment_date',
        'amount_paid',
        'payment_method',
        'reference',
        'status',
        'notes',
        'attachment_path',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    /**
     * Relation avec la facture
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Relation avec l'opération
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    /**
     * Obtenir le libellé du type de paiement
     */
    public function getPaymentTypeLabelAttribute(): string
    {
        return match($this->payment_type) {
            'invoice' => 'Facture',
            'operation' => 'Opération',
            'both' => 'Facture + Opération',
            default => 'Inconnu',
        };
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'processing' => 'En cours',
            'completed' => 'Complété',
            'failed' => 'Échoué',
            'cancelled' => 'Annulé',
            default => 'Inconnu',
        };
    }

    /**
     * Obtenir le libellé de la méthode de paiement
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'Espèces',
            'bank_transfer' => 'Virement bancaire',
            'check' => 'Chèque',
            'mobile_money' => 'Mobile Money',
            'other' => 'Autre',
            default => 'Inconnu',
        };
    }

    /**
     * Scope pour les paiements d'opérations
     */
    public function scopeForOperations($query)
    {
        return $query->where('payment_type', 'operation')
                    ->orWhere('payment_type', 'both');
    }

    /**
     * Scope pour les paiements de factures
     */
    public function scopeForInvoices($query)
    {
        return $query->where('payment_type', 'invoice')
                    ->orWhere('payment_type', 'both');
    }

    /**
     * Scope pour les paiements complétés
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour les paiements en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
