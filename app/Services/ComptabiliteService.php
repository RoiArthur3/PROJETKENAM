<?php

namespace App\Services;

use App\Models\ApprovisionnementCaisse;
use App\Models\DepenseCaisse;
use App\Models\EcritureComptable;
use App\Models\LigneEcritureComptable;
use Illuminate\Support\Facades\DB;

class ComptabiliteService
{
    public function genererEcritureApprovisionnement(ApprovisionnementCaisse $approvisionnement)
    {
        $libelle = sprintf(
            "Approvisionnement de caisse N°%s - %s",
            $approvisionnement->numero_operation,
            $approvisionnement->motif
        );

        // Numéro de pièce unique
        $numeroPiece = 'APP-' . $approvisionnement->id . '-' . now()->format('Ymd');

        // Créer l'écriture comptable
        $ecriture = EcritureComptable::create([
            'numero_piece' => $numeroPiece,
            'date_ecriture' => now(),
            'libelle' => $libelle,
            'montant_total' => $approvisionnement->montant,
            'validee' => true,
            'date_validation' => now(),
            'validateur_id' => auth()->id(),
            'reference_type' => get_class($approvisionnement),
            'reference_id' => $approvisionnement->id,
        ]);

        // Ligne au débit (caisse secondaire - 572)
        LigneEcritureComptable::create([
            'ecriture_comptable_id' => $ecriture->id,
            'compte_comptable_id' => $this->getCompteCaisseSecondaire()->id,
            'libelle' => $libelle,
            'montant' => $approvisionnement->montant,
            'sens' => 'debit',
        ]);

        // Ligne au crédit (caisse principale - 571)
        LigneEcritureComptable::create([
            'ecriture_comptable_id' => $ecriture->id,
            'compte_comptable_id' => $this->getCompteCaissePrincipale()->id,
            'libelle' => $libelle,
            'montant' => $approvisionnement->montant,
            'sens' => 'credit',
        ]);

        return $ecriture;
    }

    public function genererEcritureDepense(DepenseCaisse $depense)
    {
        $libelle = sprintf(
            "Dépense de caisse N°%s - %s",
            $depense->approvisionnement->numero_operation,
            $depense->libelle
        );

        // Numéro de pièce unique
        $numeroPiece = 'DEP-' . $depense->id . '-' . now()->format('Ymd');

        // Créer l'écriture comptable
        $ecriture = EcritureComptable::create([
            'numero_piece' => $numeroPiece,
            'date_ecriture' => $depense->date_depense,
            'libelle' => $libelle,
            'montant_total' => $depense->montant,
            'validee' => true,
            'date_validation' => now(),
            'validateur_id' => auth()->id(),
            'reference_type' => get_class($depense),
            'reference_id' => $depense->id,
        ]);

        // Ligne au débit (compte de charge)
        LigneEcritureComptable::create([
            'ecriture_comptable_id' => $ecriture->id,
            'compte_comptable_id' => $depense->compte_comptable_id,
            'libelle' => $libelle,
            'montant' => $depense->montant,
            'sens' => 'debit',
        ]);

        // Ligne au crédit (caisse secondaire - 572)
        LigneEcritureComptable::create([
            'ecriture_comptable_id' => $ecriture->id,
            'compte_comptable_id' => $this->getCompteCaisseSecondaire()->id,
            'libelle' => $libelle,
            'montant' => $depense->montant,
            'sens' => 'credit',
        ]);

        return $ecriture;
    }

    public function genererEcritureRemboursement(ApprovisionnementCaisse $approvisionnement)
    {
        $montantRemboursement = $approvisionnement->solde_restant;

        if ($montantRemboursement <= 0) {
            return null;
        }

        $libelle = sprintf(
            "Remboursement approvisionnement N°%s - Solde restant",
            $approvisionnement->numero_operation
        );

        // Numéro de pièce unique
        $numeroPiece = 'REM-' . $approvisionnement->id . '-' . now()->format('Ymd');

        // Créer l'écriture comptable
        $ecriture = EcritureComptable::create([
            'numero_piece' => $numeroPiece,
            'date_ecriture' => now(),
            'libelle' => $libelle,
            'montant_total' => $montantRemboursement,
            'validee' => true,
            'date_validation' => now(),
            'validateur_id' => auth()->id(),
            'reference_type' => get_class($approvisionnement),
            'reference_id' => $approvisionnement->id,
        ]);

        // Ligne au débit (caisse principale - 571)
        LigneEcritureComptable::create([
            'ecriture_comptable_id' => $ecriture->id,
            'compte_comptable_id' => $this->getCompteCaissePrincipale()->id,
            'libelle' => $libelle,
            'montant' => $montantRemboursement,
            'sens' => 'debit',
        ]);

        // Ligne au crédit (caisse secondaire - 572)
        LigneEcritureComptable::create([
            'ecriture_comptable_id' => $ecriture->id,
            'compte_comptable_id' => $this->getCompteCaisseSecondaire()->id,
            'libelle' => $libelle,
            'montant' => $montantRemboursement,
            'sens' => 'credit',
        ]);

        return $ecriture;
    }

    protected function getCompteCaissePrincipale()
    {
        return \App\Models\CompteComptable::where('numero', '571')->firstOrFail();
    }

    protected function getCompteCaisseSecondaire()
    {
        return \App\Models\CompteComptable::where('numero', '572')->firstOrFail();
    }
}
