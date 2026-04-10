<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReceivingAddress;

class ReceivingAddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [
            [
                'name' => 'Entrepôt Principal - Paris',
                'address' => 'Zone Logistique Roissy CDG, Bâtiment Cargo C',
                'city' => 'Roissy-en-France',
                'postal_code' => '95700',
                'country' => 'France',
                'phone' => '+33 1 48 62 12 34',
                'email' => 'paris@groupage.com',
                'contact_person' => 'Service Réception',
                'instructions' => 'Mentionner "Groupage Pro - Client [Votre Nom]" sur l\'emballage. Ouvert du lundi au vendredi de 9h à 18h.',
                'warehouse_code' => 'CDG-GRP',
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Hub International - New York',
                'address' => 'JFK International Airport, Building 262, Cargo Area',
                'city' => 'New York',
                'postal_code' => '11430',
                'country' => 'United States',
                'phone' => '+1 718 751 2345',
                'email' => 'newyork@groupage.com',
                'contact_person' => 'John Smith',
                'instructions' => 'Please include "Groupage Pro - Customer [Your Name]" on package. Operating hours: Mon-Fri 8AM-6PM EST.',
                'warehouse_code' => 'JFK-GRP',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Centre Logistique - Londres',
                'address' => 'Heathrow Airport, Hounslow TW6 2EQ, Cargo Terminal 4',
                'city' => 'London',
                'postal_code' => 'TW6 2EQ',
                'country' => 'United Kingdom',
                'phone' => '+44 20 8759 4321',
                'email' => 'london@groupage.com',
                'contact_person' => 'Sarah Johnson',
                'instructions' => 'Please mark "Groupage Pro - Customer [Your Name]" on package. Open Monday-Friday 8:30-17:30.',
                'warehouse_code' => 'LHR-GRP',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Plateforme Asie - Dubaï',
                'address' => 'Dubai International Airport, Free Zone Logistics Center',
                'city' => 'Dubai',
                'postal_code' => '',
                'country' => 'United Arab Emirates',
                'phone' => '+971 4 216 5432',
                'email' => 'dubai@groupage.com',
                'contact_person' => 'Mohammed Al Rahman',
                'instructions' => 'Please indicate "Groupage Pro - Customer [Your Name]" on package. Operating hours: Sat-Thu 8AM-8PM.',
                'warehouse_code' => 'DXB-GRP',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Hub Europe Central - Amsterdam',
                'address' => 'Amsterdam Airport Schiphol, Cargo Center Building',
                'city' => 'Amsterdam',
                'postal_code' => '1118 BG',
                'country' => 'Netherlands',
                'phone' => '+31 20 601 2345',
                'email' => 'amsterdam@groupage.com',
                'contact_person' => 'Peter Van der Berg',
                'instructions' => 'Please mark "Groupage Pro - Customer [Your Name]" on package. Open Monday-Friday 9:00-17:00.',
                'warehouse_code' => 'AMS-GRP',
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($addresses as $address) {
            ReceivingAddress::create($address);
        }
    }
}
