<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parcel extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'user_id',
        'shipment_id',
        'shipment_type', // international, national
        'transport_mode',
        'content_type',
        'status',
        'origin_country',
        'origin_city',
        'destination_country',
        'destination_city',
        'delivery_option',
        'content_description',
        'declared_value',
        'estimated_weight',
        'actual_weight',
        'length',
        'width',
        'height',
        'volumetric_weight',
        'chargeable_weight',
        'invoice_file',
        'received_at_warehouse_date',
        'inspection_date',
        'rejection_reason',
        'notes',
        'sender_name',
        'sender_phone',
        'recipient_name',
        'recipient_phone',
    ];

    protected function casts(): array
    {
        return [
            'declared_value' => 'decimal:2',
            'estimated_weight' => 'decimal:2',
            'actual_weight' => 'decimal:2',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'volumetric_weight' => 'decimal:2',
            'chargeable_weight' => 'decimal:2',
            'received_at_warehouse_date' => 'date',
            'inspection_date' => 'date',
        ];
    }

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

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function photos()
    {
        return $this->hasMany(ParcelPhoto::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(ParcelStatusHistory::class);
    }

    public function calculateVolumetricWeight(int $divisor = 5000): float
    {
        if ($this->length && $this->width && $this->height) {
            return ($this->length * $this->width * $this->height) / $divisor;
        }
        return 0;
    }

    public function calculateChargeableWeight(int $divisor = 5000): float
    {
        $volumetric = $this->calculateVolumetricWeight($divisor);
        return max($this->actual_weight ?? 0, $volumetric);
    }

    public function getShipmentTypeTextAttribute()
    {
        return $this->shipment_type === 'national' ? 'National' : 'International';
    }

    public function getTransportModeTextAttribute()
    {
        return match($this->transport_mode) {
            'air_normal' => 'Avion Normal',
            'air_express' => 'Avion Express',
            'sea' => 'Maritime',
            'road_national' => 'Routier National',
            'express_national' => 'Express National',
            'standard_national' => 'Standard National',
            default => $this->transport_mode,
        };
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
