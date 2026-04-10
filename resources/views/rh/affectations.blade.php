@extends('layouts.app')

@section('title', 'RH - Affectations | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-truck mr-2 text-primary"></i>Gestion des Affectations
            </h1>
            <p class="text-muted">Suivi des affectations véhicules et missions du personnel</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('rh.affectations.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>Nouvelle Affectation
                </a>
                <button class="btn btn-outline-secondary">
                    <i class="fas fa-calendar-alt mr-1"></i>Planning
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
                                Véhicules Actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalVehicules ?? 0 }}</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">{{ $vehiculesAffectes ?? 0 }} affectés</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
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
                                Missions en Cours</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $missionsEnCours ?? 0 }}</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">{{ $missionsAujourdhui ?? 0 }} aujourd'hui</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-route fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Véhicules Disponibles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $vehiculesDispos ?? 0 }}</div>
                            <div class="text-xs text-muted mt-1"><span class="text-warning">Prêts à l'emploi</span></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tachometer-alt fa-2x text-gray-300"></i>
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
                                Taux d'Occupation</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ ($totalVehicules ?? 0) > 0 ? round((($vehiculesAffectes ?? 0) / max(1, $totalVehicules)) * 100) : 0 }}%</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-info">Flotte utilisée</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- État actuel des affectations -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-truck mr-2"></i>État des Véhicules
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="text-center">
                                <div class="h4 mb-0 text-success">15</div>
                                <div class="text-muted small">Affectés</div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 83%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center">
                                <div class="h4 mb-0 text-warning">3</div>
                                <div class="text-muted small">Disponibles</div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: 17%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-truck text-success mr-2"></i>
                                <span class="small">Véhicules de service</span>
                            </div>
                            <span class="badge bg-success">12</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-wrench text-warning mr-2"></i>
                                <span class="small">En maintenance</span>
                            </div>
                            <span class="badge bg-warning">2</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-ban text-danger mr-2"></i>
                                <span class="small">Hors service</span>
                            </div>
                            <span class="badge bg-danger">1</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-route mr-2"></i>Missions du Jour
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Livraison Paris</h6>
                                <p class="timeline-text text-muted">
                                    <i class="fas fa-user mr-1"></i>Jean Dupont<br>
                                    <i class="fas fa-truck mr-1"></i>Véhicule VF-001<br>
                                    <i class="fas fa-clock mr-1"></i>08:00 - 17:00
                                </p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Maintenance Site Lyon</h6>
                                <p class="timeline-text text-muted">
                                    <i class="fas fa-user mr-1"></i>Marie Curie<br>
                                    <i class="fas fa-truck mr-1"></i>Véhicule VF-003<br>
                                    <i class="fas fa-clock mr-1"></i>09:00 - 16:00
                                </p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Prospection Marseille</h6>
                                <p class="timeline-text text-muted">
                                    <i class="fas fa-user mr-1"></i>Pierre Louis<br>
                                    <i class="fas fa-truck mr-1"></i>Véhicule VF-005<br>
                                    <i class="fas fa-clock mr-1"></i>10:00 - 18:00
                                </p>
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
                    <label class="form-label">Véhicule</label>
                    <select class="form-select">
                        <option value="">Tous</option>
                        <option>VF-001</option>
                        <option>VF-002</option>
                        <option>VF-003</option>
                        <option>VF-004</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select class="form-select">
                        <option value="">Tous</option>
                        <option>En cours</option>
                        <option>Terminée</option>
                        <option>Planifiée</option>
                        <option>Annulée</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" value="2025-01-15">
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

    <!-- Liste des affectations -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>Affectations et Missions (24)
            </h6>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary">
                    <i class="fas fa-download mr-1"></i>Exporter
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="affectationsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Agent</th>
                            <th>Véhicule</th>
                            <th>Mission</th>
                            <th>Date Début</th>
                            <th>Date Fin</th>
                            <th>Destination</th>
                            <th>Kilométrage</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($affectations ?? []) as $a)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white mr-3">{{ strtoupper(substr(optional($a->agent)->name ?? 'NA', 0, 1)) }}</div>
                                        <div>
                                            <div class="font-weight-bold">{{ optional($a->agent)->name ?? '—' }}</div>
                                            <div class="text-muted small">{{ optional($a->agent)->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ optional($a->vehicule)->immatriculation ?? '—' }}</span>
                                </td>
                                <td>{{ $a->mission ?? '—' }}</td>
                                <td>{{ optional($a->date_debut)->format('d/m/Y') ?? ( $a->date_debut ? \Carbon\Carbon::parse($a->date_debut)->format('d/m/Y') : '—') }}</td>
                                <td>{{ optional($a->date_fin)->format('d/m/Y') ?? ( $a->date_fin ? \Carbon\Carbon::parse($a->date_fin)->format('d/m/Y') : '—') }}</td>
                                <td>—</td>
                                <td>—</td>
                                <td>
                                    @php
                                        $map = ['En cours' => 'success', 'Planifiée' => 'warning', 'Terminée' => 'secondary', 'Annulée' => 'danger'];
                                        $color = $map[$a->statut ?? ''] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $a->statut ?? '—' }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="Détails"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-outline-warning" title="Modifier"><i class="fas fa-edit"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Aucune affectation</td>
                            </tr>
                        @endforelse
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

.table-success {
    background-color: rgba(28, 200, 138, 0.1) !important;
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
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 3px solid #fff;
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
    line-height: 1.4;
}
</style>

<!-- Scripts -->
<script>
$(document).ready(function() {
    // Initialisation de DataTables
    $('#affectationsTable').DataTable({
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
        }
    });
});
</script>
@endsection
