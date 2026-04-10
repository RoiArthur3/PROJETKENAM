<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'France', 'code' => 'FR', 'code3' => 'FRA'],
            ['name' => 'China', 'code' => 'CN', 'code3' => 'CHN'],
            ['name' => 'United States', 'code' => 'US', 'code3' => 'USA'],
            ['name' => 'Canada', 'code' => 'CA', 'code3' => 'CAN'],
            ['name' => 'Germany', 'code' => 'DE', 'code3' => 'DEU'],
            ['name' => 'United Kingdom', 'code' => 'GB', 'code3' => 'GBR'],
            ['name' => 'Italy', 'code' => 'IT', 'code3' => 'ITA'],
            ['name' => 'Spain', 'code' => 'ES', 'code3' => 'ESP'],
            ['name' => 'Belgium', 'code' => 'BE', 'code3' => 'BEL'],
            ['name' => 'Switzerland', 'code' => 'CH', 'code3' => 'CHE'],
            ['name' => 'Senegal', 'code' => 'SN', 'code3' => 'SEN'],
            ['name' => 'Mali', 'code' => 'ML', 'code3' => 'MLI'],
            ['name' => 'Ivory Coast', 'code' => 'CI', 'code3' => 'CIV'],
            ['name' => 'Burkina Faso', 'code' => 'BF', 'code3' => 'BFA'],
            ['name' => 'Niger', 'code' => 'NE', 'code3' => 'NER'],
            ['name' => 'Togo', 'code' => 'TG', 'code3' => 'TGO'],
            ['name' => 'Benin', 'code' => 'BJ', 'code3' => 'BJA'],
            ['name' => 'Guinea', 'code' => 'GN', 'code3' => 'GIN'],
            ['name' => 'Mauritania', 'code' => 'MR', 'code3' => 'MRT'],
            ['name' => 'Ghana', 'code' => 'GH', 'code3' => 'GHA'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
