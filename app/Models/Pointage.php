<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Pointage extends Model
{
    protected $fillable = [
        'user_id',
        'personnel_id',
        'vehicle_assignment_id',
        'vehicle_id',
        'operation_id',
        'type', // 'depart', 'arrivee', 'pause', 'reprise'
        'date_pointage',
        'heure_pointage',
        'heure_arrivee',
        'heure_depart',
        'unit_type',
        'quantity',
        'supplier_unit_cost',
        'client_unit_price',
        'total_supplier_cost',
        'total_client_amount',
        'submodule',
        'billing_mode',
        'created_by',
        'latitude',
        'longitude',
        'location_address',
        'kilometrage',
        'niveau_carburant',
        'photo_url',
        'notes',
        'statut', // 'en_attente', 'valide', 'rejete'
        'validated_by',
        'validated_at',
        'delay_minutes',
        'worked_hours',
    ];

    protected $casts = [
        'date_pointage' => 'date',
        'heure_pointage' => 'datetime',
        'quantity' => 'float',
        'supplier_unit_cost' => 'float',
        'client_unit_price' => 'float',
        'total_supplier_cost' => 'float',
        'total_client_amount' => 'float',
        'latitude' => 'float',
        'longitude' => 'float',
        'kilometrage' => 'integer',
        'niveau_carburant' => 'float',
        'validated_at' => 'datetime',
    ];

    /**
     * Relation avec le véhicule
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class);
    }

    /**
     * Relation avec l'utilisateur (chauffeur)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le personnel RH
     */
    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Personnel::class);
    }

    /**
     * Relation avec l'affectation de véhicule
     */
    public function vehicleAssignment(): BelongsTo
    {
        return $this->belongsTo(VehicleAssignment::class);
    }

    /**
     * Relation avec l'opération
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    /**
     * Relation avec le validateur
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Scope pour les pointages du jour
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date_pointage', today());
    }

    /**
     * Scope pour les pointages par type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les pointages validés
     */
    public function scopeValidated($query)
    {
        return $query->where('statut', 'valide');
    }

    /**
     * Calculer la durée de travail entre deux pointages
     */
    public static function calculateWorkDuration($userId, $date)
    {
        $depart = self::where('user_id', $userId)
            ->where('date_pointage', $date)
            ->where('type', 'depart')
            ->where('statut', 'valide')
            ->first();

        $arrivee = self::where('user_id', $userId)
            ->where('date_pointage', $date)
            ->where('type', 'arrivee')
            ->where('statut', 'valide')
            ->first();

        if ($depart && $arrivee) {
            return $depart->heure_pointage->diffInHours($arrivee->heure_pointage);
        }

        return 0;
    }

    /**
     * Obtenir le dernier pointage d'un utilisateur
     */
    public static function getLastPointage($userId)
    {
        return self::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
