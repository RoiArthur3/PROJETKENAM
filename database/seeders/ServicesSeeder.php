<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'nom' => 'Comptabilité',
                'code' => 'COMPTA',
                'email' => 'test@datasnf.net',
                'description' => 'Service comptable et financier - Gestion des opérations comptables',
                'signature_email' => "Cordialement,\nService Comptabilité\nKENAM Services"
            ],
            [
                'nom' => 'Ressources Humaines',
                'code' => 'RH',
                'email' => 'rh@kenam.ci',
                'description' => 'Gestion du personnel et des ressources humaines',
                'signature_email' => "Cordialement,\nService RH\nKENAM Services"
            ],
            [
                'nom' => 'Technique',
                'code' => 'TECH',
                'email' => 'technique@kenam.ci',
                'description' => 'Maintenance et support technique',
                'signature_email' => "Cordialement,\nService Technique\nKENAM Services"
            ],
            [
                'nom' => 'Magasin',
                'code' => 'MAGASIN',
                'email' => 'magasin@kenam.ci',
                'description' => 'Gestion des stocks et approvisionnements',
                'signature_email' => "Cordialement,\nService Magasin\nKENAM Services"
            ],
            [
                'nom' => 'Maintenance',
                'code' => 'MAINT',
                'email' => 'maintenance@kenam.ci',
                'description' => 'Maintenance des équipements et véhicules',
                'signature_email' => "Cordialement,\nService Maintenance\nKENAM Services"
            ],
            [
                'nom' => 'Achat',
                'code' => 'ACHAT',
                'email' => 'achat@kenam.ci',
                'description' => 'Gestion des achats et fournisseurs',
                'signature_email' => "Cordialement,\nService Achat\nKENAM Services"
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['code' => $service['code']],
                $service
            );
        }
    }
}
