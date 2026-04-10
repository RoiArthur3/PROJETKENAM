@extends('layouts.app')

@section('title', 'Contrats Fournisseurs | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-contract mr-2 text-primary"></i>Contrats Fournisseurs
            </h1>
            <p class="text-muted">Gestion et suivi des contrats avec les fournisseurs</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i>Nouveau Contrat
            </button>
        </div>
    </div>

    <!-- Statistiques des contrats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Contrats</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">89</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">+7 ce trimestre</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Contrats Actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">76</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">85% taux d'activité</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Expiration < 90j</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-warning">Nécessitent attention</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Valeur Totale</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">2.8M FCFA</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-info">Contrats actifs</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Filtres
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select class="form-select">
                        <option value="">Tous les statuts</option>
                        <option>Actif</option>
                        <option>En cours de négociation</option>
                        <option>Expiré</option>
                        <option>Résilié</option>
                        <option>Suspendu</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type de contrat</label>
                    <select class="form-select">
                        <option value="">Tous types</option>
                        <option>Fourniture</option>
                        <option>Service</option>
                        <option>Maintenance</option>
                        <option>Transport</option>
                        <option>Consulting</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Échéance</label>
                    <select class="form-select">
                        <option value="">Toutes échéances</option>
                        <option>Ce mois</option>
                        <option>Ce trimestre</option>
                        <option>Dans 3 mois</option>
                        <option>Dans 6 mois</option>
                        <option>Plus de 6 mois</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fournisseur</label>
                    <select class="form-select">
                        <option value="">Tous fournisseurs</option>
                        <option>TechnoPlus SA</option>
                        <option>Logistics Pro</option>
                        <option>Energy Solutions</option>
                        <option>Maintenance Plus</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des contrats -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>Contrats Fournisseurs (89)
            </h6>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-success">
                    <i class="fas fa-file-excel mr-1"></i>Export Excel
                </button>
                <button class="btn btn-outline-info">
                    <i class="fas fa-print mr-1"></i>Imprimer
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="contratsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Fournisseur</th>
                            <th>Type</th>
                            <th>Date Début</th>
                            <th>Date Fin</th>
                            <th>Valeur</th>
                            <th>Statut</th>
                            <th>Jours Restants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span class="badge bg-primary">CTR-2025-001</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">TechnoPlus SA</div>
                                <div class="text-muted small">Maintenance équipements</div>
                            </td>
                            <td>
                                <span class="badge bg-info">Maintenance</span>
                            </td>
                            <td>2025-01-01</td>
                            <td>2025-12-31</td>
                            <td class="font-weight-bold">850 000 FCFA</td>
                            <td>
                                <span class="badge bg-success">Actif</span>
                            </td>
                            <td>
                                <span class="text-success">245 jours</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Renouveler">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-warning">
                            <td>
                                <span class="badge bg-primary">CTR-2025-002</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">Logistics Pro</div>
                                <div class="text-muted small">Transport marchandises</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">Transport</span>
                            </td>
                            <td>2024-06-01</td>
                            <td>2025-05-31</td>
                            <td class="font-weight-bold">1 200 000 FCFA</td>
                            <td>
                                <span class="badge bg-warning">Expiration proche</span>
                            </td>
                            <td>
                                <span class="text-warning">42 jours</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Renégocier">
                                        <i class="fas fa-handshake"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Alerte">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="badge bg-primary">CTR-2025-003</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">Energy Solutions</div>
                                <div class="text-muted small">Fourniture électricité</div>
                            </td>
                            <td>
                                <span class="badge bg-success">Fourniture</span>
                            </td>
                            <td>2025-01-15</td>
                            <td>2026-01-14</td>
                            <td class="font-weight-bold">450 000 FCFA</td>
                            <td>
                                <span class="badge bg-success">Actif</span>
                            </td>
                            <td>
                                <span class="text-success">311 jours</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Paiements">
                                        <i class="fas fa-money-bill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-danger">
                            <td>
                                <span class="badge bg-primary">CTR-2024-045</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">Fournitures Express</div>
                                <div class="text-muted small">Matériel bureautique</div>
                            </td>
                            <td>
                                <span class="badge bg-success">Fourniture</span>
                            </td>
                            <td>2024-01-01</td>
                            <td>2024-12-31</td>
                            <td class="font-weight-bold">180 000 FCFA</td>
                            <td>
                                <span class="badge bg-danger">Expiré</span>
                            </td>
                            <td>
                                <span class="text-danger">Expiré</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Renouveler">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Archiver">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Précédent</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Suivant</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Alertes d'expiration -->
    <div class="row mt-4">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Alertes d'Expiration
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-clock mr-2"></i>
                        <strong>12 contrats</strong> expirent dans moins de 90 jours. <a href="#" class="alert-link">Voir la liste complète</a>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Contrats expirant ce mois</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    CTR-2025-002 - Logistics Pro
                                    <span class="badge bg-warning">42 jours</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    CTR-2025-015 - Global Transport
                                    <span class="badge bg-warning">28 jours</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    CTR-2025-023 - Office Plus
                                    <span class="badge bg-warning">15 jours</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Actions recommandées</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary">
                                    <i class="fas fa-envelope mr-2"></i>Envoyer rappels automatiques
                                </button>
                                <button class="btn btn-outline-success">
                                    <i class="fas fa-calendar mr-2"></i>Planifier renouvellements
                                </button>
                                <button class="btn btn-outline-info">
                                    <i class="fas fa-file-contract mr-2"></i>Générer rapports d'expiration
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    // Initialisation de DataTables
    $('#contratsTable').DataTable({
        "pageLength": 10,
        "language": {
            "search": "Rechercher:",
            "lengthMenu": "Afficher _MENU_ éléments par page",
            "zeroRecords": "Aucun résultat trouvé",
            "info": "Page _PAGE_ sur _PAGES_",
            "infoEmpty": "Aucun élément disponible",
            "infoFiltered": "(filtré sur _MAX_ éléments au total)",
            "paginate": {
                "first": "Premier",
                "last": "Dernier",
                "next": "Suivant",
                "previous": "Précédent"
            }
        },
        "order": [[ 7, "asc" ]] // Trier par jours restants
    });

    // Animation des alertes
    $('.alert').hide().fadeIn(1000);
});
</script>

<style>
.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.table-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}
</style>
@endsection
