<?php

namespace App\Observers;

use App\Models\DepenseCaisse;
use App\Models\Caisse;
use App\Models\MouvementCaisse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DepenseCaisseObserver
{
    /**
     * Handle the DepenseCaisse "created" event.
     */
    public function created(DepenseCaisse $depense): void
    {
        // Mettre à jour le solde de la caisse si la dépense est validée
        // Vérifier les variantes possibles du statut validé
        $isValide = $this->isValidatedStatus($depense->statut);
        
        if ($isValide && $depense->caisse_id) {
            $this->updateCaisseSolde($depense->caisse_id, $depense->montant, 'retrait', $depense);

            Log::info("Solde caisse mis à jour - Dépense décaissement #{$depense->id}: -{$depense->montant} FCFA (Caisse #{$depense->caisse_id})");
        }
    }

    /**
     * Handle the DepenseCaisse "updated" event.
     */
    public function updated(DepenseCaisse $depense): void
    {
        $isValide = $this->isValidatedStatus($depense->statut);
        $wasValide = $this->isValidatedStatus($depense->getOriginal('statut'));
        
        // Si le statut passe à 'validé', mettre à jour le solde
        if ($depense->wasChanged('statut') && $isValide && $depense->caisse_id) {
            // Vérifier que l'ancien montant était 0 ou que ce n'était pas validé avant
            if (!$wasValide) {
                $this->updateCaisseSolde($depense->caisse_id, $depense->montant, 'retrait', $depense);
                Log::info("Solde caisse mis à jour - Dépense décaissement #{$depense->id} validée: -{$depense->montant} FCFA (Caisse #{$depense->caisse_id})");
            }
        }

        // Si le statut passe de 'validé' à autre chose, rembourser le solde
        if ($depense->wasChanged('statut') && $wasValide && !$isValide && $depense->caisse_id) {
            $this->updateCaisseSolde($depense->caisse_id, $depense->montant, 'depot', $depense);
            Log::info("Solde caisse remboursé - Dépense décaissement #{$depense->id} invalidée: +{$depense->montant} FCFA (Caisse #{$depense->caisse_id})");
        }

        // Si le montant change et la dépense est validée
        if ($depense->wasChanged('montant') && $isValide && $depense->caisse_id) {
            $ancienMontant = $depense->getOriginal('montant');
            $difference = $depense->montant - $ancienMontant;

            // Si le montant a augmenté, déduire la différence supplémentaire
            if ($difference > 0) {
                $this->updateCaisseSolde($depense->caisse_id, $difference, 'retrait', $depense);
            }
            // Si le montant a diminué, ajouter la différence
            else {
                $this->updateCaisseSolde($depense->caisse_id, abs($difference), 'depot', $depense);
            }

            Log::info("Solde caisse ajusté - Dépense décaissement #{$depense->id} changement montant: {$difference} FCFA (Caisse #{$depense->caisse_id})");
        }
        
        // Gérer le changement de caisse
        if ($depense->wasChanged('caisse_id') && $isValide) {
            $ancienneCaisseId = $depense->getOriginal('caisse_id');
            
            // Rembourser l'ancienne caisse
            if ($ancienneCaisseId) {
                $this->updateCaisseSolde($ancienneCaisseId, $depense->montant, 'depot', $depense);
                Log::info("Solde ancienne caisse remboursé - Dépense décaissement #{$depense->id} transférée de #{$ancienneCaisseId} à #{$depense->caisse_id}");
            }
            
            // Débiter la nouvelle caisse
            $this->updateCaisseSolde($depense->caisse_id, $depense->montant, 'retrait', $depense);
        }
    }

    /**
     * Handle the DepenseCaisse "deleted" event.
     */
    public function deleted(DepenseCaisse $depense): void
    {
        // Rembourser le solde si la dépense était validée
        $isValide = $this->isValidatedStatus($depense->statut);
        
        if ($isValide && $depense->caisse_id) {
            $this->updateCaisseSolde($depense->caisse_id, $depense->montant, 'depot', $depense);
            Log::info("Solde caisse remboursé - Dépense décaissement #{$depense->id} supprimée: +{$depense->montant} FCFA (Caisse #{$depense->caisse_id})");
        }
    }

    /**
     * Mettre à jour le solde d'une caisse et créer un mouvement de suivi
     */
    private function updateCaisseSolde($caisseId, $montant, $type, $depense = null): void
    {
        try {
            $caisse = Caisse::findOrFail($caisseId);

            // Les montants negatifs inversent le sens de l'operation
            // (ex: retrait -5000 => depot 5000).
            $montant = (float) $montant;
            $normalizedMontant = abs($montant);
            $effectiveType = $type;

            if ($montant < 0) {
                $effectiveType = $type === 'retrait' ? 'depot' : 'retrait';
            }
            
            // Vérifier qu'il y a assez de solde pour un retrait
            if ($effectiveType === 'retrait' && $caisse->solde_actuel < $normalizedMontant) {
                Log::warning("Tentative de retrait insuffisant - Caisse #{$caisseId}: solde={$caisse->solde_actuel}, montant={$normalizedMontant}");
                // On continue quand même pour permettre les découverts temporaires (ajustements)
            }

            $soldeAvant = $caisse->solde_actuel;

            // Mettre à jour le solde de la caisse
            if ($effectiveType === 'retrait') {
                $caisse->decrement('solde_actuel', $normalizedMontant);
            } else {
                $caisse->increment('solde_actuel', $normalizedMontant);
            }

            // Enregistrer le mouvement de caisse pour le suivi
            try {
                $this->enregistrerMouvement($caisse, $normalizedMontant, $effectiveType, $soldeAvant, $depense);
            } catch (\Exception $e) {
                Log::warning("Impossible d'enregistrer le mouvement de caisse: " . $e->getMessage());
                // On ne lève pas l'exception pour ne pas bloquer la mise à jour du solde
            }

            Log::info("Solde caisse #{$caisseId} mis à jour - {$effectiveType}: {$normalizedMontant} FCFA (avant: {$soldeAvant}, après: {$caisse->solde_actuel})");

        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour du solde de la caisse #{$caisseId}: " . $e->getMessage());
        }
    }

    /**
     * Enregistrer un mouvement de caisse pour le suivi
     */
    private function enregistrerMouvement(Caisse $caisse, $montant, $type, $soldeAvant, $depense = null): void
    {
        if (!class_exists(MouvementCaisse::class)) {
            return;
        }

        $depenseId = $depense ? $depense->id : null;
        $depenseLibelle = $depense ? $depense->libelle : 'Ajustement automatique';

        $description = $type === 'retrait'
            ? "Décaissement: {$depenseLibelle}"
            : "Remboursement: {$depenseLibelle}";

        $payload = MouvementCaisse::normalizePayload([
            'caisse_id' => $caisse->id,
            'type_mouvement' => $type === 'retrait' ? MouvementCaisse::TYPE_DEPENSE : MouvementCaisse::TYPE_REMBOURSEMENT,
            'montant' => $type === 'retrait' ? -abs($montant) : abs($montant),
            'description' => $description,
            'libelle' => $type === 'retrait' ? 'Décaissement' : 'Remboursement décaissement',
            'notes' => "Ajustement solde caisse (avant: {$soldeAvant}, apres: {$caisse->solde_actuel})",
            'date_mouvement' => now(),
            'created_by' => Auth::id() ?? ($depense->created_by ?? $depense->createur_id ?? null),
        ], $depense);

        // Évite les doublons de mouvement pour le même décaissement et même type.
        $alreadyExists = MouvementCaisse::query()
            ->where('source_type', DepenseCaisse::class)
            ->where('source_id', $depenseId)
            ->where('type_mouvement', $payload['type_mouvement'] ?? null)
            ->when(isset($payload['description']), function ($query) use ($payload) {
                $query->where('description', $payload['description']);
            })
            ->exists();

        if (!$alreadyExists) {
            MouvementCaisse::create($payload);
        }
    }

    private function isValidatedStatus($statut): bool
    {
        $normalized = strtolower(trim((string) $statut));

        return in_array($normalized, ['validé', 'valide', 'validated', 'valid'], true);
    }

}
