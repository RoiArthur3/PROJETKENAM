<?php

namespace App\Services;

use App\Models\ExchangeRate;

class CurrencyService
{
    /**
     * Convertir un montant avec le formatage approprié
     */
    public static function convertAndFormat(float $amount, string $fromCurrency, string $toCurrency): array
    {
        $convertedAmount = ExchangeRate::convert($amount, $fromCurrency, $toCurrency);

        if ($convertedAmount === null) {
            return [
                'amount' => $amount,
                'currency' => $fromCurrency,
                'formatted' => self::formatCurrency($amount, $fromCurrency),
                'converted' => null,
                'rate' => null,
                'error' => 'Taux de change non disponible'
            ];
        }

        $rate = ExchangeRate::getRate($fromCurrency, $toCurrency);

        return [
            'amount' => $amount,
            'currency' => $fromCurrency,
            'formatted' => self::formatCurrency($amount, $fromCurrency),
            'converted' => $convertedAmount,
            'converted_currency' => $toCurrency,
            'converted_formatted' => self::formatCurrency($convertedAmount, $toCurrency),
            'rate' => $rate,
            'error' => null
        ];
    }

    /**
     * Formater un montant selon la devise
     */
    public static function formatCurrency(float $amount, string $currency): string
    {
        $formats = [
            'EUR' => ['symbol' => '€', 'position' => 'after', 'decimals' => 2],
            'USD' => ['symbol' => '$', 'position' => 'before', 'decimals' => 2],
            'GBP' => ['symbol' => '£', 'position' => 'before', 'decimals' => 2],
            'XOF' => ['symbol' => 'F CFA', 'position' => 'after', 'decimals' => 0],
            'XAF' => ['symbol' => 'F CFA', 'position' => 'after', 'decimals' => 0],
            'CAD' => ['symbol' => 'C$', 'position' => 'before', 'decimals' => 2],
            'CHF' => ['symbol' => 'CHF', 'position' => 'before', 'decimals' => 2],
            'JPY' => ['symbol' => '¥', 'position' => 'before', 'decimals' => 0],
            'CNY' => ['symbol' => '¥', 'position' => 'before', 'decimals' => 2],
            'MAD' => ['symbol' => 'MAD', 'position' => 'before', 'decimals' => 2],
            'TND' => ['symbol' => 'TND', 'position' => 'before', 'decimals' => 3],
            'DZD' => ['symbol' => 'DA', 'position' => 'before', 'decimals' => 2],
            'NGN' => ['symbol' => '₦', 'position' => 'before', 'decimals' => 2],
            'GHS' => ['symbol' => 'GH₵', 'position' => 'before', 'decimals' => 2],
        ];

        $format = $formats[$currency] ?? ['symbol' => $currency, 'position' => 'before', 'decimals' => 2];

        $formattedAmount = number_format($amount, $format['decimals'], '.', ' ');

        if ($format['position'] === 'before') {
            return $format['symbol'] . ' ' . $formattedAmount;
        } else {
            return $formattedAmount . ' ' . $format['symbol'];
        }
    }

    /**
     * Obtenir les informations sur une devise
     */
    public static function getCurrencyInfo(string $currency): array
    {
        $currencies = [
            'EUR' => ['name' => 'Euro', 'symbol' => '€', 'country' => 'Zone Euro'],
            'USD' => ['name' => 'Dollar Américain', 'symbol' => '$', 'country' => 'États-Unis'],
            'GBP' => ['name' => 'Livre Sterling', 'symbol' => '£', 'country' => 'Royaume-Uni'],
            'XOF' => ['name' => 'Franc CFA BCEAO', 'symbol' => 'F CFA', 'country' => 'Afrique de l\'Ouest'],
            'XAF' => ['name' => 'Franc CFA BEAC', 'symbol' => 'F CFA', 'country' => 'Afrique Centrale'],
            'CAD' => ['name' => 'Dollar Canadien', 'symbol' => 'C$', 'country' => 'Canada'],
            'CHF' => ['name' => 'Franc Suisse', 'symbol' => 'CHF', 'country' => 'Suisse'],
            'JPY' => ['name' => 'Yen Japonais', 'symbol' => '¥', 'country' => 'Japon'],
            'CNY' => ['name' => 'Yuan Chinois', 'symbol' => '¥', 'country' => 'Chine'],
            'MAD' => ['name' => 'Dirham Marocain', 'symbol' => 'MAD', 'country' => 'Maroc'],
            'TND' => ['name' => 'Dinar Tunisien', 'symbol' => 'TND', 'country' => 'Tunisie'],
            'DZD' => ['name' => 'Dinar Algérien', 'symbol' => 'DA', 'country' => 'Algérie'],
            'NGN' => ['name' => 'Naira Nigérian', 'symbol' => '₦', 'country' => 'Nigeria'],
            'GHS' => ['name' => 'Cedi Ghanéen', 'symbol' => 'GH₵', 'country' => 'Ghana'],
        ];

        return $currencies[$currency] ?? ['name' => $currency, 'symbol' => $currency, 'country' => 'Inconnu'];
    }

    /**
     * Obtenir toutes les devises disponibles avec leurs informations
     */
    public static function getAvailableCurrencies(): array
    {
        $currencyCodes = ExchangeRate::getAvailableCurrencies();
        $currencies = [];

        foreach ($currencyCodes as $code) {
            $currencies[$code] = self::getCurrencyInfo($code);
        }

        return $currencies;
    }

    /**
     * Calculer le montant total dans différentes devises
     */
    public static function calculateTotalInCurrencies(array $amounts, string $baseCurrency = 'EUR'): array
    {
        $total = array_sum($amounts);
        $currencies = self::getAvailableCurrencies();
        $results = [];

        foreach ($currencies as $currency => $info) {
            $conversion = self::convertAndFormat($total, $baseCurrency, $currency);
            $results[$currency] = $conversion;
        }

        return $results;
    }
}
