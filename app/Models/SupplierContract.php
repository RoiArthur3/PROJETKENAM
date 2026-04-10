<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierContract extends Model
{
    use SoftDeletes;

    /**
     * Les types de contrats
     *
     * @var array<string, string>
     */
    public const TYPES = [
        'purchase' => 'Achat',
        'service' => 'Prestation de service',
        'maintenance' => 'Maintenance',
        'framework' => 'Accord-cadre',
        'other' => 'Autre',
    ];

    /**
     * Les statuts de contrat
     *
     * @var array<string, string>
     */
    public const STATUSES = [
        'draft' => 'Brouillon',
        'active' => 'Actif',
        'expired' => 'Expiré',
        'terminated' => 'Résilié',
        'renewed' => 'Renouvelé',
    ];

    /**
     * Les fréquences de renouvellement
     *
     * @var array<string, string>
     */
    public const RENEWAL_FREQUENCIES = [
        'monthly' => 'Mensuel',
        'quarterly' => 'Trimestriel',
        'semiannual' => 'Semestriel',
        'annual' => 'Annuel',
        'biennial' => 'Biennal',
        'triennial' => 'Triennal',
        'none' => 'Aucun',
    ];

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supplier_id',
        'contract_number',
        'name',
        'description',
        'type',
        'status',
        'start_date',
        'end_date',
        'auto_renewal',
        'renewal_frequency',
        'renewal_terms',
        'notice_period_days',
        'total_value',
        'currency',
        'payment_terms',
        'delivery_terms',
        'service_level_agreement',
        'penalty_clause',
        'termination_terms',
        'signed_date',
        'signed_by',
        'notes',
        'document_path',
        'created_by',
        'updated_by',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_date' => 'datetime',
        'auto_renewal' => 'boolean',
        'total_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs du modèle.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'draft',
        'auto_renewal' => false,
        'total_value' => 0.00,
        'currency' => 'EUR',
    ];

    /**
     * Les attributs qui doivent être cachés pour la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Les relations qui doivent être chargées par défaut.
     *
     * @var array
     */
    protected $with = [
        'supplier',
        'amendments',
    ];

    /**
     * Les accesseurs ajoutés au modèle.
     *
     * @var array
     */
    protected $appends = [
        'is_active',
        'days_until_expiration',
        'formatted_total_value',
    ];

    /**
     * Obtenir le fournisseur associé au contrat.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Obtenir les avenants du contrat.
     */
    public function amendments(): HasMany
    {
        return $this->hasMany(ContractAmendment::class, 'parent_contract_id');
    }

    /**
     * Obtenir les commandes d'achat liées à ce contrat.
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'contract_id');
    }

    /**
     * Obtenir les documents associés au contrat.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ContractDocument::class);
    }

    /**
     * Obtenir les échéances de paiement du contrat.
     */
    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(ContractPaymentSchedule::class);
    }

    /**
     * Obtenir l'utilisateur qui a créé le contrat.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtenir l'utilisateur qui a mis à jour le contrat pour la dernière fois.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtenir la personne qui a signé le contrat.
     */
    public function signatory()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    /**
     * Vérifier si le contrat est actif.
     *
     * @return bool
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }

    /**
     * Obtenir le nombre de jours restants avant l'expiration du contrat.
     *
     * @return int|null
     */
    public function getDaysUntilExpirationAttribute(): ?int
    {
        if ($this->end_date < now()) {
            return null;
        }
        
        return now()->diffInDays($this->end_date, false);
    }

    /**
     * Obtenir la valeur totale formatée du contrat.
     *
     * @return string
     */
    public function getFormattedTotalValueAttribute(): string
    {
        $currencySymbol = $this->getCurrencySymbol($this->currency);
        return $currencySymbol . ' ' . number_format($this->total_value, 2, ',', ' ');
    }

    /**
     * Obtenir le symbole de la devise.
     *
     * @param string $currency
     * @return string
     */
    protected function getCurrencySymbol(string $currency): string
    {
        $symbols = [
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
            'JPY' => '¥',
            'XOF' => 'CFA',
            'XAF' => 'FCFA',
        ];

        return $symbols[strtoupper($currency)] ?? strtoupper($currency);
    }

    /**
     * Activer le contrat.
     *
     * @param User $user
     * @return bool
     */
    public function activate(User $user): bool
    {
        return $this->update([
            'status' => 'active',
            'signed_by' => $user->id,
            'signed_date' => now(),
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Renouveler le contrat.
     *
     * @param \DateTime $newEndDate
     * @param string|null $notes
     * @return bool
     */
    public function renew(\DateTime $newEndDate, ?string $notes = null): bool
    {
        // Créer un avenant pour le renouvellement
        $amendment = $this->amendments()->create([
            'type' => 'renewal',
            'description' => 'Renouvellement du contrat jusqu\'au ' . $newEndDate->format('d/m/Y'),
            'changes' => [
                'end_date' => [
                    'old' => $this->end_date->format('Y-m-d'),
                    'new' => $newEndDate->format('Y-m-d'),
                ],
            ],
            'created_by' => auth()->id(),
        ]);

        // Mettre à jour la date de fin du contrat
        return $this->update([
            'end_date' => $newEndDate,
            'status' => 'active',
            'notes' => $notes ? ($this->notes ? $this->notes . "\n\nRenouvellement: " . $notes : 'Renouvellement: ' . $notes) : $this->notes,
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Résilier le contrat.
     *
     * @param string $reason
     * @param \DateTime|null $terminationDate
     * @return bool
     */
    public function terminate(string $reason, ?\DateTime $terminationDate = null): bool
    {
        $terminationDate = $terminationDate ?? now();

        return $this->update([
            'status' => 'terminated',
            'end_date' => $terminationDate,
            'notes' => $this->notes ? $this->notes . "\n\nRésiliation: " . $reason : 'Résiliation: ' . $reason,
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Vérifier si le contrat peut être renouvelé automatiquement.
     *
     * @return bool
     */
    public function canAutoRenew(): bool
    {
        return $this->auto_renewal && 
               $this->renewal_frequency !== 'none' &&
               $this->status === 'active';
    }

    /**
     * Obtenir la date de prochain renouvellement.
     *
     * @return \DateTime|null
     */
    public function getNextRenewalDate(): ?\DateTime
    {
        if (!$this->canAutoRenew()) {
            return null;
        }

        $renewalDate = clone $this->end_date;
        
        switch ($this->renewal_frequency) {
            case 'monthly':
                $renewalDate->modify('+1 month');
                break;
            case 'quarterly':
                $renewalDate->modify('+3 months');
                break;
            case 'semiannual':
                $renewalDate->modify('+6 months');
                break;
            case 'annual':
                $renewalDate->modify('+1 year');
                break;
            case 'biennial':
                $renewalDate->modify('+2 years');
                break;
            case 'triennial':
                $renewalDate->modify('+3 years');
                break;
            default:
                return null;
        }
        
        return $renewalDate;
    }

    /**
     * Vérifier si le contrat est expiré.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->end_date < now() && $this->status !== 'terminated';
    }

    /**
     * Vérifier si le contrat est sur le point d'expirer.
     *
     * @param int $daysBefore
     * @return bool
     */
    public function isAboutToExpire(int $daysBefore = 30): bool
    {
        $expirationDate = (clone now())->modify("+{$daysBefore} days");
        return $this->end_date <= $expirationDate && 
               $this->end_date >= now() && 
               $this->status === 'active';
    }

    /**
     * Obtenir la valeur consommée du contrat.
     *
     * @return float
     */
    public function getConsumedValue(): float
    {
        return (float) $this->purchaseOrders()
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
    }

    /**
     * Obtenir la valeur restante du contrat.
     *
     * @return float
     */
    public function getRemainingValue(): float
    {
        $consumed = $this->getConsumedValue();
        return max(0, $this->total_value - $consumed);
    }

    /**
     * Obtenir le taux d'utilisation du contrat.
     *
     * @return float
     */
    public function getUtilizationRate(): float
    {
        if ($this->total_value <= 0) {
            return 0.0;
        }
        
        return min(100, round(($this->getConsumedValue() / $this->total_value) * 100, 2));
    }

    /**
     * Scope pour les contrats actifs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    /**
     * Scope pour les contrats expirés.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now())
                    ->where('status', '!=', 'terminated');
    }

    /**
     * Scope pour les contrats à renouveler.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $daysBefore
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpiringSoon($query, int $daysBefore = 30)
    {
        $date = now()->addDays($daysBefore);
        
        return $query->where('status', 'active')
                    ->where('end_date', '<=', $date)
                    ->where('end_date', '>=', now());
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Générer automatiquement un numéro de contrat lors de la création
        static::creating(function ($contract) {
            if (empty($contract->contract_number)) {
                $contract->contract_number = self::generateContractNumber();
            }
            
            if (auth()->check()) {
                $contract->created_by = $contract->created_by ?? auth()->id();
                $contract->updated_by = $contract->updated_by ?? auth()->id();
            }
        });

        // Mettre à jour l'utilisateur qui a modifié
        static::updating(function ($contract) {
            if (auth()->check()) {
                $contract->updated_by = auth()->id();
            }
        });

        // Gérer le statut d'expiration
        static::saving(function ($contract) {
            if ($contract->end_date < now() && $contract->status !== 'terminated') {
                $contract->status = 'expired';
            }
        });
    }

    /**
     * Générer un numéro de contrat unique.
     *
     * @return string
     */
    public static function generateContractNumber(): string
    {
        $prefix = 'CONTRACT-';
        $lastContract = self::orderBy('id', 'desc')->first();
        $number = $lastContract ? (int) str_replace($prefix, '', $lastContract->contract_number) + 1 : 1;
        
        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
