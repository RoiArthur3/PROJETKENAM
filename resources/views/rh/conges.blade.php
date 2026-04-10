@extends('layouts.app')

@section('title', 'RH - Congés | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-calendar-times mr-2 text-primary"></i>Gestion des Congés
            </h1>
            <p class="text-muted">Planification et suivi des congés et absences du personnel</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('rh.conges.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>Demander un congé
                </a>
                <button class="btn btn-outline-secondary">
                    <i class="fas fa-calendar-alt mr-1"></i>Calendrier
                </button>
            </div>
        </div>
    </div>

    <!-- Cartes de synthèse -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Congés en Cours</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">8</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-primary">3 demandes en attente</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-plane fa-2x text-gray-300"></i>
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
                                Congés Approuvés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">156</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">Ce mois-ci</span>
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
                                En Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-warning">Validation requise</span>
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
                                Solde Moyen</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">18j</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-info">Par employé</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendrier et demandes récentes -->
    <div class="row mb-4">
        <!-- Calendrier des congés -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-alt mr-2"></i>Calendrier des Congés - Février 2025
                    </h6>
                </div>
                <div class="card-body">
                    <div class="calendar-container">
                        <div class="calendar-header d-flex justify-content-between align-items-center mb-3">
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <h5 class="mb-0">Février 2025</h5>
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="calendar-grid">
                            <div class="calendar-day-header">Lun</div>
                            <div class="calendar-day-header">Mar</div>
                            <div class="calendar-day-header">Mer</div>
                            <div class="calendar-day-header">Jeu</div>
                            <div class="calendar-day-header">Ven</div>
                            <div class="calendar-day-header">Sam</div>
                            <div class="calendar-day-header">Dim</div>

                            <!-- Jours du mois -->
                            <div class="calendar-day disabled">27</div>
                            <div class="calendar-day disabled">28</div>
                            <div class="calendar-day disabled">29</div>
                            <div class="calendar-day disabled">30</div>
                            <div class="calendar-day disabled">31</div>
                            <div class="calendar-day">1</div>
                            <div class="calendar-day">2</div>

                            <div class="calendar-day">3</div>
                            <div class="calendar-day">4</div>
                            <div class="calendar-day">5</div>
                            <div class="calendar-day">6</div>
                            <div class="calendar-day">7</div>
                            <div class="calendar-day weekend">8</div>
                            <div class="calendar-day weekend">9</div>

                            <div class="calendar-day">10</div>
                            <div class="calendar-day">11</div>
                            <div class="calendar-day holiday" title="Lundi de Pâques">12</div>
                            <div class="calendar-day">13</div>
                            <div class="calendar-day">14</div>
                            <div class="calendar-day weekend">15</div>
                            <div class="calendar-day weekend">16</div>

                            <div class="calendar-day">17</div>
                            <div class="calendar-day leave-approved" title="Marie Curie - 3 jours">
                                <span>18</span>
                                <small class="leave-indicator bg-success">MC</small>
                            </div>
                            <div class="calendar-day leave-approved">
                                <span>19</span>
                                <small class="leave-indicator bg-success">MC</small>
                            </div>
                            <div class="calendar-day leave-approved">
                                <span>20</span>
                                <small class="leave-indicator bg-success">MC</small>
                            </div>
                            <div class="calendar-day">21</div>
                            <div class="calendar-day weekend">22</div>
                            <div class="calendar-day weekend">23</div>

                            <div class="calendar-day">24</div>
                            <div class="calendar-day">25</div>
                            <div class="calendar-day">26</div>
                            <div class="calendar-day">27</div>
                            <div class="calendar-day">28</div>
                            <div class="calendar-day weekend">29</div>
                            <div class="calendar-day weekend">1</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Demandes récentes -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock mr-2"></i>Demandes Récentes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Pierre Louis</h6>
                                <p class="timeline-text text-muted">Congé annuel: 15-22 mars</p>
                                <small class="text-warning">En attente validation</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Sophie Martin</h6>
                                <p class="timeline-text text-muted">RTT: 10-11 février</p>
                                <small class="text-success">Approuvé</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Ahmed Benali</h6>
                                <p class="timeline-text text-muted">Maladie: 5-7 février</p>
                                <small class="text-danger">Refusé - justificatif requis</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Filtres et Recherche
            </h6>
        </div>
        <div class="card-body">
            <form class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Agent</label>
                    <select class="form-select">
                        <option value="">Tous les agents</option>
                        <option>Jean Dupont</option>
                        <option>Marie Curie</option>
                        <option>Pierre Louis</option>
                        <option>Sophie Martin</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type de congé</label>
                    <select class="form-select">
                        <option value="">Tous</option>
                        <option>Congé annuel</option>
                        <option>RTT</option>
                        <option>Maladie</option>
                        <option>Maternité</option>
                        <option>Exceptionnel</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select class="form-select">
                        <option value="">Tous</option>
                        <option>Approuvé</option>
                        <option>En attente</option>
                        <option>Refusé</option>
                        <option>Annulé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Mois</label>
                    <select class="form-select">
                        <option value="2025-02">Février 2025</option>
                        <option value="2025-03">Mars 2025</option>
                        <option value="2025-04">Avril 2025</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i>Filtrer
                        </button>
                        <button type="button" class="btn btn-outline-secondary">
                            <i class="fas fa-times mr-1"></i>Réinitialiser
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des congés -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>Historique des Congés
            </h6>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary">
                    <i class="fas fa-download mr-1"></i>Exporter
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="congesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Agent</th>
                            <th>Type</th>
                            <th>Période</th>
                            <th>Durée</th>
                            <th>Date Demande</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white mr-3">MC</div>
                                    <div>
                                        <div class="font-weight-bold">Marie Curie</div>
                                        <div class="text-muted small">Entretien</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">Congé annuel</span>
                            </td>
                            <td>18 fév - 20 fév 2025</td>
                            <td><span class="font-weight-bold">3 jours</span></td>
                            <td>10 jan 2025</td>
                            <td>
                                <span class="badge bg-success">Approuvé</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success text-white mr-3">SM</div>
                                    <div>
                                        <div class="font-weight-bold">Sophie Martin</div>
                                        <div class="text-muted small">Administration</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">RTT</span>
                            </td>
                            <td>10 fév - 11 fév 2025</td>
                            <td><span class="font-weight-bold">2 jours</span></td>
                            <td>05 jan 2025</td>
                            <td>
                                <span class="badge bg-success">Approuvé</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-warning text-white mr-3">PL</div>
                                    <div>
                                        <div class="font-weight-bold">Pierre Louis</div>
                                        <div class="text-muted small">Commercial</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">Congé annuel</span>
                            </td>
                            <td>15 mar - 22 mar 2025</td>
                            <td><span class="font-weight-bold">6 jours</span></td>
                            <td>08 jan 2025</td>
                            <td>
                                <span class="badge bg-warning">En attente</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" title="Approuver">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Refuser">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-danger text-white mr-3">AB</div>
                                    <div>
                                        <div class="font-weight-bold">Ahmed Benali</div>
                                        <div class="text-muted small">Logistique</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-danger">Maladie</span>
                            </td>
                            <td>05 fév - 07 fév 2025</td>
                            <td><span class="font-weight-bold">3 jours</span></td>
                            <td>04 fév 2025</td>
                            <td>
                                <span class="badge bg-danger">Refusé</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Justificatif">
                                        <i class="fas fa-file-medical"></i>
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
</div>

<!-- Styles personnalisés -->
<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 11px;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.075);
}

