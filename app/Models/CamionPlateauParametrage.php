<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CamionPlateauParametrage extends Model
{
    protected $table = 'camion_plateau_parametrages';

    protected $fillable = [
        'vehicle_id',
        'client_id',
        'type_facturation',
        'monthly_trip_threshold',
        'monthly_flat_rate',
        'extra_trip_unit_price',
        'trip_client_price',
        'supplier_type_paiement',
        'supplier_monthly_cost',
        'supplier_trip_cost',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'is_active'              => 'boolean',
        'monthly_flat_rate'      => 'float',
        'extra_trip_unit_price'  => 'float',
        'trip_client_price'      => 'float',
        'supplier_monthly_cost'  => 'float',
        'supplier_trip_cost'     => 'float',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicule::class, 'vehicle_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
