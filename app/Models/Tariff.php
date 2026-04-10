<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tariff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'transport_mode',
        'origin_country',
        'destination_country',
        'content_type',
        'min_weight',
        'max_weight',
        'min_volume',
        'max_volume',
        'price_per_kg',
        'fixed_fee',
        'pricing_type',
        'currency',
        'volumetric_divisor',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_weight' => 'decimal:2',
            'max_weight' => 'decimal:2',
            'min_volume' => 'decimal:3',
            'max_volume' => 'decimal:3',
            'price_per_kg' => 'decimal:2',
            'fixed_fee' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public static function findApplicableTariff(
        string $transportMode,
        string $originCountry,
        string $destinationCountry,
        float $weight,
        ?string $contentType = null
    ): ?self {
        $query = self::where('transport_mode', $transportMode)
            ->where('origin_country', $originCountry)
            ->where('destination_country', $destinationCountry)
            ->where('is_active', true);

        // Filtrer par type de contenu si spécifié
        if ($contentType) {
            $query->where(function ($q) use ($contentType) {
                $q->where('content_type', $contentType)
                  ->orWhereNull('content_type');
            });
        }

        return $query->where('min_weight', '<=', $weight)
            ->where(function ($query) use ($weight) {
                $query->whereNull('max_weight')
                    ->orWhere('max_weight', '>=', $weight);
            })
            ->orderBy('content_type', 'desc') // Prioriser les tarifs spécifiques
            ->orderBy('min_weight', 'desc')
            ->first();
    }

    public static function findApplicableTariffByVolume(
        string $transportMode,
        string $originCountry,
        string $destinationCountry,
        float $volume,
        ?string $contentType = null
    ): ?self {
        $query = self::where('transport_mode', $transportMode)
            ->where('origin_country', $originCountry)
            ->where('destination_country', $destinationCountry)
            ->where('pricing_type', 'per_cbm')
            ->where('is_active', true);

        // Filtrer par type de contenu si spécifié
        if ($contentType) {
            $query->where(function ($q) use ($contentType) {
                $q->where('content_type', $contentType)
                  ->orWhereNull('content_type');
            });
        }

        return $query->where('min_volume', '<=', $volume)
            ->where(function ($query) use ($volume) {
                $query->whereNull('max_volume')
                    ->orWhere('max_volume', '>=', $volume);
            })
            ->orderBy('content_type', 'desc')
            ->orderBy('min_volume', 'desc')
            ->first();
    }
}
