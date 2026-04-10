<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Caisse;
use App\Models\CompteBancaire;
use App\Models\Banque;

class TreasurySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si les données existent déjà
        if (Caisse::count() === 0) {
            // Créer une caisse principale
            Caisse::create([
                'code' => 'CAISSE_001',
                'nom' => 'Caisse Principale',
                'libelle' => 'Caisse de la trésorerie',
                'type' => 'principale',
                'solde_initial' => 5000000,
                'solde_actuel' => 5000000,
                'devise' => 'FCFA',
                'responsable_id' => null,
                'description' => 'Caisse principale de gestion de la trésorerie',
                'est_active' => true,
            ]);

            // Créer une caisse secondaire
            Caisse::create([
                'code' => 'CAISSE_002',
                'nom' => 'Caisse Secondaire',
                'libelle' => 'Caisse auxiliaire',
                'type' => 'secondaire',
                'solde_initial' => 1000000,
                'solde_actuel' => 1000000,
                'devise' => 'FCFA',
                'responsable_id' => null,
                'description' => 'Caisse secondaire pour dépenses courantes',
                'est_active' => true,
            ]);

            $this->command->info('✓ Caisses créées avec succès');
        } else {
            $this->command->info('→ Caisses existent déjà (skipped)');
        }

        // Créer une banque si elle n'existe pas
        $banque = Banque::firstOrCreate([
            'nom' => 'Banque Régionale',
        ], [
            'code_banque' => 'BR001',
            'adresse' => 'Dakar',
            'telephone' => '+221 33 123 45 67',
            'email' => 'contact@banque-regionale.sn',
            'est_active' => true,
        ]);

        // Vérifier si les comptes bancaires existent
        if (CompteBancaire::count() === 0) {
            // Créer un compte courant
            CompteBancaire::create([
                'banque_id' => $banque->id,
                'numero_compte' => '001234567890',
                'intitule_compte' => 'Compte Opérationnel KENAM',
                'type_compte' => 'courant',
                'devise' => 'XOF',
                'solde' => 10000000,
                'solde_ouverture' => 10000000,
                'date_ouverture' => now()->subYears(2),
                'nom_titulaire' => 'KENAM SERVICES SARL',
                'adresse_titulaire' => 'Dakar, Senegal',
                'telephone_titulaire' => '+221 33 XXX XX XX',
                'est_actif' => true,
                'informations_supplementaires' => 'Compte principal pour les opérations courantes',
            ]);

            // Créer un compte épargne
            CompteBancaire::create([
                'banque_id' => $banque->id,
                'numero_compte' => '001234567891',
                'intitule_compte' => 'Compte Réserve KENAM',
                'type_compte' => 'epargne',
                'devise' => 'XOF',
                'solde' => 5000000,
                'solde_ouverture' => 5000000,
                'date_ouverture' => now()->subYears(1),
                'nom_titulaire' => 'KENAM SERVICES SARL',
                'adresse_titulaire' => 'Dakar, Senegal',
                'telephone_titulaire' => '+221 33 XXX XX XX',
                'est_actif' => true,
                'informations_supplementaires' => 'Compte épargne pour la réserve',
            ]);

            $this->command->info('✓ Comptes bancaires créés avec succès');
            $this->command->info('  - Compte courant: 10,000,000 FCFA');
            $this->command->info('  - Compte épargne: 5,000,000 FCFA');
            $this->command->info('  - TOTAL BANQUES: 15,000,000 FCFA');
        } else {
            $this->command->info('→ Comptes bancaires existent déjà (skipped)');
        }

        // Afficher le résumé
        $totalCaisses = Caisse::where('est_active', true)->sum('solde_actuel');
        $totalBanques = CompteBancaire::where('est_actif', true)->sum('solde');
        
        $this->command->info('');
        $this->command->info('=== RÉSUMÉ TRÉSORERIE ===');
        $this->command->info('Caisses actives: ' . number_format($totalCaisses, 0, ',', ' ') . ' FCFA');
        $this->command->info('Comptes bancaires actifs: ' . number_format($totalBanques, 0, ',', ' ') . ' FCFA');
        $this->command->info('TOTAL: ' . number_format($totalCaisses + $totalBanques, 0, ',', ' ') . ' FCFA');
    }
}
