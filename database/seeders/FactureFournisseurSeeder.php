<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FactureFournisseur;
use App\Models\Fournisseur;

class FactureFournisseurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fournisseur = Fournisseur::first();

        if ($fournisseur) {
            FactureFournisseur::create([
                'reference' => 'FAC-2026-001',
                'fournisseur_id' => $fournisseur->id,
                'date_facture' => now()->subDays(10),
                'date_echeance' => now()->addDays(20),
                'montant_ht' => 500000,
                'tva' => 50000,
                'montant_ttc' => 550000,
                'statut' => 'en_attente',
                'est_payee' => false,
                'est_partiellement_payee' => false,
                'montant_paye' => 0,
                'notes' => 'Facture pour matériel informatique',
                'created_by' => 1
            ]);

            FactureFournisseur::create([
                'reference' => 'FAC-2026-002',
                'fournisseur_id' => $fournisseur->id,
                'date_facture' => now()->subDays(8),
                'date_echeance' => now()->addDays(15),
                'montant_ht' => 750000,
                'tva' => 75000,
                'montant_ttc' => 825000,
                'statut' => 'partiellement_payee',
                'est_payee' => false,
                'est_partiellement_payee' => true,
                'montant_paye' => 400000,
                'notes' => 'Facture pour pièces de rechange',
                'created_by' => 1
            ]);

            FactureFournisseur::create([
                'reference' => 'FAC-2026-003',
                'fournisseur_id' => $fournisseur->id,
                'date_facture' => now()->subDays(5),
                'date_echeance' => now()->addDays(10),
                'montant_ht' => 250000,
                'tva' => 25000,
                'montant_ttc' => 275000,
                'statut' => 'payee',
                'est_payee' => true,
                'est_partiellement_payee' => false,
                'montant_paye' => 275000,
                'notes' => 'Facture pour fournitures de bureau',
                'created_by' => 1
            ]);
        }
    }
}
