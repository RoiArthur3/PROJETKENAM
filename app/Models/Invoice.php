<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'type',
        'client_id',
        'supplier_name',
        'operation_reference',
        'operation_id', // Ajout de la relation avec l'opération
        'description',
        'quantity',
        'unit_price',
        'total_amount',
        'tax_rate',
        'tax_amount',
        'discount',
        'net_amount',
        'payment_method',
        'status',
        'due_date',
        'issue_date',
        'notes',
        'attachment',
    ];

    protected $casts = [
        'due_date' => 'date',
        'issue_date' => 'date',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    /**
     * Relation avec le client (utilisateur)
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Relation avec l'opération
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    /**
     * Relation avec les paiements
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Calculer le montant total payé
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments->sum('amount_paid');
    }

    /**
     * Calculer le montant restant à payer
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->net_amount - $this->total_paid);
    }

    /**
     * Vérifier si la facture a des paiements partiels
     */
    public function hasPartialPayments(): bool
    {
        return $this->payments()->count() > 0 && !$this->isPaid();
    }

    /**
     * Obtenir le pourcentage payé
     */
    public function getPaymentPercentageAttribute(): float
    {
        if ($this->net_amount == 0) return 0;
        return min(100, ($this->total_paid / $this->net_amount) * 100);
    }

    /**
     * Scope pour les factures avec paiements partiels
     */
    public function scopeWithPartialPayments($query)
    {
        return $query->whereHas('payments')->whereRaw('payments.amount_paid < invoices.net_amount');
    }
}
