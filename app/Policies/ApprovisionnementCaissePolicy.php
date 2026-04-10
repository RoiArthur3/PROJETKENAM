<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ApprovisionnementCaisse;

class ApprovisionnementCaissePolicy extends BasePolicy
{
    protected $model = ApprovisionnementCaisse::class;

    /**
     * Droit de valider un approvisionnement
     */
    public function valider(User $user, ApprovisionnementCaisse $approvisionnement)
    {
        // Seul un utilisateur avec le rôle trésorerie peut valider
        // et seulement si l'approvisionnement est en attente
        return $user->hasRole('tresorerie') &&
               $approvisionnement->statut === 'en_attente';
    }

    /**
     * Droit de rejeter un approvisionnement
     */
    public function rejeter(User $user, ApprovisionnementCaisse $approvisionnement)
    {
        // Mêmes droits que pour la validation
        return $this->valider($user, $approvisionnement);
    }

    /**
     * Droit de voir l'historique d'une caisse
     */
    public function viewHistorique(User $user, ApprovisionnementCaisse $approvisionnement)
    {
        // L'utilisateur peut voir l'historique s'il a accès à la caisse source ou destination
        return $user->can('view', $approvisionnement->source) ||
               $user->can('view', $approvisionnement->destination);
    }
}
