<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'transport_mode',
        'price_per_kg',
        'minimum_price',
        'insurance_rate',
        'customs_rate',
        'handling_fee',
        'packaging_fee_per_carton',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'price_per_kg' => 'decimal:2',
        'minimum_price' => 'decimal:2',
        'insurance_rate' => 'decimal:2',
        'customs_rate' => 'decimal:2',
        'handling_fee' => 'decimal:2',
        'packaging_fee_per_carton' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function getTransportModeLabelAttribute()
    {
        return match($this->transport_mode) {
            'air_normal' => 'Avion Normal',
            'air_express' => 'Avion Express',
            'sea' => 'Bateau (Cargo)',
            default => $this->transport_mode
        };
    }

    public function calculatePrice($weight, $cartonsCount = 1, $declaredValue = null)
    {
        // Prix de base par kg avec minimum
        $basePrice = max($this->price_per_kg * $weight, $this->minimum_price);

        // Frais d'emballage par carton
        $packagingFee = $this->packaging_fee_per_carton * $cartonsCount;

        // Total de base
        $total = $basePrice + $this->handling_fee + $packagingFee;

        // Assurance (pourcentage de la valeur déclarée)
        if ($declaredValue && $this->insurance_rate > 0) {
            $total += ($declaredValue * $this->insurance_rate) / 100;
        }

        // Douane (pourcentage de la valeur déclarée)
        if ($declaredValue && $this->customs_rate > 0) {
            $total += ($declaredValue * $this->customs_rate) / 100;
        }

        return $total;
    }
}
