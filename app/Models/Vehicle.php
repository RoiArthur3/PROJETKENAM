<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class Vehicle extends Model
{
    /**
     * Compatibilite multi-schema:
     * - anciens environnements: table `vehicules`
     * - nouveaux environnements: table `vehicles`
     */
    public function getTable()
    {
        $defaultTable = parent::getTable(); // vehicles

        if (Schema::hasTable($defaultTable)) {
            return $defaultTable;
        }

        if (Schema::hasTable('vehicules')) {
            return 'vehicules';
        }

        return $defaultTable;
    }

    protected $fillable = [
        'immatriculation',
        'modele',
        'marque',
        'annee',
        'type',
        'etat',
        'disponibilite',
        'kilometrage',
        'date_achat',
        'prix_achat',
        'description',
        'service_assigne',
        'user_id', // Conducteur assigne
    ];

    protected $casts = [
        'annee' => 'integer',
        'disponibilite' => 'boolean',
        'kilometrage' => 'integer',
        'date_achat' => 'date',
        'prix_achat' => 'decimal:2',
    ];

    /**
     * Relation avec l'utilisateur (conducteur assigné)
     */
    public function assignedDriver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relations avec les affectations de véhicules
     */
    public function vehicleAssignments(): HasMany
    {
        return $this->hasMany(VehicleAssignment::class);
    }

    /**
     * Relations avec les missions
     */
    public function missions(): HasMany
    {
        return $this->hasMany(VehicleMission::class);
    }

    /**
     * Relations avec les incidents
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(VehicleIncident::class);
    }

    /**
     * Relations avec les entretiens
     */
    public function entretiens(): HasMany
    {
        return $this->hasMany(Entretien::class);
    }

    /**
     * Relations avec les réparations
     */
    public function reparations(): HasMany
    {
        return $this->hasMany(Reparation::class);
    }

    /**
     * Relations avec les contrôles
     */
    public function vehicleChecks(): HasMany
    {
        return $this->hasMany(VehicleCheck::class);
    }

    /**
     * Obtenir les opérations actives du véhicule
     */
    public function getActiveOperationsAttribute()
    {
        return $this->vehicleAssignments()
            ->where('statut', 'en_cours')
            ->with('operation')
            ->get();
    }

    /**
     * Relations avec les assurances
     */
    public function assurances(): HasMany
    {
        return $this->hasMany(Assurance::class);
    }

    /**
     * Relations avec les dépenses (carburant, maintenance, etc.)
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'vehicule_id');
    }

    /**
     * Relations avec les dépenses de carburant uniquement
     */
    public function fuelExpenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'vehicule_id')
            ->where('service_concerne', 'Parc')
            ->where(function ($q) {
                $q->where('categorie', 'Carburant')
                  ->orWhere('categorie', 'LIKE', '%carbur%');
            });
    }

}
