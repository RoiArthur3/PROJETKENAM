<?php

namespace App\Listeners;

use App\Events\JuridiqueContratCreated;
use App\Events\JuridiqueContratUpdated;
use App\Events\FinancingDossierCreated;
use App\Services\JuridiqueService;
use Illuminate\Support\Facades\Log;

class JuridiqueEventListener
{
    protected $juridiqueService;

    public function __construct(JuridiqueService $juridiqueService)
    {
        $this->juridiqueService = $juridiqueService;
    }

    /**
     * Quand un contrat juridique est créé
     */
    public function handleContratCreated(JuridiqueContratCreated $event)
    {
        $contrat = $event->contrat;

        Log::info('Contrat juridique créé', [
            'contrat_id' => $contrat->id,
            'reference' => $contrat->reference,
            'user_id' => $contrat->created_by,
        ]);

        // Vérifier si le contrat nécessite un suivi particulier
        if ($contrat->type_contrat === 'travail') {
            $this->verifierConformiteRH($contrat);
        }

        // Si le contrat a un montant important, créer une alerte
        if ($contrat->montant && $contrat->montant > 1000000) {
            $this->creerAlerteMontantElevé($contrat);
        }
    }

    /**
     * Quand un contrat est mis à jour
     */
    public function handleContratUpdated(JuridiqueContratUpdated $event)
    {
        $contrat = $event->contrat;
        $changes = $event->changes;

        Log::info('Contrat juridique mis à jour', [
            'contrat_id' => $contrat->id,
            'changes' => $changes,
            'user_id' => auth()->id(),
        ]);

        // Vérifier les changements de statut importants
        if (isset($changes['statut'])) {
            $this->gererChangementStatut($contrat, $changes['statut']);
        }
    }

    /**
     * Quand un dossier de financement est créé
     */
    public function handleFinancingDossierCreated(FinancingDossierCreated $event)
    {
        $dossier = $event->dossier;

        Log::info('Dossier de financement créé', [
            'dossier_id' => $dossier->id,
            'montant_demande' => $dossier->montant_demande,
            'contrat_id' => $dossier->contrat_id,
        ]);

        // Si le dossier est lié à un contrat, mettre à jour les statistiques
        if ($dossier->contrat_id) {
            $this->mettreAJourStatistiquesContrat($dossier->contrat_id);
        }
    }

    /**
     * Vérifier la conformité avec le module RH
     */
    private function verifierConformiteRH($contrat)
    {
        // Logique pour vérifier la conformité RH
        Log::info('Vérification conformité RH pour contrat', [
            'contrat_id' => $contrat->id,
            'partie_contractante' => $contrat->partie_contractante,
        ]);
    }

    /**
     * Créer une alerte pour les montants élevés
     */
    private function creerAlerteMontantElevé($contrat)
    {
        Log::warning('Alerte: Contrat avec montant élevé', [
            'contrat_id' => $contrat->id,
            'montant' => $contrat->montant,
            'devise' => $contrat->devise,
        ]);
    }

    /**
     * Gérer les changements de statut
     */
    private function gererChangementStatut($contrat, $nouveauStatut)
    {
        if ($nouveauStatut === 'actif') {
            Log::info('Contrat activé', [
                'contrat_id' => $contrat->id,
                'date_debut' => $contrat->date_debut,
            ]);
        } elseif ($nouveauStatut === 'resilie') {
            Log::warning('Contrat résilié', [
                'contrat_id' => $contrat->id,
                'date_fin' => $contrat->date_fin,
            ]);
        }
    }

    /**
     * Mettre à jour les statistiques du contrat
     */
    private function mettreAJourStatistiquesContrat($contratId)
    {
        // Mettre à jour les statistiques ou le cache
        Log::info('Statistiques contrat mises à jour', [
            'contrat_id' => $contratId,
        ]);
    }
}
