<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Client;
use App\Models\Operation;
use App\Models\Invoice;

class Proforma extends Model
{
    protected $fillable = [
        'reference',
        'client_id',
        'projet_id',
        'date_proposition',
        'date_validite',
        'conditions_generales',
        'note',
        'total_ht',
        'total_tva',
        'total_ttc',
        'montant_lettres',
        'statut',
        'invoice_id',
    ];

    protected $casts = [
        'date_proposition' => 'date',
        'date_validite' => 'date',
        'total_ht' => 'decimal:2',
        'total_tva' => 'decimal:2',
        'total_ttc' => 'decimal:2'
    ];

    /**
     * Relation avec les items de proforma
     */
    public function items(): HasMany
    {
        return $this->hasMany(ProformaItem::class);
    }

    /**
     * Relation avec le client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec le projet (opération)
     */
    public function projet(): BelongsTo
    {
        return $this->belongsTo(Operation::class, 'projet_id');
    }

    /**
     * Relation avec la facture générée
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Générer une nouvelle référence
     */
    public static function generateReference(): string
    {
        $year = date('Y');
        $lastProforma = self::where('reference', 'like', "PR{$year}%")
                          ->orderBy('id', 'desc')
                          ->first();

        if ($lastProforma) {
            $lastNumber = intval(substr($lastProforma->reference, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "PR{$year}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculer les totaux
     */
    public function calculateTotals(): void
    {
        $totalHT = 0;
        $totalTVA = 0;

        foreach ($this->items as $item) {
            $totalHT += $item->total_ht;
            $totalTVA += ($item->total_ht * $item->tva / 100);
        }

        $this->total_ht = $totalHT;
        $this->total_tva = $totalTVA;
        $this->total_ttc = $totalHT + $totalTVA;

        // Convertir en lettres (simplifié)
        $this->montant_lettres = $this->numberToWords($this->total_ttc);

        $this->save();
    }

    /**
     * Convertir un nombre en lettres (version simplifiée)
     */
    private function numberToWords(float $number): string
    {
        // Version simplifiée - on pourrait utiliser une bibliothèque dédiée
        return number_format($number, 2, ',', ' ') . ' euros';
    }

    /**
     * Vérifier si la proforma est expirée
     */
    public function isExpired(): bool
    {
        return $this->date_validite < now();
    }

    /**
     * Dupliquer la proforma
     */
    public function duplicate(): self
    {
        $newProforma = $this->replicate();
        $newProforma->reference = self::generateReference();
        $newProforma->statut = 'brouillon';
        $newProforma->date_proposition = now();
        $newProforma->date_validite = now()->addDays(30);
        $newProforma->save();

        // Dupliquer les items
        foreach ($this->items as $item) {
            $newItem = $item->replicate();
            $newItem->proforma_id = $newProforma->id;
            $newItem->save();
        }

        $newProforma->calculateTotals();

        return $newProforma;
    }

    /**
     * Générer une facture à partir de cette proforma
     */
    public function createInvoiceFromProforma(): Invoice
    {
        if ($this->invoice) {
            return $this->invoice;
        }

        $baseAmount = (float) $this->total_ht;
        $taxAmount = (float) $this->total_tva;
        $netAmount = (float) $this->total_ttc;

        $invoice = Invoice::create([
            'invoice_number'   => $this->reference, // Référence de la proforma utilisée comme numéro de facture
            'type'             => 'client',
            'client_id'        => $this->client_id,
            'operation_id'     => $this->projet_id,
            'description'      => 'Facture issue de la proforma ' . $this->reference,
            'quantity'         => 1,
            'unit_price'       => $baseAmount,
            'total_amount'     => $baseAmount,
            'tax_rate'         => $baseAmount > 0 ? round($taxAmount * 100 / $baseAmount, 2) : 0,
            'tax_amount'       => $taxAmount,
            'discount'         => 0,
            'net_amount'       => $netAmount,
            'payment_method'   => null,
            'status'           => 'issued',
            'issue_date'       => now(),
            'due_date'         => now()->addDays(30),
            'notes'            => $this->note,
        ]);

        $this->invoice_id = $invoice->id;
        $this->statut = 'valide';
        $this->save();

        return $invoice;
    }
}
