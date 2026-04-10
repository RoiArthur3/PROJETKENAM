<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EntrepriseSettings;

class EntrepriseSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EntrepriseSettings::updateOrCreate(
            ['id' => 1],
            [
                'nom_entreprise' => 'KENAM SERVICES',
                'sigle' => 'KENAM',
                'adresse' => 'Lomé, TOGO',
                'telephone' => '+228 90 00 00 00',
                'email_contact' => 'contact@kenamservices.net',
                'site_web' => 'https://kenamservices.net',
                'rccm' => 'TG-LOM-2023-B-12345',
                'compte_bancaire' => 'TG00123456789',
                'ifu' => '1234567890123',
                'cnss' => '987654321098',
                'email_noreply' => 'noreply@kenamservices.net',
                'email_support' => 'support@kenamservices.net',
                'telephone_support' => '+228 91 00 00 00',
                'actif' => true,
            ]
        );
    }
}
