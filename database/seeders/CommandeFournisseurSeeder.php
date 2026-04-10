<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CommandeFournisseur;
use App\Models\Fournisseur;

class CommandeFournisseurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fournisseur = Fournisseur::first();

        if ($fournisseur) {
            CommandeFournisseur::create([
                'reference' => 'CMD-2026-001',
                'fournisseur_id' => $fournisseur->id,
                'date_commande' => now()->subDays(5),
                'montant_ht' => 500000,
                'tva' => 50000,
                'montant_ttc' => 550000,
                'statut' => 'en_attente',
                'date_livraison_prevue' => now()->addDays(10),
                'service_demandeur' => 'Opérations',
                'notes' => 'Commande de matériel informatique',
                'created_by' => 1
            ]);

            CommandeFournisseur::create([
                'reference' => 'CMD-2026-002',
                'fournisseur_id' => $fournisseur->id,
                'date_commande' => now()->subDays(3),
                'montant_ht' => 750000,
                'tva' => 75000,
                'montant_ttc' => 825000,
                'statut' => 'confirmee',
                'date_livraison_prevue' => now()->addDays(7),
                'service_demandeur' => 'Maintenance',
                'notes' => 'Pièces de rechange pour véhicules',
                'created_by' => 1
            ]);

            CommandeFournisseur::create([
                'reference' => 'CMD-2026-003',
                'fournisseur_id' => $fournisseur->id,
                'date_commande' => now()->subDay(),
                'montant_ht' => 250000,
                'tva' => 25000,
                'montant_ttc' => 275000,
                'statut' => 'livree',
                'date_livraison_prevue' => now()->addDays(5),
                'date_livraison_reelle' => now()->subHours(6),
                'service_demandeur' => 'Administration',
                'notes' => 'Fournitures de bureau',
                'created_by' => 1
            ]);
        }
    }
}
