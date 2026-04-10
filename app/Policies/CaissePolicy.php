<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Caisse;

class CaissePolicy extends BasePolicy
{
    protected $model = Caisse::class;

    public function view(User $user, $caisse)
    {
        // Un utilisateur peut voir une caisse s'il en est le responsable
        // ou s'il a la permission de voir toutes les caisses
        return $user->id === $caisse->responsable_id ||
               $user->can('view_caisse');
    }

    public function update(User $user, $caisse)
    {
        // Seul le responsable de la caisse ou un admin peut la modifier
        return $user->id === $caisse->responsable_id ||
               $user->can('update_caisse');
    }

    public function delete(User $user, $caisse)
    {
        // Empêche la suppression d'une caisse avec des mouvements
        if ($caisse->mouvements()->exists()) {
            return false;
        }

        return $user->can('delete_caisse');
    }

    public function viewSolde(User $user, Caisse $caisse)
    {
        // Seul le responsable de la caisse, un comptable ou un admin peut voir le solde
        return $user->id === $caisse->responsable_id ||
               $user->hasRole(['comptable', 'admin']) ||
               $user->can('view_caisse_solde');
    }

    public function effectuerOperation(User $user, Caisse $caisse)
    {
        // Vérifie si l'utilisateur peut effectuer des opérations sur cette caisse
        return $user->id === $caisse->responsable_id ||
               $user->can('effectuer_operation_caisse');
    }
}
