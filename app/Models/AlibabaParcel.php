<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AlibabaParcel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tracking_number',
        'shipment_type', // international, national
        'first_name',
        'last_name',
        'transport_mode',
        'origin_country',
        'origin_city',
        'destination_country',
        'destination_city',
        'phone1',
        'phone2',
        'fragile',
        'alibaba_order_number',
        'purchase_site',
        'notes',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function originCity()
    {
        return $this->belongsTo(City::class, 'origin_city_id');
    }

    public function destinationCity()
    {
        return $this->belongsTo(City::class, 'destination_city_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getTransportModeTextAttribute()
    {
        return match($this->transport_mode) {
            'air' => 'Avion',
            'sea' => 'Maritime',
            'road_national' => 'Routier National',
            'express_national' => 'Express National',
            'standard_national' => 'Standard National',
            default => $this->transport_mode,
        };
    }

    public function getShipmentTypeTextAttribute()
    {
        return $this->shipment_type === 'national' ? 'National' : 'International';
    }

    public function getFullOriginAttribute()
    {
        $origin = $this->origin_country;
        if ($this->originCity) {
            $origin = $this->originCity->name . ', ' . $origin;
        }
        return $origin;
    }

    public function getFullDestinationAttribute()
    {
        $destination = $this->destination_country;
        if ($this->destinationCity) {
            $destination = $this->destinationCity->name . ', ' . $destination;
        }
        return $destination;
    }
}
