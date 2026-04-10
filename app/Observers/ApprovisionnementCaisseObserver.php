<?php

namespace App\Observers;

use App\Models\ApprovisionnementCaisse;
use App\Models\Caisse;
use Illuminate\Support\Facades\Log;

class ApprovisionnementCaisseObserver
{
    /**
     * Handle the ApprovisionnementCaisse "created" event.
     */
    public function created(ApprovisionnementCaisse $approvisionnement): void
    {
        // Mettre à jour le solde de la caisse si l'approvisionnement est validé
        if ($approvisionnement->statut === 'validé' && $approvisionnement->caisse_id) {
            $this->updateCaisseSolde($approvisionnement->caisse_id, $approvisionnement->montant, 'depot', $approvisionnement);
            
            Log::info("Solde caisse mis à jour - Approvisionnement #{$approvisionnement->id}: +{$approvisionnement->montant} FCFA (Caisse #{$approvisionnement->caisse_id})");
        }
    }

    /**
     * Handle the ApprovisionnementCaisse "updated" event.
     */
    public function updated(ApprovisionnementCaisse $approvisionnement): void
    {
        // Si le statut passe à 'validé', mettre à jour le solde
        if ($approvisionnement->wasChanged('statut') && $approvisionnement->statut === 'validé' && $approvisionnement->caisse_id) {
            $this->updateCaisseSolde($approvisionnement->caisse_id, $approvisionnement->montant, 'depot', $approvisionnement);
            
            Log::info("Solde caisse mis à jour - Approvisionnement #{$approvisionnement->id} validé: +{$approvisionnement->montant} FCFA (Caisse #{$approvisionnement->caisse_id})");
        }
        
        // Si le statut passe de 'validé' à autre chose, déduire le solde
        if ($approvisionnement->wasChanged('statut') && $approvisionnement->getOriginal('statut') === 'validé' && $approvisionnement->statut !== 'validé' && $approvisionnement->caisse_id) {
            $this->updateCaisseSolde($approvisionnement->caisse_id, $approvisionnement->montant, 'retrait', $approvisionnement);
            
            Log::info("Solde caisse déduit - Approvisionnement #{$approvisionnement->id} invalidé: -{$approvisionnement->montant} FCFA (Caisse #{$approvisionnement->caisse_id})");
        }
        
        // Si le montant change et l'approvisionnement est validé
        if ($approvisionnement->wasChanged('montant') && $approvisionnement->statut === 'validé' && $approvisionnement->caisse_id) {
            $ancienMontant = $approvisionnement->getOriginal('montant');
            $difference = $approvisionnement->montant - $ancienMontant;
            
            // Si le montant a augmenté, ajouter la différence
            if ($difference > 0) {
                $this->updateCaisseSolde($approvisionnement->caisse_id, $difference, 'depot', $approvisionnement);
            } 
            // Si le montant a diminué, déduire la différence
            else {
                $this->updateCaisseSolde($approvisionnement->caisse_id, abs($difference), 'retrait', $approvisionnement);
            }
            
            Log::info("Solde caisse ajusté - Approvisionnement #{$approvisionnement->id}: {$difference} FCFA (Caisse #{$approvisionnement->caisse_id})");
        }
    }

    /**
     * Handle the ApprovisionnementCaisse "deleted" event.
     */
    public function deleted(ApprovisionnementCaisse $approvisionnement): void
    {
        // Déduire le solde si l'approvisionnement était validé
        if ($approvisionnement->statut === 'validé' && $approvisionnement->caisse_id) {
            $this->updateCaisseSolde($approvisionnement->caisse_id, $approvisionnement->montant, 'retrait', $approvisionnement);
            
            Log::info("Solde caisse déduit - Approvisionnement #{$approvisionnement->id} supprimé: -{$approvisionnement->montant} FCFA (Caisse #{$approvisionnement->caisse_id})");
        }
    }

    /**
     * Mettre à jour le solde d'une caisse
     */
    private function updateCaisseSolde($caisseId, $montant, $type, $approvisionnement = null): void
    {
        try {
            $caisse = Caisse::find($caisseId);
            
            if (!$caisse) {
                Log::error("Caisse #{$caisseId} non trouvée pour la mise à jour du solde");
                return;
            }

            $soldeAvant = $caisse->solde_actuel;

            if ($type === 'depot') {
                $caisse->increment('solde_actuel', $montant);
            } else {
                $caisse->decrement('solde_actuel', $montant);
            }

            // Ajouter un mouvement pour le suivi
            $approvisionnementId = $approvisionnement ? $approvisionnement->id : 'N/A';
            $description = $type === 'depot' 
                ? "Approvisionnement #{$approvisionnementId}"
                : "Annulation approvisionnement #{$approvisionnementId}";

            if ($caisse->mouvements) {
                $caisse->mouvements()->create([
                    'type_mouvement' => $type === 'depot' ? 'APPROVISIONNEMENT' : 'ANNULATION',
                    'montant' => $montant,
                    'solde_avant' => $soldeAvant,
                    'solde_apres' => $caisse->solde_actuel,
                    'description' => $description,
                    'user_id' => auth()->id(),
                    'date_mouvement' => now(),
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour du solde de la caisse #{$caisseId}: " . $e->getMessage());
        }
    }
}
