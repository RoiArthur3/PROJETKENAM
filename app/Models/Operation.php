<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operation extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'montant',
        'cout_estimatif',
        'montant_facturer',
        'priorite',
        'echeance',
        'client_id',
        'operational_service_id',
        'type_operation_id',
        'type', // Ajout de la colonne type
        'statut_courant',
        'user_id',
        'demandeur_name',
        'demandeur_email',
        'date_operation',
        'numero_operation',
    ];

    protected $casts = [
        'echeance'           => 'date',
        'paid_at'            => 'datetime',
        'bon_pour_accord_at' => 'datetime',
        'is_paid'            => 'boolean',
        'montant'            => 'float', // Correction : stocker et manipuler le montant comme un nombre flottant (FCFA)
        'cout_estimatif'     => 'float',
        'montant_facturer'   => 'float',
    ];

    // Seuil DG (FCFA) — modifiable via config('app.seuil_dg')
    const SEUIL_DG = 250000;

    public function services()
    {
        return $this->hasMany(OperationService::class);
    }

    /**
     * Obtenir les véhicules associés à cette opération
     */
    public function vehicules()
    {
        return $this->belongsToMany(Vehicule::class, 'operation_vehicule')
            ->withPivot(['date_affectation', 'date_fin_affectation', 'actif', 'notes'])
            ->withTimestamps()
            ->wherePivot('actif', true);
    }

    /**
     * Obtenir les affectations de véhicules
     */
    public function operationVehicules()
    {
        return $this->hasMany(OperationVehicule::class);
    }

    // Méthode pour obtenir les noms des services
    public function getServiceNamesAttribute()
    {
        return $this->services->pluck('service_name')->toArray();
    }

    // Méthode pour obtenir les services formatés pour l'affichage
    public function getFormattedServicesAttribute()
    {
        return $this->services->map(function($service) {
            return [
                'name' => $service->service_name,
                'email' => $service->service_email,
                'destinataire' => $service->destinataire_nom,
                'statut' => $service->statut
            ];
        });
    }

    public function statusLogs()
    {
        return $this->hasMany(OperationStatusLog::class);
    }

    /**
     * Relation avec le client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec l'initiateur (user)
     */
    public function initiateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec la facture
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Relations avec les affectations de véhicules
     */
    public function vehicleAssignments(): HasMany
    {
        return $this->hasMany(VehicleAssignment::class);
    }

    /**
     * Relations avec le personnel affecté
     */
    public function operationStaff(): HasMany
    {
        return $this->hasMany(OperationStaff::class);
    }

    /**
     * Relations avec les mouvements de stock
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function operationalService(): BelongsTo
    {
        return $this->belongsTo(ServiceOperationnel::class, 'operational_service_id');
    }

    public function serviceOperationnel(): BelongsTo
    {
        return $this->belongsTo(ServiceOperationnel::class, 'operational_service_id');
    }

    /**
     * Relation avec les fichiers/documents attachés
     */
    public function fichiers(): HasMany
    {
        return $this->hasMany(\App\Models\OperationFile::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(\App\Models\OperationFile::class);
    }

    /**
     * Relation avec le type d'opération
     */
    public function typeOperation(): BelongsTo
    {
        return $this->belongsTo(TypeOperation::class, 'type_operation_id');
    }

    /**
     * Relation avec les validations
     */
    public function riskValidations(): HasMany
    {
        return $this->hasMany(Validation::class, 'record_id')
            ->where('module_source', 'operations');
    }

    /**
     * Relation avec les validations de service (Workflow)
     */
    public function validations(): HasMany
    {
        return $this->hasMany(OperationServiceValidation::class, 'operation_id')
            ->orderBy('ordre_validation');
    }

    /**
     * Relations spécifiques au module Requêtes
     */
    public function serviceEmetteur(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_emetteur_id');
    }

    public function serviceDestinataire(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_destinataire_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function destinataire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    /**
     * Utilisateur qui a marqué l'opération comme payée.
     */
    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Service caisse désigné par la comptabilité pour exécuter le paiement.
     */
    public function caisseExecutante(): BelongsTo
    {
        return $this->belongsTo(ServiceOperationnel::class, 'caisse_executante_id');
    }

    /**
     * Utilisateur (comptable/trésorier) qui a émis le Bon Pour Accord.
     */
    public function bonPourAccordBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bon_pour_accord_by');
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(OperationHistorique::class, 'operation_id');
    }

    /**
     * Indique si l'opération nécessite la validation DG (montant > seuil).
     */
    public function getNecessiteDgAttribute(): bool
    {
        $entreprise = \App\Models\EntrepriseSettings::getActive();
        $seuil = $entreprise?->seuil_validation_dg ?? self::SEUIL_DG;
        return ($this->montant ?? 0) > $seuil;
    }

    /**
     * Label lisible du statut courant.
     */
    public function getStatutLabelAttribute(): string
    {
        return match($this->statut_courant) {
            'brouillon'                    => '📝 Brouillon',
            'pending_validation'           => '⏳ En attente de validation',
            'en_validation'               => '🔄 En cours de validation',
            'en_validation_responsable'    => '👤 Validation Responsable',
            'en_validation_dg'            => '🏛️ Validation DG',
            'bon_pour_accord'             => '✅ Bon Pour Accord (Compta)',
            'pret_execution'              => '💳 Bon Pour Exécution (Caisse)',
            'Approuvé_en_attente_paiement' => '✅ Approuvé – En attente paiement',
            'payee'                       => '💰 Payée',
            'rejetee'                     => '❌ Rejetée',
            default                       => ucfirst($this->statut_courant ?? 'Inconnu'),
        };
    }

    /**
     * Accessor pour numéro de projet formaté (PRJ-YYYY-00001)
     */
    public function getNumeroProjetAttribute(): string
    {
        $year = optional($this->created_at)->format('Y') ?: now()->format('Y');
        $seq = str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
        return "PRJ-{$year}-{$seq}";
    }
}
