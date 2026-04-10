<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExchangeRate;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Taux de change au 8 janvier 2026 (exemple)
        $rates = [
            'EUR' => [
                'USD' => 1.0895,
                'GBP' => 0.8567,
                'XOF' => 655.957,  // Franc CFA BCEAO
                'XAF' => 655.957,  // Franc CFA BEAC
                'CAD' => 1.4765,
                'CHF' => 0.9345,
                'JPY' => 158.23,
                'CNY' => 7.8234,
                'MAD' => 10.8765,
                'TND' => 3.4567,
                'DZD' => 147.89,
                'NGN' => 1650.45,
                'GHS' => 14.567,
            ],
            'USD' => [
                'EUR' => 0.9178,
                'GBP' => 0.7856,
                'XOF' => 602.34,
                'XAF' => 602.34,
                'CAD' => 1.3546,
                'CHF' => 0.8567,
                'JPY' => 145.23,
                'CNY' => 7.189,
                'MAD' => 9.987,
                'TND' => 3.167,
                'DZD' => 135.67,
                'NGN' => 1514.78,
                'GHS' => 13.345,
            ],
            'XOF' => [
                'EUR' => 0.001525,
                'USD' => 0.001659,
                'GBP' => 0.001307,
                'CAD' => 0.002249,
                'CHF' => 0.001425,
                'JPY' => 0.2413,
                'CNY' => 0.01193,
                'MAD' => 0.01658,
                'TND' => 0.00527,
                'DZD' => 0.2255,
                'NGN' => 2.516,
                'GHS' => 0.02222,
            ],
        ];

        foreach ($rates as $fromCurrency => $toRates) {
            foreach ($toRates as $toCurrency => $rate) {
                ExchangeRate::updateOrCreate(
                    [
                        'from_currency' => $fromCurrency,
                        'to_currency' => $toCurrency,
                    ],
                    [
                        'rate' => $rate,
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('✅ Taux de change créés avec succès');
        $this->command->info('📊 Devises disponibles: EUR, USD, XOF, XAF, CAD, CHF, JPY, CNY, MAD, TND, DZD, NGN, GHS');
    }
}
