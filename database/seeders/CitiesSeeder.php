<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Country;

class CitiesSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les pays existants
        $ivoryCoast = Country::where('code', 'CI')->first();
        $china = Country::where('code', 'CN')->first();
        $france = Country::where('code', 'FR')->first();
        $usa = Country::where('code', 'US')->first();

        // Villes de Côte d'Ivoire
        if ($ivoryCoast) {
            $ivoryCoastCities = [
                ['name' => 'Abidjan', 'code' => 'ABJ', 'latitude' => 5.3595, 'longitude' => -4.0083],
                ['name' => 'Yamoussoukro', 'code' => 'YKR', 'latitude' => 6.8276, 'longitude' => -5.2893],
                ['name' => 'Bouaké', 'code' => 'BKE', 'latitude' => 7.6889, 'longitude' => -4.7770],
                ['name' => 'Daloa', 'code' => 'DLO', 'latitude' => 6.8725, 'longitude' => -6.4497],
                ['name' => 'San Pedro', 'code' => 'SPY', 'latitude' => 4.7492, 'longitude' => -6.6375],
                ['name' => 'Gagnoa', 'code' => 'GGA', 'latitude' => 6.1319, 'longitude' => -5.9347],
                ['name' => 'Man', 'code' => 'MAN', 'latitude' => 7.3917, 'longitude' => -7.5450],
                ['name' => 'Korhogo', 'code' => 'KHO', 'latitude' => 9.4589, 'longitude' => -5.6361],
                ['name' => 'Divo', 'code' => 'DIV', 'latitude' => 5.8436, 'longitude' => -4.4383],
                ['name' => 'Abengourou', 'code' => 'ABO', 'latitude' => 6.7292, 'longitude' => -3.4833],
            ];

            foreach ($ivoryCoastCities as $city) {
                City::create(array_merge($city, ['country_id' => $ivoryCoast->id]));
            }
        }

        // Villes de Chine
        if ($china) {
            $chinaCities = [
                ['name' => 'Guangzhou', 'code' => 'CAN', 'latitude' => 23.1291, 'longitude' => 113.2644],
                ['name' => 'Shanghai', 'code' => 'SHA', 'latitude' => 31.2304, 'longitude' => 121.4737],
                ['name' => 'Shenzhen', 'code' => 'SZX', 'latitude' => 22.5431, 'longitude' => 114.0579],
                ['name' => 'Beijing', 'code' => 'PEK', 'latitude' => 39.9042, 'longitude' => 116.4074],
                ['name' => 'Yiwu', 'code' => 'YIW', 'latitude' => 29.3064, 'longitude' => 120.0394],
                ['name' => 'Hangzhou', 'code' => 'HGH', 'latitude' => 30.2741, 'longitude' => 120.1551],
                ['name' => 'Ningbo', 'code' => 'NGB', 'latitude' => 29.8683, 'longitude' => 121.5440],
                ['name' => 'Xiamen', 'code' => 'XMN', 'latitude' => 24.4798, 'longitude' => 118.0894],
                ['name' => 'Qingdao', 'code' => 'TAO', 'latitude' => 36.0671, 'longitude' => 120.3826],
                ['name' => 'Tianjin', 'code' => 'TSN', 'latitude' => 39.3434, 'longitude' => 117.3616],
            ];

            foreach ($chinaCities as $city) {
                City::create(array_merge($city, ['country_id' => $china->id]));
            }
        }

        // Villes de France
        if ($france) {
            $franceCities = [
                ['name' => 'Paris', 'code' => 'PAR', 'latitude' => 48.8566, 'longitude' => 2.3522],
                ['name' => 'Lyon', 'code' => 'LYS', 'latitude' => 45.7640, 'longitude' => 4.8357],
                ['name' => 'Marseille', 'code' => 'MRS', 'latitude' => 43.2965, 'longitude' => 5.3698],
                ['name' => 'Toulouse', 'code' => 'TLS', 'latitude' => 43.6047, 'longitude' => 1.4442],
                ['name' => 'Nice', 'code' => 'NCE', 'latitude' => 43.7102, 'longitude' => 7.2620],
                ['name' => 'Bordeaux', 'code' => 'BOD', 'latitude' => 44.8378, 'longitude' => -0.5792],
                ['name' => 'Lille', 'code' => 'LIL', 'latitude' => 50.6292, 'longitude' => 3.0573],
            ];

            foreach ($franceCities as $city) {
                City::create(array_merge($city, ['country_id' => $france->id]));
            }
        }

        // Villes des USA
        if ($usa) {
            $usaCities = [
                ['name' => 'New York', 'code' => 'NYC', 'latitude' => 40.7128, 'longitude' => -74.0060],
                ['name' => 'Los Angeles', 'code' => 'LAX', 'latitude' => 34.0522, 'longitude' => -118.2437],
                ['name' => 'Chicago', 'code' => 'CHI', 'latitude' => 41.8781, 'longitude' => -87.6298],
                ['name' => 'Houston', 'code' => 'HOU', 'latitude' => 29.7604, 'longitude' => -95.3698],
                ['name' => 'Miami', 'code' => 'MIA', 'latitude' => 25.7617, 'longitude' => -80.1918],
            ];

            foreach ($usaCities as $city) {
                City::create(array_merge($city, ['country_id' => $usa->id]));
            }
        }
    }
}