.calendar-container {
    max-width: 100%;
}

.calendar-header {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 10px;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}

.calendar-day-header {
    text-align: center;
    font-weight: bold;
    padding: 10px 5px;
    background-color: #f8f9fc;
    border: 1px solid #dee2e6;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 5px;
    border: 1px solid #dee2e6;
    position: relative;
    min-height: 60px;
}

.calendar-day.disabled {
    background-color: #f8f9fc;
    color: #6c757d;
}

.calendar-day.weekend {
    background-color: #fff3cd;
}

.calendar-day.holiday {
    background-color: #f8d7da;
}

.calendar-day.leave-approved {
    background-color: #d4edda;
}

.leave-indicator {
    position: absolute;
    bottom: 2px;
    right: 2px;
    padding: 1px 4px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: bold;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 15px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 2px;
    color: #495057;
}

.timeline-text {
    font-size: 12px;
    margin: 0;
}
</style>

<!-- Scripts -->
<script>
$(document).ready(function() {
    // Initialisation de DataTables
    $('#congesTable').DataTable({
        "pageLength": 10,
        "autoWidth": false,  // Désactiver la largeur automatique pour éviter les problèmes de colonnes
        "columns": [
            null,  // Agent
            null,  // Type
            null,  // Période
            null,  // Durée
            null,  // Date Demande
            null,  // Statut
            null   // Actions
        ],
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
        }
    });
});
</script>
@endsection
