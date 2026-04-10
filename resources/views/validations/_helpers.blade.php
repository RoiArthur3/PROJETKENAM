@php
/**
 * Fonctions d'aide pour les validations
 * Préfixées par 'op' pour éviter les conflits globaux
 */

if (!function_exists('getOpStatusColor')) {
    function getOpStatusColor($status) {
        $status = strtolower($status ?? '');
        switch($status) {
            case 'en_attente':
            case 'pending_validation':
            case 'en_validation':
            case 'brouillon':
                return 'warning';
            case 'en_cours':
                return 'info';
            case 'termine':
            case 'approuvee':
            case 'approved':
            case 'approuve':
                return 'success';
            case 'rejetee':
            case 'rejected':
            case 'rejete':
                return 'danger';
            default: return 'secondary';
        }
    }
}

if (!function_exists('getOpStatusIcon')) {
    function getOpStatusIcon($status) {
        $status = strtolower($status ?? '');
        switch($status) {
            case 'en_attente':
            case 'pending_validation':
            case 'en_validation':
            case 'brouillon':
                return 'clock';
            case 'en_cours':
                return 'spinner';
            case 'termine':
            case 'approuvee':
            case 'approved':
            case 'approuve':
                return 'check';
            case 'rejetee':
            case 'rejected':
            case 'rejete':
                return 'times';
            default: return 'question';
        }
    }
}

if (!function_exists('getOpStatusText')) {
    function getOpStatusText($status) {
        $textes = [
            'en_attente' => 'En attente',
            'pending_validation' => 'En attente',
            'en_validation' => 'En validation',
            'brouillon' => 'Brouillon',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'approuvee' => 'Approuvée',
            'approved' => 'Approuvée',
            'approuve' => 'Approuvé',
            'rejetee' => 'Rejetée',
            'rejected' => 'Rejetée',
            'rejete' => 'Rejeté',
        ];
        $status = strtolower($status ?? '');
        return $textes[$status] ?? 'Inconnu';
    }
}

if (!function_exists('getOpPriorityColor')) {
    function getOpPriorityColor($priorite) {
        $priorite = strtolower($priorite ?? '');
        switch($priorite) {
            case 'urgente': return 'danger';
            case 'haute': return 'warning';
            case 'moyenne': return 'info';
            case 'basse': return 'secondary';
            default: return 'secondary';
        }
    }
}
@endphp
