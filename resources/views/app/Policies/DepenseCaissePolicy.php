<?php

namespace App\Policies;

use App\Models\ApprovisionnementCaisse;
use App\Models\DepenseCaisse;
use App\Models\Justificatif;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepenseCaissePolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->hasRole('superadmin') || $user->hasRole('super-admin') || $user->hasRole('admin')) {
            return true;
        }

        if (($user->hasRole('moderator') || $user->hasRole('moderateur')) && $user->canAccessModule('tresorerie')) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->can('view_depenses');
    }

    public function view(User $user, DepenseCaisse $depense)
    {
        return $user->can('view_depenses')
            && $this->userHasAccessToApprovisionnement($user, $depense->approvisionnement);
    }

    public function create(User $user, ApprovisionnementCaisse $approvisionnement = null)
    {
        if (!$user->can('create_depenses')) {
            return false;
        }

        // Si on a un approvisionnement spécifique, on vérifie les droits dessus
        if ($approvisionnement) {
            return $this->userHasAccessToApprovisionnement($user, $approvisionnement)
                && $approvisionnement->statut === 'decaisse';
        }

        return true;
    }

    public function update(User $user, DepenseCaisse $depense)
    {
        return $user->can('edit_depenses')
            && $this->userHasAccessToApprovisionnement($user, $depense->approvisionnement)
            && $depense->approvisionnement->statut === 'decaisse';
    }

    public function delete(User $user, DepenseCaisse $depense)
    {
        return $user->can('delete_depenses')
            && $this->userHasAccessToApprovisionnement($user, $depense->approvisionnement)
            && $depense->approvisionnement->statut === 'decaisse';
    }

    public function justifier(User $user, DepenseCaisse $depense)
    {
        return $this->userHasAccessToApprovisionnement($user, $depense->approvisionnement)
            && $depense->approvisionnement->statut === 'decaisse'
            && !$depense->est_justifie
            && (
                $user->id === $depense->created_by
                || $user->can('valider_justificatifs')
            );
    }

    public function valider(User $user, Justificatif $justificatif)
    {
        return $user->can('valider_justificatifs')
            && $this->userHasAccessToApprovisionnement($user, $justificatif->depense->approvisionnement)
            && !$justificatif->valide_par;
    }

    protected function userHasAccessToApprovisionnement(User $user, ApprovisionnementCaisse $approvisionnement): bool
    {
        // L'utilisateur a accès s'il est le demandeur, le validateur ou s'il a un rôle admin/comptabilité
        return $user->hasRole(['admin', 'comptabilite'])
            || $user->id === $approvisionnement->demandeur_id
            || $user->id === $approvisionnement->validateur_id
            || $user->id === $approvisionnement->caisse_destination->responsable_id
            || $user->id === $approvisionnement->caisse_source->responsable_id;
    }
}
