<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class VehiclePointage extends Model
{
    protected $table = 'vehicle_pointages';

    protected $fillable = [
        'user_id',
        'date_pointage',
        'heure_pointage',
        'heure_arrivee',
        'heure_depart',
        'statut',
        'notes',
        'vehicle_mission_id',
        'operation_id',
        'vehicle_id',
        'driver_id',
        'unit_type',
        'quantity',
        'supplier_unit_cost',
        'client_unit_price',
        'total_supplier_cost',
        'total_client_amount',
        'submodule',
        'billing_mode',
        'task_label',
        'trip_count',
        'monthly_trip_threshold',
        'monthly_flat_rate',
        'extra_trip_unit_price',
        'departure_location',
        'arrival_location',
        'distance_km',
        'fuel_amount',
        'delivery_note_number',
        'road_fees',
        'toll_fees',
        'other_fees',
        'created_by',
    ];

    protected $casts = [
        'date_pointage' => 'date',
        'quantity' => 'decimal:2',
        'supplier_unit_cost' => 'decimal:2',
        'client_unit_price' => 'decimal:2',
        'total_supplier_cost' => 'decimal:2',
        'total_client_amount' => 'decimal:2',
        'trip_count' => 'decimal:2',
        'monthly_trip_threshold' => 'decimal:2',
        'monthly_flat_rate' => 'decimal:2',
        'extra_trip_unit_price' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'fuel_amount' => 'decimal:2',
        'road_fees' => 'decimal:2',
        'toll_fees' => 'decimal:2',
        'other_fees' => 'decimal:2',
    ];

    public function getTable()
    {
        if (Schema::hasTable('vehicle_pointages')) {
            return 'vehicle_pointages';
        }

        return Schema::hasTable('pointages') ? 'pointages' : $this->table;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(VehicleMission::class, 'vehicle_mission_id');
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class, 'vehicle_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, 'driver_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getGrossMarginAttribute(): float
    {
        return (float) $this->total_client_amount - (float) $this->total_supplier_cost;
    }

    public function getAdditionalCostsAttribute(): float
    {
        return (float) $this->fuel_amount
            + (float) $this->road_fees
            + (float) $this->toll_fees
            + (float) $this->other_fees;
    }

    public function getSubmoduleLabelAttribute(): string
    {
        return $this->submodule === 'camion_plateau' ? 'Camion Plateau' : 'Engin standard';
    }

    public function getBillingModeLabelAttribute(): string
    {
        return match ($this->billing_mode) {
            'monthly' => 'Facturation au mois',
            'trip' => 'Facturation au voyage',
            default => 'Standard',
        };
    }

    public function getActivityLabelAttribute(): string
    {
        if (!empty($this->task_label)) {
            return $this->task_label;
        }

        if ($this->mission) {
            return $this->mission->reference . ' - ' . ($this->mission->destination ?? 'Mission');
        }

        if ($this->operation) {
            return $this->operation->titre ?? ('Projet #' . $this->operation_id);
        }

        return 'Sans mission / projet';
    }
}
