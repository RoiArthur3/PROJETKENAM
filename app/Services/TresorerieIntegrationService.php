<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class TresorerieIntegrationService
{
    /**
     * Intègre automatiquement les opérations validées (VENTES) en encaissements
     */
    public function integrerVentesAutomatiquement()
    {
        if (!Schema::hasTable('operations') || !Schema::hasTable('encaissements')) {
            return ['success' => false, 'message' => 'Tables manquantes'];
        }

        $operationsIntegrees = 0;
        $montantTotal = 0;

        // Récupérer les opérations validées non encore intégrées
        $operations = DB::table('operations')
            ->where('statut_courant', 'validée')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('encaissements')
                    ->whereColumn('encaissements.operation_id', 'operations.id');
            })
            ->get();

        foreach ($operations as $operation) {
            try {
                // Créer l'encaissement automatique
                DB::table('encaissements')->insert([
                    'date_encaissement' => $operation->date_operation ?? Carbon::now(),
                    'type_encaissement' => 'vente',
                    'montant' => $operation->montant,
                    'mode_paiement' => 'espece', // Par défaut, peut être configuré
                    'caisse_id' => $this->getCaissePrincipaleId(),
                    'operation_id' => $operation->id,
                    'compte_comptable' => '531000', // Caisse
                    'description' => 'Encaissement automatique - ' . ($operation->titre ?? 'Opération #' . $operation->id),
                    'statut' => 'valide',
                    'encaisseur_id' => $operation->user_id ?? 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // Créer le mouvement de caisse correspondant
                $this->creerMouvementCaisse('entree', $operation->montant, 'encaissement', 'ENC-' . $operation->id, $operation->titre ?? 'Encaissement automatique');

                $operationsIntegrees++;
                $montantTotal += $operation->montant;

            } catch (\Exception $e) {
                // Log l'erreur mais continue avec les autres opérations
                \Log::error('Erreur intégration vente #' . $operation->id . ': ' . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'operations_integrees' => $operationsIntegrees,
            'montant_total' => $montantTotal,
            'message' => $operationsIntegrees . ' ventes intégrées pour ' . number_format($montantTotal, 0) . ' FCFA'
        ];
    }

    /**
     * Intègre automatiquement les achats validés en dépenses
     */
    public function integrerAchatsAutomatiquement()
    {
        if (!Schema::hasTable('operations') || !Schema::hasTable('depense_caisse')) {
            return ['success' => false, 'message' => 'Tables manquantes'];
        }

        $achatsIntegres = 0;
        $montantTotal = 0;

        // Récupérer les achats validés non encore intégrés
        $operations = DB::table('operations')
            ->where('type', 'achat')
            ->where('statut_courant', 'validée')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('depense_caisse')
                    ->where('depense_caisse.reference', 'operations.id');
            })
            ->get();

        foreach ($operations as $operation) {
            try {
                // Créer la dépense automatique
                DB::table('depense_caisse')->insert([
                    'date_depense' => $operation->date_operation ?? Carbon::now(),
                    'type_depense' => 'achat',
                    'montant' => $operation->montant,
                    'beneficiaire' => $this->getBeneficiaireAchat($operation),
                    'reference' => 'OP-' . $operation->id,
                    'caisse_id' => $this->getCaissePrincipaleId(),
                    'compte_comptable' => '601000', // Achats de matériel
                    'description' => 'Dépense automatique - ' . ($operation->titre ?? 'Achat #' . $operation->id),
                    'statut' => 'valide',
                    'demandeur_id' => $operation->user_id ?? 1,
                    'valideur_id' => $operation->user_id ?? 1,
                    'date_validation' => Carbon::now(),
                    'date_paiement' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // Créer le mouvement de caisse correspondant
                $this->creerMouvementCaisse('sortie', $operation->montant, 'depense_caisse', 'DEP-' . $operation->id, $operation->titre ?? 'Dépense automatique');

                $achatsIntegres++;
                $montantTotal += $operation->montant;

            } catch (\Exception $e) {
                \Log::error('Erreur intégration achat #' . $operation->id . ': ' . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'achats_integres' => $achatsIntegres,
            'montant_total' => $montantTotal,
            'message' => $achatsIntegres . ' achats intégrés pour ' . number_format($montantTotal, 0) . ' FCFA'
        ];
    }

    /**
     * Crée un mouvement de caisse
     */
    private function creerMouvementCaisse($type, $montant, $typeDocument, $reference, $libelle)
    {
        if (!Schema::hasTable('mouvement_caisse')) {
            return false;
        }

        $caisseId = $this->getCaissePrincipaleId();
        $soldeAvant = $this->getSoldeCaisse($caisseId);
        $soldeApres = $type === 'entree' ? $soldeAvant + $montant : $soldeAvant - $montant;

        try {
            DB::table('mouvement_caisse')->insert([
                'date_mouvement' => Carbon::now(),
                'type_mouvement' => $type,
                'montant' => $montant,
                'libelle' => $libelle,
                'caisse_id' => $caisseId,
                'reference' => $reference,
                'type_document' => $typeDocument,
                'solde_avant' => $soldeAvant,
                'solde_apres' => $soldeApres,
                'user_id' => auth()->id() ?? 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Mettre à jour le solde de la caisse
            DB::table('caisses')->where('id', $caisseId)->update(['solde_actuel' => $soldeApres]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Erreur création mouvement caisse: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si un approvisionnement automatique est nécessaire
     */
    public function verifierApprovisionnementAutomatique()
    {
        if (!Schema::hasTable('caisses') || !Schema::hasTable('approvisionnement_caisse')) {
            return ['success' => false, 'message' => 'Tables manquantes'];
        }

        $soldeCaisse = $this->getSoldeCaisse($this->getCaissePrincipaleId());
        $seuilMin = 100000; // 100k FCFA

        if ($soldeCaisse < $seuilMin) {
            // Vérifier si une demande d'approvisionnement est déjà en cours
            $demandeExistante = DB::table('approvisionnement_caisse')
                ->where('caisse_id', $this->getCaissePrincipaleId())
                ->where('statut', 'en_attente')
                ->exists();

            if (!$demandeExistante) {
                try {
                    DB::table('approvisionnement_caisse')->insert([
                        'date_approvisionnement' => Carbon::now(),
                        'montant' => 500000, // 500k FCFA
                        'mode_paiement' => 'virement',
                        'reference' => 'AUTO-APPROV-' . Carbon::now()->format('YmdHis'),
                        'caisse_id' => $this->getCaissePrincipaleId(),
                        'compte_bancaire_id' => $this->getCompteBancairePrincipalId(),
                        'statut' => 'en_attente',
                        'motif' => 'Approvisionnement automatique - Solde faible (' . number_format($soldeCaisse, 0) . ' FCFA < ' . number_format($seuilMin, 0) . ' FCFA)',
                        'demandeur_id' => auth()->id() ?? 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    return [
                        'success' => true,
                        'action' => 'demande_cree',
                        'message' => 'Demande d\'approvisionnement automatique créée: 500,000 FCFA'
                    ];
                } catch (\Exception $e) {
                    return ['success' => false, 'message' => 'Erreur création demande: ' . $e->getMessage()];
                }
            }
        }

        return ['success' => true, 'action' => 'aucune', 'message' => 'Solde suffisant, aucune action nécessaire'];
    }

    /**
     * Génère un rapport de trésorerie complet
     */
    public function genererRapportTresorerie()
    {
        $rapport = [
            'periode' => Carbon::now()->format('Y-m'),
            'date_generation' => Carbon::now(),
            'soldes' => $this->getSoldesActuels(),
            'mouvements' => $this->getMouvementsDuMois(),
            'integrations' => [
                'ventes' => $this->integrerVentesAutomatiquement(),
                'achats' => $this->integrerAchatsAutomatiquement(),
            ],
            'approvisionnement' => $this->verifierApprovisionnementAutomatique(),
            'statistiques' => $this->getStatistiquesTresorerie()
        ];

        return $rapport;
    }

    /**
     * Méthodes utilitaires privées
     */
    private function getCaissePrincipaleId()
    {
        $caisse = DB::table('caisses')->where('est_principale', true)->first();
        return $caisse ? $caisse->id : 1;
    }

    private function getCompteBancairePrincipalId()
    {
        $compte = DB::table('compte_bancaires')->first();
        return $compte ? $compte->id : 1;
    }

    private function getSoldeCaisse($caisseId)
    {
        $caisse = DB::table('caisses')->where('id', $caisseId)->first();
        return $caisse ? $caisse->solde_actuel : 0;
    }

    private function getBeneficiaireAchat($operation)
    {
        if ($operation->fournisseur_id) {
            $fournisseur = DB::table('fournisseurs')->where('id', $operation->fournisseur_id)->first();
            return $fournisseur ? $fournisseur->nom : 'Fournisseur #' . $operation->fournisseur_id;
        }
        return 'Non spécifié';
    }

    private function getSoldesActuels()
    {
        return [
            'solde_caisse' => DB::table('caisses')->sum('solde_actuel'),
            'solde_banque' => DB::table('compte_bancaires')->sum('solde'),
            'total_tresorerie' => DB::table('caisses')->sum('solde_actuel') + DB::table('compte_bancaires')->sum('solde'),
        ];
    }

    private function getMouvementsDuMois()
    {
        return [
            'entrees' => DB::table('mouvement_caisse')
                ->whereMonth('date_mouvement', Carbon::now()->month)
                ->where('type_mouvement', 'entree')
                ->sum('montant'),
            'sorties' => DB::table('mouvement_caisse')
                ->whereMonth('date_mouvement', Carbon::now()->month)
                ->where('type_mouvement', 'sortie')
                ->sum('montant'),
        ];
    }

    private function getStatistiquesTresorerie()
    {
        return [
            'total_encaissements' => DB::table('encaissements')->where('statut', 'valide')->sum('montant'),
            'total_depenses' => DB::table('depense_caisse')->where('statut', 'valide')->sum('montant'),
            'total_approvisionnements' => DB::table('approvisionnement_caisse')->count(),
            'approvisionnements_en_attente' => DB::table('approvisionnement_caisse')->where('statut', 'en_attente')->count(),
            'total_virements' => DB::table('virements')->count(),
            'total_avances' => DB::table('avances')->count(),
        ];
    }
}
