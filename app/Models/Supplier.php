<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Supplier extends Model
{
    use SoftDeletes;

    /**
     * Les catégories de fournisseurs
     *
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'auto_parts' => 'Pièces auto',
        'fuel' => 'Carburant',
        'maintenance' => 'Maintenance',
        'logistics' => 'Logistique',
        'equipment' => 'Équipement',
        'services' => 'Services',
        'other' => 'Autre',
    ];

    /**
     * Les statuts de fournisseurs
     *
     * @var array<string, string>
     */
    public const STATUSES = [
        'active' => 'Actif',
        'suspended' => 'Suspendu',
        'pending_approval' => 'En attente de validation',
        'blacklisted' => 'Liste noire',
    ];

    /**
     * Les badges de performance
     *
     * @var array<string, array>
     */
    public const PERFORMANCE_BADGES = [
        'gold' => ['name' => '🥇 Gold', 'min_rating' => 4.5],
        'silver' => ['name' => '🥈 Silver', 'min_rating' => 4.0],
        'bronze' => ['name' => '🥉 Bronze', 'min_rating' => 3.5],
        'watch' => ['name' => '⚠️ À surveiller', 'min_rating' => 3.0],
        'blacklist' => ['name' => '❌ À exclure', 'min_rating' => 0],
    ];

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'company_name',
        'legal_name',
        'tax_number',
        'registration_number',
        'vat_number',
        'email',
        'phone',
        'mobile',
        'website',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'category',
        'industry',
        'contact_person',
        'contact_position',
        'contact_email',
        'contact_phone',
        'payment_terms',
        'bank_name',
        'bank_account_number',
        'bank_swift_code',
        'bank_iban',
        'currency',
        'rating',
        'status',
        'notes',
        'logo_path',
        'is_approved',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'decimal:2',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs du modèle.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pending_approval',
        'is_approved' => false,
        'rating' => 0.0,
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
        'documents',
        'contacts',
    ];

    /**
     * Les accesseurs ajoutés au modèle.
     *
     * @var array
     */
    protected $appends = [
        'performance_badge',
        'is_active',
        'full_address',
    ];

    /**
     * Obtenir les contrats du fournisseur.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(SupplierContract::class);
    }

    /**
     * Obtenir les commandes du fournisseur.
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Obtenir les documents du fournisseur.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(SupplierDocument::class);
    }

    /**
     * Obtenir les contacts du fournisseur.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(SupplierContact::class);
    }

    /**
     * Obtenir les évaluations de performance du fournisseur.
     */
    public function performanceEvaluations(): HasMany
    {
        return $this->hasMany(SupplierEvaluation::class);
    }

    /**
     * Obtenir les produits fournis par ce fournisseur.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Obtenir l'utilisateur qui a approuvé ce fournisseur.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Obtenir l'utilisateur qui a créé ce fournisseur.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtenir l'utilisateur qui a mis à jour ce fournisseur pour la dernière fois.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtenir le contrat actif du fournisseur.
     */
    public function activeContract()
    {
        return $this->hasOne(SupplierContract::class)->where('end_date', '>=', now())->latest();
    }

    /**
     * Obtenir les commandes en attente du fournisseur.
     */
    public function pendingOrders()
    {
        return $this->purchaseOrders()->where('status', 'pending');
    }

    /**
     * Obtenir les commandes livrées du fournisseur.
     */
    public function deliveredOrders()
    {
        return $this->purchaseOrders()->where('status', 'delivered');
    }

    /**
     * Obtenir les commandes annulées du fournisseur.
     */
    public function cancelledOrders()
    {
        return $this->purchaseOrders()->where('status', 'cancelled');
    }

    /**
     * Vérifier si le fournisseur est actif.
     *
     * @return bool
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && $this->is_approved;
    }

    /**
     * Obtenir le badge de performance du fournisseur.
     *
     * @return string
     */
    public function getPerformanceBadgeAttribute(): string
    {
        $rating = (float) $this->rating;

        foreach (self::PERFORMANCE_BADGES as $badge) {
            if ($rating >= $badge['min_rating']) {
                return $badge['name'];
            }
        }

        return self::PERFORMANCE_BADGES['blacklist']['name'];
    }

    /**
     * Obtenir l'adresse complète du fournisseur.
     *
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        $address = [
            $this->address_line1,
            $this->address_line2,
            $this->postal_code,
            $this->city,
            $this->state,
            $this->country,
        ];

        return implode(', ', array_filter($address));
    }

    /**
     * Obtenir l'URL du logo du fournisseur.
     *
     * @return string|null
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::url($this->logo_path) : null;
    }

    /**
     * Approuver le fournisseur.
     *
     * @param User $user
     * @param string|null $notes
     * @return bool
     */
    public function approve(User $user, ?string $notes = null): bool
    {
        return $this->update([
            'is_approved' => true,
            'status' => 'active',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'notes' => $notes ? ($this->notes ? $this->notes . "\n\nApprobation: " . $notes : 'Approbation: ' . $notes) : $this->notes,
        ]);
    }

    /**
     * Suspendre le fournisseur.
     *
     * @param string $reason
     * @return bool
     */
    public function suspend(string $reason): bool
    {
        return $this->update([
            'status' => 'suspended',
            'notes' => $this->notes ? $this->notes . "\n\nSuspension: " . $reason : 'Suspension: ' . $reason,
        ]);
    }

    /**
     * Réactiver le fournisseur.
     *
     * @param string|null $notes
     * @return bool
     */
    public function reactivate(?string $notes = null): bool
    {
        return $this->update([
            'status' => 'active',
            'notes' => $notes ? ($this->notes ? $this->notes . "\n\nRéactivation: " . $notes : 'Réactivation: ' . $notes) : $this->notes,
        ]);
    }

    /**
     * Mettre à jour la note du fournisseur en fonction des évaluations.
     *
     * @return float
     */
    public function updateRating(): float
    {
        $rating = $this->performanceEvaluations()->avg('score');
        $this->update(['rating' => $rating]);
        return $rating;
    }

    /**
     * Vérifier si le fournisseur a un contrat actif.
     *
     * @return bool
     */
    public function hasActiveContract(): bool
    {
        return $this->contracts()->where('end_date', '>=', now())->exists();
    }

    /**
     * Obtenir le nombre de jours restants avant l'expiration du contrat actif.
     *
     * @return int|null
     */
    public function getDaysUntilContractExpiration(): ?int
    {
        $contract = $this->activeContract;
        return $contract ? now()->diffInDays($contract->end_date, false) : null;
    }

    /**
     * Obtenir le montant total des achats pour une période donnée.
     *
     * @param string $startDate
     * @param string|null $endDate
     * @return float
     */
    public function getTotalPurchases(string $startDate, ?string $endDate = null): float
    {
        $query = $this->purchaseOrders()
            ->where('status', 'delivered')
            ->where('order_date', '>=', $startDate);

        if ($endDate) {
            $query->where('order_date', '<=', $endDate);
        }

        return (float) $query->sum('total_amount');
    }

    /**
     * Obtenir le taux de conformité des livraisons.
     *
     * @return float
     */
    public function getDeliveryComplianceRate(): float
    {
        $totalOrders = $this->purchaseOrders()->count();
        
        if ($totalOrders === 0) {
            return 100.0;
        }
        
        $onTimeOrders = $this->purchaseOrders()
            ->where('status', 'delivered')
            ->whereRaw('delivery_date <= expected_delivery_date')
            ->count();
            
        return round(($onTimeOrders / $totalOrders) * 100, 2);
    }

    /**
     * Obtenir le taux de qualité des produits.
     *
     * @return float
     */
    public function getQualityRate(): float
    {
        $evaluations = $this->performanceEvaluations()
            ->whereNotNull('quality_score')
            ->avg('quality_score');
            
        return (float) $evaluations ? round($evaluations * 20, 2) : 100.0; // Convertir de 1-5 à 0-100%
    }

    /**
     * Scope pour les fournisseurs actifs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_approved', true);
    }

    /**
     * Scope pour les fournisseurs suspendus.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /**
     * Scope pour les fournisseurs en attente d'approbation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    /**
     * Scope pour les fournisseurs de la liste noire.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlacklisted($query)
    {
        return $query->where('status', 'blacklisted');
    }

    /**
     * Scope pour les fournisseurs par catégorie.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope pour les fournisseurs avec des contrats expirant bientôt.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithContractsExpiringSoon($query, int $days = 30)
    {
        return $query->whereHas('contracts', function ($q) use ($days) {
            $q->whereBetween('end_date', [now(), now()->addDays($days)]);
        });
    }

    /**
     * Scope pour les fournisseurs avec des contrats expirés.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithExpiredContracts($query)
    {
        return $query->whereHas('contracts', function ($q) {
            $q->where('end_date', '<', now());
        });
    }

    /**
     * Générer une référence unique pour le fournisseur.
     *
     * @return string
     */
    public static function generateReference(): string
    {
        $prefix = 'SUP-';
        $lastSupplier = self::orderBy('id', 'desc')->first();
        $number = $lastSupplier ? (int) str_replace($prefix, '', $lastSupplier->reference) + 1 : 1;
        
        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Générer automatiquement une référence lors de la création
        static::creating(function ($supplier) {
            if (empty($supplier->reference)) {
                $supplier->reference = self::generateReference();
            }
            
            if (auth()->check()) {
                $supplier->created_by = auth()->id();
                $supplier->updated_by = auth()->id();
            }
        });

        // Mettre à jour l'utilisateur qui a modifié
        static::updating(function ($supplier) {
            if (auth()->check()) {
                $supplier->updated_by = auth()->id();
            }
        });
    }
}
