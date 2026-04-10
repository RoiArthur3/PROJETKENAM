<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'shipment_type', // international, national
        'transport_mode',
        'origin_country',
        'origin_warehouse',
        'destination_country',
        'destination_warehouse',
        'status',
        'departure_date',
        'arrival_date',
        'estimated_arrival_date',
        'carrier_name',
        'tracking_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'arrival_date' => 'date',
            'estimated_arrival_date' => 'date',
        ];
    }

    public function getShipmentTypeTextAttribute()
    {
        return $this->shipment_type === 'national' ? 'National' : 'International';
    }

    public function parcels()
    {
        return $this->hasMany(Parcel::class);
    }
}
