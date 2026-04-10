<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tariff;

class NewTariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver les anciens tarifs
        Tariff::query()->update(['is_active' => false]);

        $tariffs = [
            // Côte d'Ivoire - France
            [
                'name' => 'CI-FR: Aliments (min 30kg)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'Côte d\'Ivoire',
                'destination_country' => 'France',
                'content_type' => 'aliments',
                'min_weight' => 30,
                'max_weight' => null,
                'price_per_kg' => 1000,
                'fixed_fee' => 0,
                'pricing_type' => 'per_kg',
                'currency' => 'XOF',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'CI-FR: Divers (min 30kg)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'Côte d\'Ivoire',
                'destination_country' => 'France',
                'content_type' => 'divers',
                'min_weight' => 30,
                'max_weight' => null,
                'price_per_kg' => 3000,
                'fixed_fee' => 0,
                'pricing_type' => 'per_kg',
                'currency' => 'XOF',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'CI-FR: Beurre de karité (min 30kg)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'Côte d\'Ivoire',
                'destination_country' => 'France',
                'content_type' => 'beurre_karite',
                'min_weight' => 30,
                'max_weight' => null,
                'price_per_kg' => 4000,
                'fixed_fee' => 0,
                'pricing_type' => 'per_kg',
                'currency' => 'XOF',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'CI-FR: Indigénat et savon noir (min 30kg)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'Côte d\'Ivoire',
                'destination_country' => 'France',
                'content_type' => 'indigenat_savon',
                'min_weight' => 30,
                'max_weight' => null,
                'price_per_kg' => 4000,
                'fixed_fee' => 0,
                'pricing_type' => 'per_kg',
                'currency' => 'XOF',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'CI-FR: Cosmétique (min 30kg)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'Côte d\'Ivoire',
                'destination_country' => 'France',
                'content_type' => 'cosmetique',
                'min_weight' => 30,
                'max_weight' => null,
                'price_per_kg' => 8000,
                'fixed_fee' => 0,
                'pricing_type' => 'per_kg',
                'currency' => 'XOF',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'CI-FR: Poisson - escargot - crabes (min 6kg)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'Côte d\'Ivoire',
                'destination_country' => 'France',
                'content_type' => 'poisson_escargot_crabe',
                'min_weight' => 6,
                'max_weight' => null,
                'price_per_kg' => 7000,
                'fixed_fee' => 0,
                'pricing_type' => 'per_kg',
                'currency' => 'XOF',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],

            // Abidjan - Canada
            [
                'name' => 'CI-CA: Aliments (min 6kg)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'Côte d\'Ivoire',
                'destination_country' => 'Canada',
                'content_type' => 'aliments',
                'min_weight' => 6,
                'max_weight' => null,
                'price_per_kg' => 4800,
                'fixed_fee' => 0,
                'pricing_type' => 'per_kg',
                'currency' => 'XOF',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'CI-CA: Divers (min 6kg)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'Côte d\'Ivoire',
                'destination_country' => 'Canada',
                'content_type' => 'divers',
                'min_weight' => 6,
                'max_weight' => null,
                'price_per_kg' => 3000,
                'fixed_fee' => 0,
                'pricing_type' => 'per_kg',
                'currency' => 'XOF',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'CI-CA: Indigénat et savon noir (min 6kg)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'Côte d\'Ivoire',
                'destination_country' => 'Canada',
                'content_type' => 'indigenat_savon',
                'min_weight' => 6,
                'max_weight' => null,
                'price_per_kg' => 8000,
                'fixed_fee' => 0,
                'pricing_type' => 'per_kg',
                'currency' => 'XOF',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],

            // Abidjan - USA
            [
                'name' => 'CI-US: Tout type colis (min 10kg)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'Côte d\'Ivoire',
                'destination_country' => 'USA',
                'content_type' => null,
                'min_weight' => 10,
                'max_weight' => null,
                'price_per_kg' => 4800,
                'fixed_fee' => 0,
                'pricing_type' => 'per_kg',
                'currency' => 'XOF',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'CI-US: Divers (min 30kg)',
                'transport_mode' => 'air_normal',
                'origin_country' => 'Côte d\'Ivoire',
                'destination_country' => 'USA',
                'content_type' => 'divers',
                'min_weight' => 30,
                'max_weight' => null,
                'price_per_kg' => 15000,
                'fixed_fee' => 0,
                'pricing_type' => 'per_kg',
                'currency' => 'XOF',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'CI-US: Appareils électroniques',
                'transport_mode' => 'air_normal',
                'origin_country' => 'Côte d\'Ivoire',
                'destination_country' => 'USA',
                'content_type' => 'electronique',
                'min_weight' => 0,
                'max_weight' => null,
                'price_per_kg' => 0,
                'fixed_fee' => 40000,
                'pricing_type' => 'per_piece',
                'currency' => 'XOF',
                'volumetric_divisor' => 5000,
                'is_active' => true,
            ],

            // Chine - Abidjan (tarification par volume)
            [
                'name' => 'CN-CI: 0-1 CBM',
                'transport_mode' => 'sea',
                'origin_country' => 'China',
                'destination_country' => 'Côte d\'Ivoire',
                'content_type' => null,
                'min_volume' => 0,
                'max_volume' => 1,
                'price_per_kg' => 300000,
                'fixed_fee' => 0,
                'pricing_type' => 'per_cbm',
                'currency' => 'XOF',
                'volumetric_divisor' => 6000,
                'is_active' => true,
            ],
            [
                'name' => 'CN-CI: 1.1-5 CBM',
                'transport_mode' => 'sea',
                'origin_country' => 'China',
                'destination_country' => 'Côte d\'Ivoire',
                'content_type' => null,
                'min_volume' => 1.1,
                'max_volume' => 5,
                'price_per_kg' => 260000,
                'fixed_fee' => 0,
                'pricing_type' => 'per_cbm',
                'currency' => 'XOF',
                'volumetric_divisor' => 6000,
                'is_active' => true,
            ],
            [
                'name' => 'CN-CI: >5 CBM',
                'transport_mode' => 'sea',
                'origin_country' => 'China',
                'destination_country' => 'Côte d\'Ivoire',
                'content_type' => null,
                'min_volume' => 5.001,
                'max_volume' => null,
                'price_per_kg' => 230000,
                'fixed_fee' => 0,
                'pricing_type' => 'per_cbm',
                'currency' => 'XOF',
                'volumetric_divisor' => 6000,
                'is_active' => true,
            ],
        ];

        foreach ($tariffs as $tariff) {
            Tariff::updateOrCreate(
                [
                    'name' => $tariff['name'],
                ],
                $tariff
            );
        }

        $this->command->info('Nouvelle grille tarifaire importée avec succès!');
    }
}
