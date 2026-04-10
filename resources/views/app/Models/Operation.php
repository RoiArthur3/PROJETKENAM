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
        'type', // Champ requis ajouté
        'titre',
        'description',
        'montant', // Champ requis ajouté
        'date_operation', // Champ requis ajouté
        'priorite',
        'echeance',
        'type_operation_id',
        'operational_service_id',
        'statut_courant',
        'user_id',
        'demandeur_name',
        'demandeur_email',
        'service',
        'responsable_name',
        'responsable_email',
        'client_id',
        'invoice_id',
        'is_paid',
        'paid_by',
        'paid_at',
        'payment_reference',
    ];

    protected $casts = [
        'echeance' => 'date',
        'paid_at' => 'datetime',
        'is_paid' => 'boolean',
    ];

    public function services()
    {
        return $this->hasMany(OperationService::class);
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

    /**
     * Relation avec les fichiers/documents attachés
     */
    public function fichiers(): HasMany
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

    public function historiques(): HasMany
    {
        return $this->hasMany(OperationHistorique::class, 'operation_id');
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
