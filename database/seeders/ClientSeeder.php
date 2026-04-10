<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\ClientAddress;
use App\Models\ClientContact;
use App\Models\User;
use Carbon\Carbon;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'company_name' => 'SARL Import Export',
                'contact_person' => 'Jean Dupont',
                'phone' => '+33612345678',
                'email' => 'jean.dupont@importexport.fr',
                'client_type' => 'company',
                'payment_terms' => '30days',
                'preferred_transport_mode' => 'air_normal',
                'business_sector' => 'Import/Export',
                'tax_id' => 'FR12345678901',
                'commercial_register' => 'RCS Paris 123 456 789',
                'is_vip' => true,
                'credit_limit' => 10000.00,
                'address' => [
                    'address_line_1' => '123 Avenue des Champs-Élysées',
                    'city' => 'Paris',
                    'country' => 'France',
                    'postal_code' => '75008',
                    'contact_person' => 'Jean Dupont',
                    'phone' => '+33612345678',
                ],
                'contact' => [
                    'first_name' => 'Jean',
                    'last_name' => 'Dupont',
                    'position' => 'Directeur Général',
                    'phone' => '+33612345678',
                    'mobile' => '+33687654321',
                    'email' => 'jean.dupont@importexport.fr',
                    'preferred_contact_method' => 'email',
                ],
            ],
            [
                'contact_person' => 'Marie Martin',
                'phone' => '+33623456789',
                'email' => 'marie.martin@gmail.com',
                'client_type' => 'individual',
                'payment_terms' => 'cod',
                'preferred_transport_mode' => 'sea',
                'credit_limit' => 1000.00,
                'address' => [
                    'address_line_1' => '45 Rue de la République',
                    'city' => 'Lyon',
                    'country' => 'France',
                    'postal_code' => '69001',
                    'contact_person' => 'Marie Martin',
                    'phone' => '+33623456789',
                ],
                'contact' => [
                    'first_name' => 'Marie',
                    'last_name' => 'Martin',
                    'phone' => '+33623456789',
                    'mobile' => '+33698765432',
                    'email' => 'marie.martin@gmail.com',
                    'preferred_contact_method' => 'phone',
                ],
            ],
            [
                'company_name' => 'Tech Solutions Africa',
                'contact_person' => 'Ahmed Diallo',
                'phone' => '+22176543210',
                'email' => 'ahmed.diallo@techsolutions.sn',
                'client_type' => 'company',
                'payment_terms' => '14days',
                'preferred_transport_mode' => 'air_express',
                'business_sector' => 'Technology',
                'tax_id' => 'SN98765432109',
                'credit_limit' => 5000.00,
                'address' => [
                    'address_line_1' => 'Boulevard de la Liberation',
                    'city' => 'Dakar',
                    'country' => 'Senegal',
                    'postal_code' => 'BP 12345',
                    'contact_person' => 'Ahmed Diallo',
                    'phone' => '+22176543210',
                ],
                'contact' => [
                    'first_name' => 'Ahmed',
                    'last_name' => 'Diallo',
                    'position' => 'CEO',
                    'phone' => '+22176543210',
                    'mobile' => '+22112345678',
                    'email' => 'ahmed.diallo@techsolutions.sn',
                    'preferred_contact_method' => 'email',
                ],
            ],
            [
                'company_name' => 'Global Trading Ltd',
                'contact_person' => 'Li Wei',
                'phone' => '+8613812345678',
                'email' => 'li.wei@globaltrading.cn',
                'client_type' => 'company',
                'payment_terms' => '7days',
                'preferred_transport_mode' => 'sea',
                'business_sector' => 'Trading',
                'tax_id' => 'CN123456789012345',
                'commercial_register' => '91310000MA1FL0001X',
                'is_vip' => true,
                'credit_limit' => 15000.00,
                'address' => [
                    'address_line_1' => '1234 Nanjing Road',
                    'city' => 'Shanghai',
                    'country' => 'China',
                    'postal_code' => '200001',
                    'contact_person' => 'Li Wei',
                    'phone' => '+8613812345678',
                ],
                'contact' => [
                    'first_name' => 'Li',
                    'last_name' => 'Wei',
                    'position' => 'General Manager',
                    'phone' => '+8613812345678',
                    'mobile' => '+8613987654321',
                    'email' => 'li.wei@globaltrading.cn',
                    'preferred_contact_method' => 'email',
                ],
            ],
            [
                'contact_person' => 'Sophie Laurent',
                'phone' => '+33634567890',
                'email' => 'sophie.laurent@orange.fr',
                'client_type' => 'individual',
                'payment_terms' => 'cod',
                'preferred_transport_mode' => 'air_normal',
                'credit_limit' => 500.00,
                'address' => [
                    'address_line_1' => '12 Place de la Concorde',
                    'city' => 'Marseille',
                    'country' => 'France',
                    'postal_code' => '13001',
                    'contact_person' => 'Sophie Laurent',
                    'phone' => '+33634567890',
                ],
                'contact' => [
                    'first_name' => 'Sophie',
                    'last_name' => 'Laurent',
                    'phone' => '+33634567890',
                    'mobile' => '+33678901234',
                    'email' => 'sophie.laurent@orange.fr',
                    'preferred_contact_method' => 'sms',
                ],
            ],
        ];

        foreach ($clients as $clientData) {
            // Extract address and contact data
            $addressData = $clientData['address'] ?? null;
            $contactData = $clientData['contact'] ?? null;

            // Remove address and contact from client data
            unset($clientData['address'], $clientData['contact']);

            // Add additional fields
            $clientData['client_code'] = Client::generateClientCode();
            $clientData['registration_date'] = Carbon::now()->subDays(rand(30, 365));
            $clientData['total_orders'] = rand(1, 50);
            $clientData['total_revenue'] = rand(500, 25000);
            $clientData['average_order_value'] = $clientData['total_revenue'] / $clientData['total_orders'];
            $clientData['loyalty_points'] = rand(100, 5000);
            $clientData['last_order_date'] = Carbon::now()->subDays(rand(1, 30));
            $clientData['referral_source'] = ['website', 'referral', 'advertisement', 'social_media'][rand(0, 3)];

            // Create client
            $client = Client::create($clientData);

            // Create primary address if provided
            if ($addressData) {
                ClientAddress::create([
                    'client_id' => $client->id,
                    'address_type' => 'shipping',
                    'is_primary' => true,
                    'is_active' => true,
                ] + $addressData);
            }

            // Create primary contact if provided
            if ($contactData) {
                ClientContact::create([
                    'client_id' => $client->id,
                    'contact_type' => 'primary',
                    'is_primary' => true,
                    'is_active' => true,
                ] + $contactData);
            }
        }

        $this->command->info('Clients seeded successfully!');
    }
}
