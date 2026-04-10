<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tariff;

class TariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tariffs = [
            [
                'name' => 'Chine vers France - Avion Normal (0-5kg)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'China',
                'destination_country' => 'France',
                'min_weight' => 0,
                'max_weight' => 5,
                'price_per_kg' => 8.50,
                'fixed_fee' => 5.00,
                'currency' => 'EUR',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'Chine vers France - Avion Normal (5-20kg)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'China',
                'destination_country' => 'France',
                'min_weight' => 5,
                'max_weight' => 20,
                'price_per_kg' => 7.50,
                'fixed_fee' => 5.00,
                'currency' => 'EUR',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'Chine vers France - Avion Normal (20kg+)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'China',
                'destination_country' => 'France',
                'min_weight' => 20,
                'max_weight' => null,
                'price_per_kg' => 6.50,
                'fixed_fee' => 5.00,
                'currency' => 'EUR',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'Chine vers France - Avion Express (0-5kg)',
                'transport_mode' => 'air_express',
                'origin_country' => 'China',
                'destination_country' => 'France',
                'min_weight' => 0,
                'max_weight' => 5,
                'price_per_kg' => 12.00,
                'fixed_fee' => 10.00,
                'currency' => 'EUR',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'Chine vers France - Avion Express (5-20kg)',
                'transport_mode' => 'air_express',
                'origin_country' => 'China',
                'destination_country' => 'France',
                'min_weight' => 5,
                'max_weight' => 20,
                'price_per_kg' => 10.50,
                'fixed_fee' => 10.00,
                'currency' => 'EUR',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'Chine vers France - Avion Express (20kg+)',
                'transport_mode' => 'air_express',
                'origin_country' => 'China',
                'destination_country' => 'France',
                'min_weight' => 20,
                'max_weight' => null,
                'price_per_kg' => 9.00,
                'fixed_fee' => 10.00,
                'currency' => 'EUR',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'Chine vers France - Bateau (0-50kg)',
                'transport_mode' => 'sea',
                'origin_country' => 'China',
                'destination_country' => 'France',
                'min_weight' => 0,
                'max_weight' => 50,
                'price_per_kg' => 3.50,
                'fixed_fee' => 15.00,
                'currency' => 'EUR',
                'volumetric_divisor' => 6000,
                'is_active' => true,
            ],
            [
                'name' => 'Chine vers France - Bateau (50kg+)',
                'transport_mode' => 'sea',
                'origin_country' => 'China',
                'destination_country' => 'France',
                'min_weight' => 50,
                'max_weight' => null,
                'price_per_kg' => 2.80,
                'fixed_fee' => 15.00,
                'currency' => 'EUR',
                'volumetric_divisor' => 6000,
                'is_active' => true,
            ],
        ];

        foreach ($tariffs as $tariff) {
            Tariff::updateOrCreate(
                [
                    'transport_mode' => $tariff['transport_mode'],
                    'origin_country' => $tariff['origin_country'],
                    'destination_country' => $tariff['destination_country'],
                    'min_weight' => $tariff['min_weight'],
                    'max_weight' => $tariff['max_weight'],
                ],
                $tariff
            );
        }
    }
}
