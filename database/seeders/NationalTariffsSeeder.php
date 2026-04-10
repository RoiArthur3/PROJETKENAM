<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tariff;
use App\Models\Country;

class NationalTariffsSeeder extends Seeder
{
    public function run(): void
    {
        $ivoryCoast = Country::where('code', 'CI')->first();

        if (!$ivoryCoast) return;

        // Tarifs nationaux pour la Côte d'Ivoire
        $nationalTariffs = [
            // Abidjan vers autres villes CI
            [
                'name' => 'Express National 0-1kg',
                'transport_mode' => 'air_express',
                'origin_country' => 'CI',
                'destination_country' => 'CI',
                'min_weight' => 0,
                'max_weight' => 1,
                'price_per_kg' => 500,
                'fixed_fee' => 1500,
            ],
            [
                'name' => 'Express National 1-5kg',
                'transport_mode' => 'air_express',
                'origin_country' => 'CI',
                'destination_country' => 'CI',
                'min_weight' => 1,
                'max_weight' => 5,
                'price_per_kg' => 400,
                'fixed_fee' => 1500,
            ],
            [
                'name' => 'Express National 5-10kg',
                'transport_mode' => 'air_express',
                'origin_country' => 'CI',
                'destination_country' => 'CI',
                'min_weight' => 5,
                'max_weight' => 10,
                'price_per_kg' => 350,
                'fixed_fee' => 2000,
            ],
            [
                'name' => 'Standard National 0-5kg',
                'transport_mode' => 'air_normal',
                'origin_country' => 'CI',
                'destination_country' => 'CI',
                'min_weight' => 0,
                'max_weight' => 5,
                'price_per_kg' => 300,
                'fixed_fee' => 1000,
            ],
            [
                'name' => 'Standard National 5-20kg',
                'transport_mode' => 'air_normal',
                'origin_country' => 'CI',
                'destination_country' => 'CI',
                'min_weight' => 5,
                'max_weight' => 20,
                'price_per_kg' => 250,
                'fixed_fee' => 1500,
            ],
            [
                'name' => 'Routier National 20-100kg',
                'transport_mode' => 'sea',
                'origin_country' => 'CI',
                'destination_country' => 'CI',
                'min_weight' => 20,
                'max_weight' => 100,
                'price_per_kg' => 200,
                'fixed_fee' => 3000,
            ],
        ];

        foreach ($nationalTariffs as $tariff) {
            Tariff::create($tariff);
        }
    }
}
