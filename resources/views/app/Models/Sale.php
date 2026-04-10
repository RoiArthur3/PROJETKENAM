<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'customer_name',
        'customer_phone',
        'customer_email',
        'total_amount',
        'discount_amount',
        'tax_amount',
        'net_amount',
        'payment_method',
        'payment_status',
        'sale_date',
        'notes',
        'user_id',
        'store_id',
        'invoice_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'sale_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Relation avec la facture générée
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Générer une référence unique pour la vente
     */
    public static function generateReference(): string
    {
        do {
            $reference = 'V-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Générer une facture pour cette vente
     */
    public function generateInvoice(): Invoice
    {
        // Vérifier si une facture existe déjà
        if ($this->invoice) {
            return $this->invoice;
        }

        // Créer une nouvelle facture
        $invoice = Invoice::create([
            'invoice_number' => 'FV-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT),
            'type' => 'sale', // Type spécifique pour les ventes
            'supplier_name' => $this->customer_name ?? 'Client anonyme',
            'operation_reference' => $this->reference,
            'description' => 'Vente de matériel - ' . $this->reference,
            'total_amount' => $this->total_amount,
            'tax_rate' => 18, // TVA 18%
            'tax_amount' => $this->tax_amount,
            'discount' => $this->discount_amount,
            'net_amount' => $this->net_amount,
            'payment_method' => $this->payment_method,
            'status' => $this->payment_status === 'paid' ? 'paid' : 'pending',
            'due_date' => now()->addDays(30), // 30 jours pour payer
            'issue_date' => $this->sale_date,
            'notes' => $this->notes ?? 'Facture générée automatiquement pour la vente ' . $this->reference,
        ]);

        // Mettre à jour la vente avec l'ID de la facture
        $this->update(['invoice_id' => $invoice->id]);

        return $invoice;
    }

    /**
     * Calculer les totaux de la vente
     */
    public function calculateTotals(): void
    {
        $totalAmount = 0;

        foreach ($this->items as $item) {
            $totalAmount += $item->calculateTotal();
        }

        $taxRate = 18; // TVA 18%
        $taxAmount = $totalAmount * ($taxRate / 100);
        $netAmount = $totalAmount + $taxAmount - $this->discount_amount;

        $this->total_amount = $totalAmount;
        $this->tax_amount = $taxAmount;
        $this->net_amount = $netAmount;
    }
}
