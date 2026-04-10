<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OperationalService;

class OperationalServiceSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'code' => 'TRANSPORT',
                'nom' => 'Transport et location de camions',
                'email' => 'transport@kenamservices.net',
                'validateur_email' => 'validateur.transport@kenamservices.net',
                'emails_cc' => 'superviseur.transport@kenamservices.net,dg@kenamservices.net',
                'ordre' => 1
            ],
            [
                'code' => 'LIVRAISON',
                'nom' => 'Livraison ou manutention',
                'email' => 'livraison@kenamservices.net',
                'validateur_email' => 'validateur.livraison@kenamservices.net',
                'emails_cc' => 'superviseur.livraison@kenamservices.net,dg@kenamservices.net',
                'ordre' => 2
            ],
            [
                'code' => 'STOCKAGE',
                'nom' => 'Stockage et entreposage',
                'email' => 'stockage@kenamservices.net',
                'validateur_email' => 'validateur.stockage@kenamservices.net',
                'emails_cc' => 'superviseur.stockage@kenamservices.net,dg@kenamservices.net',
                'ordre' => 3
            ],
            [
                'code' => 'FLOTTE',
                'nom' => 'Suivi de flotte',
                'email' => 'flotte@kenamservices.net',
                'validateur_email' => 'validateur.flotte@kenamservices.net',
                'emails_cc' => 'superviseur.flotte@kenamservices.net,dg@kenamservices.net',
                'ordre' => 4
            ],
            [
                'code' => 'ASSISTANCE',
                'nom' => 'Assistance technique',
                'email' => 'assistance@kenamservices.net',
                'validateur_email' => 'validateur.assistance@kenamservices.net',
                'emails_cc' => 'superviseur.assistance@kenamservices.net,dg@kenamservices.net',
                'ordre' => 5
            ],
            [
                'code' => 'CONTROLE',
                'nom' => 'Contrôle et inspection',
                'email' => 'controle@kenamservices.net',
                'validateur_email' => 'validateur.controle@kenamservices.net',
                'emails_cc' => 'superviseur.controle@kenamservices.net,dg@kenamservices.net',
                'ordre' => 6
            ],
            [
                'code' => 'ADMIN',
                'nom' => 'Services administratifs',
                'email' => 'admin@kenamservices.net',
                'validateur_email' => 'validateur.admin@kenamservices.net',
                'emails_cc' => 'superviseur.admin@kenamservices.net,dg@kenamservices.net',
                'ordre' => 7
            ],
        ];

        foreach ($defaults as $i => $item) {
            OperationalService::updateOrCreate(
                ['code' => $item['code']],
                [
                    'nom' => $item['nom'],
                    'description' => null,
                    'email' => $item['email'],
                    'validateur_email' => $item['validateur_email'],
                    'emails_cc' => $item['emails_cc'],
                    'actif' => true,
                    'ordre' => $item['ordre'] ?? ($i+1),
                ]
            );
        }
    }
}
