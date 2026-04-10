<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BasePolicy
{
    use HandlesAuthorization;

    protected $model;

    public function before(User $user, $ability)
    {
        // Superadmin : accès total (avec ou sans tiret)
        if ($user->hasRole('superadmin') || $user->hasRole('super-admin')) {
            return true;
        }

        // Admin : accès total à tous les modules
        if ($user->hasRole('admin')) {
            return true;
        }

        // Modérateur : accès si le module parent est coché dans ses permissions
        if ($user->hasRole('moderator') || $user->hasRole('moderateur')) {
            $module = $this->getModuleForPolicy();
            if ($module && $user->canAccessModule($module)) {
                return true;
            }
        }
    }

    public function viewAny(User $user)
    {
        return $user->can('view_any_' . $this->getModelName());
    }

    public function view(User $user, $model)
    {
        return $user->can('view_' . $this->getModelName());
    }

    public function create(User $user)
    {
        return $user->can('create_' . $this->getModelName());
    }

    public function update(User $user, $model)
    {
        return $user->can('update_' . $this->getModelName());
    }

    public function delete(User $user, $model)
    {
        return $user->can('delete_' . $this->getModelName());
    }

    public function restore(User $user, $model)
    {
        return $user->can('restore_' . $this->getModelName());
    }

    public function forceDelete(User $user, $model)
    {
        return $user->can('force_delete_' . $this->getModelName());
    }

    protected function getModelName()
    {
        if (property_exists($this, 'model')) {
            return strtolower(class_basename($this->model));
        }
        return strtolower(str_replace('Policy', '', class_basename($this)));
    }

    /**
     * Mapper la Policy vers le nom du module parent pour vérifier l'accès modérateur.
     * Ex: CaissePolicy → 'tresorerie', ApprovisionnementCaissePolicy → 'tresorerie'
     */
    protected function getModuleForPolicy()
    {
        $policyToModule = [
            'caisse' => 'tresorerie',
            'approvisionnementcaisse' => 'tresorerie',
            'depensecaisse' => 'tresorerie',
            'virement' => 'tresorerie',
            'comptebancaire' => 'tresorerie',
            'banque' => 'tresorerie',
            'expense' => 'tresorerie',
            'vehicule' => 'materiel',
            'vehicle' => 'materiel',
            'maintenance' => 'materiel',
            'carburant' => 'materiel',
            'employe' => 'rh',
            'employee' => 'rh',
            'conge' => 'rh',
            'paie' => 'rh',
            'fournisseur' => 'fournisseurs',
            'supplier' => 'fournisseurs',
            'warehouse' => 'magasin',
            'entrepot' => 'entrepots',
            'product' => 'magasin',
            'client' => 'commercial',
            'contrat' => 'commercial',
            'devis' => 'commercial',
            'facture' => 'commercial',
            'invoice' => 'commercial',
            'ecriture' => 'comptabilite',
            'journal' => 'comptabilite',
            'projet' => 'projects',
            'project' => 'projects',
        ];

        $modelName = $this->getModelName();
        return $policyToModule[$modelName] ?? null;
    }
}
