<?php

if (!function_exists('getOperationStatusColor')) {
    function getOperationStatusColor($statut) {
        $statut = strtolower($statut);
        switch($statut) {
            case 'pending_validation':
            case 'en_attente':
            case 'en_validation':
            case 'pending':
                return 'warning';
            case 'en_cours':
            case 'in_progress':
                return 'info';
            case 'termine':
            case 'terminee':
            case 'approuvee':
            case 'approuve':
            case 'valide':
            case 'approved':
                return 'success';
            case 'rejete':
            case 'rejetee':
            case 'rejected':
                return 'danger';
            case 'annulee':
            case 'annule':
                return 'dark';
            case 'brouillon':
                return 'secondary';
            default:
                return 'secondary';
        }
    }
}

if (!function_exists('getOperationStatusIcon')) {
    function getOperationStatusIcon($statut) {
        $statut = strtolower($statut);
        switch($statut) {
            case 'en_attente':
            case 'pending_validation':
            case 'en_validation':
            case 'pending':
                return 'clock';
            case 'en_cours':
            case 'in_progress':
                return 'spinner fa-spin';
            case 'termine':
            case 'terminee':
            case 'approuvee':
            case 'approuve':
            case 'valide':
            case 'approved':
                return 'check-circle';
            case 'rejete':
            case 'rejetee':
            case 'rejected':
                return 'times-circle';
            case 'annulee':
            case 'annule':
                return 'ban';
            case 'brouillon':
                return 'edit';
            default:
                return 'question-circle';
        }
    }
}

if (!function_exists('getOperationStatusText')) {
    function getOperationStatusText($statut) {
        // Préférer la traduction Laravel si disponible
        if (\Illuminate\Support\Facades\Lang::has('operations.traductions.' . $statut)) {
            return __('operations.traductions.' . $statut);
        }

        switch(strtolower($statut)) {
            case 'en_attente': return 'En attente';
            case 'pending_validation': return 'En attente de validation';
            case 'en_validation': return 'En validation';
            case 'en_cours':
            case 'in_progress': return 'En cours';
            case 'termine':
            case 'terminee': return 'Terminé';
            case 'approuvee':
            case 'approuve':
            case 'approved': return 'Approuvée';
            case 'rejete':
            case 'rejetee':
            case 'rejected': return 'Rejetée';
            case 'annulee':
            case 'annule': return 'Annulée';
            case 'brouillon': return 'Brouillon';
            default: return ucfirst($statut);
        }
    }
}

if (!function_exists('getPriorityColor')) {
    function getPriorityColor($priorite) {
        switch($priorite) {
            case 'urgente': return 'danger';
            case 'haute': return 'warning';
            case 'moyenne': return 'info';
            case 'basse': return 'secondary';
            default: return 'secondary';
        }
    }
}
