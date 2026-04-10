<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:6',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Obtenir le taux de change entre deux devises
     */
    public static function getRate(string $fromCurrency, string $toCurrency): ?float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $rate = self::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('is_active', true)
            ->first();

        return $rate ? $rate->rate : null;
    }

    /**
     * Convertir un montant d'une devise à une autre
     */
    public static function convert(float $amount, string $fromCurrency, string $toCurrency): ?float
    {
        $rate = self::getRate($fromCurrency, $toCurrency);

        if ($rate === null) {
            return null;
        }

        return $amount * $rate;
    }

    /**
     * Obtenir toutes les devises disponibles
     */
    public static function getAvailableCurrencies(): array
    {
        $currencies = self::select('from_currency')
            ->where('is_active', true)
            ->distinct()
            ->pluck('from_currency')
            ->toArray();

        $toCurrencies = self::select('to_currency')
            ->where('is_active', true)
            ->distinct()
            ->pluck('to_currency')
            ->toArray();

        return array_unique(array_merge($currencies, $toCurrencies));
    }

    /**
     * Mettre à jour les taux de change
     */
    public static function updateRates(array $rates): void
    {
        foreach ($rates as $from => $toRates) {
            foreach ($toRates as $to => $rate) {
                self::updateOrCreate(
                    ['from_currency' => $from, 'to_currency' => $to],
                    ['rate' => $rate, 'is_active' => true]
                );
            }
        }
    }
}
