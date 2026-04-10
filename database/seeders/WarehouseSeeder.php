<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Entrepôt Paris - Roissy',
                'location' => 'Paris, France',
                'capacity' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'Entrepôt New York - JFK',
                'location' => 'New York, USA',
                'capacity' => 7500,
                'is_active' => true,
            ],
            [
                'name' => 'Entrepôt Los Angeles - LAX',
                'location' => 'Los Angeles, USA',
                'capacity' => 6000,
                'is_active' => true,
            ],
            [
                'name' => 'Entrepôt Londres - Heathrow',
                'location' => 'London, UK',
                'capacity' => 4500,
                'is_active' => true,
            ],
            [
                'name' => 'Entrepôt Dubaï - DXB',
                'location' => 'Dubai, UAE',
                'capacity' => 8000,
                'is_active' => true,
            ],
            [
                'name' => 'Entrepôt Hong Kong - HKG',
                'location' => 'Hong Kong',
                'capacity' => 7000,
                'is_active' => true,
            ],
            [
                'name' => 'Entrepôt Singapour - SIN',
                'location' => 'Singapore',
                'capacity' => 5500,
                'is_active' => true,
            ],
            [
                'name' => 'Entrepôt Tokyo - NRT',
                'location' => 'Tokyo, Japan',
                'capacity' => 4000,
                'is_active' => true,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }
    }
}
