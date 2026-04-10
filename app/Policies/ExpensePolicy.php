<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpensePolicy
{
    public function before(User $user, $ability)
    {
        if ($user->hasRole('superadmin') || $user->hasRole('super-admin') || $user->hasRole('admin')) {
            return true;
        }

        if (($user->hasRole('moderator') || $user->hasRole('moderateur')) && $user->canAccessModule('tresorerie')) {
            return true;
        }
    }

    /**
     * Détermine si l'utilisateur peut voir n'importe quel modèle.
     */
    public function viewAny(User $user): bool
    {
        // Seuls les utilisateurs connectés peuvent voir les dépenses
        return $user !== null;
    }

    /**
     * Détermine si l'utilisateur peut voir une dépense spécifique.
     */
    public function view(User $user, Expense $expense): bool
    {
        // L'utilisateur peut voir la dépense s'il en est l'auteur
        // ou s'il a un rôle d'administrateur ou de responsable financier
        return $user->id === $expense->user_id ||
               $user->isAdmin() ||
               $user->hasRole('finances');
    }

    /**
     * Détermine si l'utilisateur peut créer des modèles.
     */
    public function create(User $user): bool
    {
        // Tous les utilisateurs authentifiés peuvent créer des dépenses
        return $user !== null;
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour une dépense.
     */
    public function update(User $user, Expense $expense): bool
    {
        // L'utilisateur peut mettre à jour la dépense s'il en est l'auteur
        // et qu'elle n'a pas encore été approuvée ou rejetée
        if ($user->id === $expense->user_id && $expense->statut === 'en_attente') {
            return true;
        }

        // Les administrateurs et responsables financiers peuvent modifier les dépenses
        return $user->isAdmin() || $user->hasRole('finances');
    }

    /**
     * Détermine si l'utilisateur peut supprimer une dépense.
     */
    public function delete(User $user, Expense $expense): bool
    {
        // L'utilisateur peut supprimer la dépense s'il en est l'auteur
        // et qu'elle n'a pas encore été approuvée
        if ($user->id === $expense->user_id && $expense->statut === 'en_attente') {
            return true;
        }

        // Les administrateurs peuvent supprimer n'importe quelle dépense
        return $user->isAdmin();
    }

    /**
     * Détermine si l'utilisateur peut approuver une dépense.
     */
    public function approve(User $user, Expense $expense): bool
    {
        // Seuls les administrateurs et responsables financiers peuvent approuver les dépenses
        // et seulement si la dépense est en attente
        return ($user->isAdmin() || $user->hasRole('finances')) &&
               $expense->statut === 'en_attente';
    }

    /**
     * Détermine si l'utilisateur peut rejeter une dépense.
     */
    public function reject(User $user, Expense $expense): bool
    {
        // Mêmes droits que pour l'approbation
        return $this->approve($user, $expense);
    }

    /**
     * Détermine si l'utilisateur peut restaurer une dépense supprimée.
     */
    public function restore(User $user, Expense $expense): bool
    {
        // Seuls les administrateurs peuvent restaurer les dépenses supprimées
        return $user->isAdmin();
    }

    /**
     * Détermine si l'utilisateur peut supprimer définitivement une dépense.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        // Seuls les administrateurs peuvent supprimer définitivement les dépenses
        return $user->isAdmin();
    }
}
