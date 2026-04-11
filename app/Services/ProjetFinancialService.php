<?php

namespace App\Services;

use App\Models\Operation;
use App\Models\VehiclePointage;
use Illuminate\Support\Facades\Log;

class ProjetFinancialService
{
    /**
     * Mettre à jour le montant à facturer d'un projet en fonction des pointages
     */
    public function updateMontantFacturer($projetId)
    {
        try {
            $projet = Operation::findOrFail($projetId);

            // Récupérer tous les pointages validés des véhicules du projet
            $vehiculeIds = $projet->vehicules()->pluck('vehicules.id');

            $totalClientAmount = \App\Models\Pointage::whereIn('vehicle_id', $vehiculeIds)
                ->where('statut', 'validé')
                ->sum('total_client_amount');

            // Mettre à jour le montant à facturer du projet
            $projet->montant_facturer = $totalClientAmount;
            $projet->save();

            Log::info("Montant à facturer mis à jour pour le projet {$projetId}: {$totalClientAmount} FCFA");

            return $totalClientAmount;

        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour du montant à facturer du projet {$projetId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculer le montant selon les règles de gestion
     * - Engin standard: tarif horaire
     * - Camion plateau: tarif par voyage ou par jour
     * - Autres types: selon configuration
     */
    public function calculerMontantPointage($vehicleId, $quantity, $unitType, $fournisseurId)
    {
        $vehicle = \App\Models\Vehicule::findOrFail($vehicleId);

        // Règles de gestion selon le type de matériel
        switch ($vehicle->type_materiel) {
            case 'Camion':
                return $this->calculerTarifCamion($vehicle, $quantity, $unitType, $fournisseurId);

            case 'Engin':
            case 'Machine':
                return $this->calculerTarifEngin($vehicle, $quantity, $unitType, $fournisseurId);

            default:
                return $this->calculerTarifStandard($vehicle, $quantity, $unitType, $fournisseurId);
        }
    }

    /**
     * Calculer le tarif pour les camions (plateau)
     */
    private function calculerTarifCamion($vehicle, $quantity, $unitType, $fournisseurId)
    {
        $isKenam = $fournisseurId === 'kenam';

        if ($unitType === 'voyage') {
            // Tarif par voyage
            $clientPrice = $vehicle->prix_location ?? 0;
            $supplierCost = $isKenam ? 0 : ($vehicle->prix_achat ?? 0);
        } elseif ($unitType === 'jour') {
            // Tarif journalier
            $clientPrice = ($vehicle->prix_location ?? 0) * 8; // 8 heures = 1 jour
            $supplierCost = $isKenam ? 0 : (($vehicle->prix_achat ?? 0) * 8);
        } else {
            // Tarif horaire (par défaut)
            $clientPrice = $vehicle->prix_location ?? 0;
            $supplierCost = $isKenam ? 0 : ($vehicle->prix_achat ?? 0);
        }

        return [
            'client_unit_price' => $clientPrice,
            'supplier_unit_cost' => $supplierCost,
            'total_client_amount' => $clientPrice * $quantity,
            'total_supplier_cost' => $supplierCost * $quantity,
        ];
    }

    /**
     * Calculer le tarif pour les engins et machines
     */
    private function calculerTarifEngin($vehicle, $quantity, $unitType, $fournisseurId)
    {
        $isKenam = $fournisseurId === 'kenam';

        // Pour les engins, généralement tarif horaire
        $clientPrice = $vehicle->prix_location ?? 0;
        $supplierCost = $isKenam ? 0 : ($vehicle->prix_achat ?? 0);

        // Si tarif journalier, multiplier par 8 heures
        if ($unitType === 'jour') {
            $clientPrice *= 8;
            $supplierCost *= 8;
        }

        return [
            'client_unit_price' => $clientPrice,
            'supplier_unit_cost' => $supplierCost,
            'total_client_amount' => $clientPrice * $quantity,
            'total_supplier_cost' => $supplierCost * $quantity,
        ];
    }

    /**
     * Calculer le tarif standard
     */
    private function calculerTarifStandard($vehicle, $quantity, $unitType, $fournisseurId)
    {
        $isKenam = $fournisseurId === 'kenam';

        $clientPrice = $vehicle->prix_location ?? 0;
        $supplierCost = $isKenam ? 0 : ($vehicle->prix_achat ?? 0);

        return [
            'client_unit_price' => $clientPrice,
            'supplier_unit_cost' => $supplierCost,
            'total_client_amount' => $clientPrice * $quantity,
            'total_supplier_cost' => $supplierCost * $quantity,
        ];
    }

    /**
     * Obtenir les statistiques financières d'un projet
     */
    public function getStatistiquesFinancieres($projetId)
    {
        try {
            $projet = Operation::with(['vehicules'])->findOrFail($projetId);
            $vehiculeIds = $projet->vehicules()->pluck('vehicules.id');

            $pointages = \App\Models\Pointage::with(['vehicle'])
                ->whereIn('vehicle_id', $vehiculeIds)
                ->where('statut', 'validé')
                ->get();

            // Regrouper par type de matériel
            $statsByType = [];
            foreach ($pointages as $pointage) {
                $type = $pointage->vehicle->type_materiel ?? 'Non défini';

                if (!isset($statsByType[$type])) {
                    $statsByType[$type] = [
                        'total_unites' => 0,
                        'total_client' => 0,
                        'total_fournisseur' => 0,
                        'total_marge' => 0,
                        'nombre_pointages' => 0,
                    ];
                }

                $statsByType[$type]['total_unites'] += $pointage->quantity ?? 0;
                $statsByType[$type]['total_client'] += $pointage->total_client_amount ?? 0;
                $statsByType[$type]['total_fournisseur'] += $pointage->total_supplier_cost ?? 0;
                $statsByType[$type]['total_marge'] += ($pointage->total_client_amount ?? 0) - ($pointage->total_supplier_cost ?? 0);
                $statsByType[$type]['nombre_pointages']++;
            }

            // Calculer les totaux généraux
            $totalGeneral = [
                'total_unites' => $pointages->sum('quantity'),
                'total_client' => $pointages->sum('total_client_amount'),
                'total_fournisseur' => $pointages->sum('total_supplier_cost'),
                'total_marge' => $pointages->sum(function($p) {
                    return ($p->total_client_amount ?? 0) - ($p->total_supplier_cost ?? 0);
                }),
                'nombre_pointages' => $pointages->count(),
            ];

            return [
                'stats_by_type' => $statsByType,
                'total_general' => $totalGeneral,
                'projet_info' => [
                    'cout_estimatif' => $projet->cout_estimatif ?? 0,
                    'montant_facturer' => $projet->montant_facturer ?? 0,
                    'marge_previsionnelle' => ($projet->montant_facturer ?? 0) - ($projet->cout_estimatif ?? 0),
                ]
            ];

        } catch (\Exception $e) {
            Log::error("Erreur lors du calcul des statistiques financières du projet {$projetId}: " . $e->getMessage());
            throw $e;
        }
    }
}
