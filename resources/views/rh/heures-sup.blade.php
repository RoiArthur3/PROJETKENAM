@extends('layouts.app')

@section('title', 'RH - Heures Supplémentaires | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clock mr-2 text-primary"></i>Gestion des Heures Supplémentaires
            </h1>
            <p class="text-muted">Suivi, validation et paiement des heures supplémentaires</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a class="btn btn-primary" href="{{ route('rh.heures-sup.create') }}">
                    <i class="fas fa-plus mr-1"></i>Déclarer H.S.
                </a>
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#planningModal">
                    <i class="fas fa-calendar-alt mr-1"></i>Planning
                </button>
                <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#validationModal">
                    <i class="fas fa-check-double mr-1"></i>Validation
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
                                Heures Supp. Mois</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $heuresMois ?? '0h' }}</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">0h cette semaine</span>
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Validées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $heuresValidees ?? '0h' }}</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">0% du total</span>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $heuresAttente ?? '0h' }}</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-warning">Validation requise</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
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
                                Coût Mensuel</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $coutMensuel ?? '0 FCFA' }}</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-info">Majoration 25%</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et analyses -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line mr-2"></i>Évolution des Heures Supplémentaires
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="heuresChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-2"></i>Répartition par Service
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:250px;">
                        <canvas id="servicesHSChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Logistique</span>
                            <span class="text-muted">45h (35%)</span>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-primary" style="width: 35%"></div>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Entretien</span>
                            <span class="text-muted">38h (30%)</span>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" style="width: 30%"></div>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Commercial</span>
                            <span class="text-muted">28h (22%)</span>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-info" style="width: 22%"></div>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Administration</span>
                            <span class="text-muted">16h (13%)</span>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-secondary" style="width: 13%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes et rappels -->
    <div class="row mb-4">
        <div class="col">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-gavel mr-2"></i>
                <strong>Normes ivoiriennes - Heures Supplémentaires :</strong> Majoration de 25% pour les 8 premières heures, 50% au-delà (Code du Travail Art. 32).
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                    <label class="form-label">Service</label>
                    <select class="form-select">
                        <option value="">Tous</option>
                        <option>Logistique</option>
                        <option>Entretien</option>
                        <option>Administration</option>
                        <option>Commercial</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select class="form-select">
                        <option value="">Tous</option>
                        <option>Validé</option>
                        <option>En attente</option>
                        <option>Refusé</option>
                        <option>Payé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Mois</label>
                    <select class="form-select">
                        <option value="2025-01">Janvier 2025</option>
                        <option value="2024-12">Décembre 2024</option>
                        <option value="2024-11">Novembre 2024</option>
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

    <!-- Liste des heures supplémentaires -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>Heures Supplémentaires - Janvier 2025 (127h)
            </h6>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-success">
                    <i class="fas fa-check-double mr-1"></i>Tout valider
                </button>
                <button class="btn btn-outline-secondary">
                    <i class="fas fa-download mr-1"></i>Exporter
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="heuresSupTable">
                    <thead class="table-light">
                        <tr>
                            <th>Agent</th>
                            <th>Date</th>
                            <th>Service</th>
                            <th>Type H.S.</th>
                            <th>Durée</th>
                            <th>Majoration</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($heuresSup ?? collect() as $hs)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white mr-3">
                                            {{ collect(explode(' ', $hs->user->name ?? '')) ->filter()->map(fn($n) => mb_substr($n,0,1))->join('') }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $hs->user->name ?? '—' }}</div>
                                            <div class="text-muted small">{{ $hs->user->role ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ optional($hs->date ?? null)->format('Y-m-d') ?? ($hs->date ?? '—') }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $hs->service ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">{{ $hs->type ?? '—' }}</span>
                                </td>
                                <td><span class="font-weight-bold">{{ $hs->duree ?? '—' }}</span></td>
                                <td>{{ $hs->majoration ?? '—' }}</td>
                                <td class="font-weight-bold">{{ $hs->montant ?? '—' }}</td>
                                <td>
                                    @php
                                        $statut = $hs->statut ?? 'en_attente';
                                        $badgeClass = $statut === 'valide' ? 'success' : ($statut === 'paye' ? 'secondary' : ($statut === 'refuse' ? 'danger' : 'warning'));
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $statut)) }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('rh.heures-sup.show', $hs->id ?? 0) }}" class="btn btn-outline-primary" title="Détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('rh.heures-sup.edit', $hs->id ?? 0) }}" class="btn btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Aucune heure supplémentaire pour cette période</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Résumé du mois -->
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="card border-left-primary">
                        <div class="card-body text-center">
                            <div class="h6">Total H.S.</div>
                            <div class="h4 text-primary font-weight-bold">0h</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning">
                        <div class="card-body text-center">
                            <div class="h6">H.S. Validées</div>
                            <div class="h4 text-warning font-weight-bold">0h</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-success">
                        <div class="card-body text-center">
                            <div class="h6">H.S. Payées</div>
                            <div class="h4 text-success font-weight-bold">0h</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-info">
                        <div class="card-body text-center">
                            <div class="h6">Coût Total</div>
                            <div class="h4 text-info font-weight-bold">0 FCFA</div>
                        </div>
                    </div>
                </div>
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

.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.table-success {
    background-color: rgba(28, 200, 138, 0.1) !important;
}
</style>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Initialisation de DataTables
    $('#heuresSupTable').DataTable({
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

    // Graphique d'évolution des heures sup
    const heuresCtx = document.getElementById('heuresChart').getContext('2d');
    new Chart(heuresCtx, {
        type: 'line',
        data: {
            labels: ['Août', 'Sep', 'Oct', 'Nov', 'Déc', 'Jan'],
            datasets: [{
                label: 'Heures Supplémentaires',
                data: [95, 110, 125, 118, 134, 127],
                borderColor: 'rgb(78, 115, 223)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 25
                    }
                }
            }
        }
    });

    // Graphique de répartition par service
    const servicesHSCtx = document.getElementById('servicesHSChart').getContext('2d');
    new Chart(servicesHSCtx, {
        type: 'doughnut',
        data: {
            labels: ['Logistique', 'Entretien', 'Commercial', 'Administration'],
            datasets: [{
                data: [45, 38, 28, 16],
                backgroundColor: [
                    'rgb(78, 115, 223)',
                    'rgb(28, 200, 138)',
                    'rgb(23, 162, 184)',
                    'rgb(108, 117, 125)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
});
</script>

<!-- Modal Déclarer H.S. -->
<div class="modal fade" id="declarerHSModal" tabindex="-1" aria-labelledby="declarerHSModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="declarerHSModalLabel">
                    <i class="fas fa-plus me-2"></i>Déclarer des Heures Supplémentaires
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="declarerHSForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="employe" class="form-label">Employé <span class="text-danger">*</span></label>
                            <select class="form-select" id="employe" required>
                                <option value="">Sélectionner un employé</option>
                                <option value="1">Jean Dupont - Logistique</option>
                                <option value="2">Marie Curie - Commercial</option>
                                <option value="3">Pierre Martin - Entretien</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dateHS" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="dateHS" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="heureDebut" class="form-label">Heure Début <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="heureDebut" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="heureFin" class="form-label">Heure Fin <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="heureFin" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="totalHeures" class="form-label">Total Heures</label>
                            <input type="text" class="form-control" id="totalHeures" readonly placeholder="0h00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="typeHS" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="typeHS" required>
                            <option value="">Sélectionner un type</option>
                            <option value="normale">Heures Normales (125%)</option>
                            <option value="nuit">Heures de Nuit (150%)</option>
                            <option value="dimanche">Dimanche/Férié (200%)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="motifHS" class="form-label">Motif <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="motifHS" rows="3" placeholder="Raison des heures supplémentaires..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="enregistrerHS()">
                    <i class="fas fa-save me-2"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Planning -->
<div class="modal fade" id="planningModal" tabindex="-1" aria-labelledby="planningModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="planningModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Planning des Heures Supplémentaires
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="moisPlanning" class="form-label">Mois</label>
                        <input type="month" class="form-control" id="moisPlanning" value="{{ date('Y-m') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="servicePlanning" class="form-label">Service</label>
                        <select class="form-select" id="servicePlanning">
                            <option value="">Tous les services</option>
                            <option value="logistique">Logistique</option>
                            <option value="commercial">Commercial</option>
                            <option value="entretien">Entretien</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filtrer
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Employé</th>
                                <th>Service</th>
                                <th>Semaine 1</th>
                                <th>Semaine 2</th>
                                <th>Semaine 3</th>
                                <th>Semaine 4</th>
                                <th>Total Mois</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Jean Dupont</td>
                                <td>Logistique</td>
                                <td><span class="badge bg-info">8h</span></td>
                                <td><span class="badge bg-info">12h</span></td>
                                <td><span class="badge bg-info">6h</span></td>
                                <td><span class="badge bg-info">10h</span></td>
                                <td><strong>36h</strong></td>
                            </tr>
                            <tr>
                                <td>Marie Curie</td>
                                <td>Commercial</td>
                                <td><span class="badge bg-info">5h</span></td>
                                <td><span class="badge bg-info">8h</span></td>
                                <td><span class="badge bg-info">4h</span></td>
                                <td><span class="badge bg-info">7h</span></td>
                                <td><strong>24h</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-success">
                    <i class="fas fa-file-excel me-2"></i>Exporter Excel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Validation -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="validationModalLabel">
                    <i class="fas fa-check-double me-2"></i>Validation des Heures Supplémentaires
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>15 déclarations</strong> en attente
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Date</th>
                                <th>Employé</th>
                                <th>Heures</th>
                                <th>Type</th>
                                <th>Motif</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox" class="select-item"></td>
                                <td>{{ date('d/m/Y') }}</td>
                                <td>Jean Dupont</td>
                                <td><strong>4h30</strong></td>
                                <td><span class="badge bg-primary">Normale</span></td>
                                <td>Livraison urgente</td>
                                <td><span class="badge bg-warning">En attente</span></td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="validerHS(1)">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="refuserHS(1)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="select-item"></td>
                                <td>{{ date('d/m/Y', strtotime('-1 day')) }}</td>
                                <td>Marie Curie</td>
                                <td><strong>3h00</strong></td>
                                <td><span class="badge bg-info">Nuit</span></td>
                                <td>Inventaire nocturne</td>
                                <td><span class="badge bg-warning">En attente</span></td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="validerHS(2)">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="refuserHS(2)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-danger" onclick="refuserSelection()">
                    <i class="fas fa-times me-2"></i>Refuser Sélection
                </button>
                <button type="button" class="btn btn-success" onclick="validerSelection()">
                    <i class="fas fa-check me-2"></i>Valider Sélection
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Calcul automatique des heures
document.getElementById('heureDebut')?.addEventListener('change', calculerHeures);
document.getElementById('heureFin')?.addEventListener('change', calculerHeures);

function calculerHeures() {
    const debut = document.getElementById('heureDebut').value;
    const fin = document.getElementById('heureFin').value;

    if (debut && fin) {
        const [hD, mD] = debut.split(':').map(Number);
        const [hF, mF] = fin.split(':').map(Number);

        let totalMinutes = (hF * 60 + mF) - (hD * 60 + mD);
        if (totalMinutes < 0) totalMinutes += 24 * 60;

        const heures = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;

        document.getElementById('totalHeures').value = `${heures}h${minutes.toString().padStart(2, '0')}`;
    }
}

function enregistrerHS() {
    const form = document.getElementById('declarerHSForm');
    if (form.checkValidity()) {
        alert('Heures supplémentaires enregistrées avec succès !');
        bootstrap.Modal.getInstance(document.getElementById('declarerHSModal')).hide();
        form.reset();
    } else {
        form.reportValidity();
    }
}

function validerHS(id) {
    if (confirm('Valider ces heures supplémentaires ?')) {
        alert(`Heures supplémentaires #${id} validées`);
    }
}

function refuserHS(id) {
    const motif = prompt('Motif du refus :');
    if (motif) {
        alert(`Heures supplémentaires #${id} refusées. Motif: ${motif}`);
    }
}

function validerSelection() {
    const selected = document.querySelectorAll('.select-item:checked');
    if (selected.length > 0) {
        if (confirm(`Valider ${selected.length} déclaration(s) ?`)) {
            alert(`${selected.length} déclaration(s) validée(s)`);
        }
    } else {
        alert('Aucune déclaration sélectionnée');
    }
}

function refuserSelection() {
    const selected = document.querySelectorAll('.select-item:checked');
    if (selected.length > 0) {
        const motif = prompt('Motif du refus :');
        if (motif) {
            alert(`${selected.length} déclaration(s) refusée(s). Motif: ${motif}`);
        }
    } else {
        alert('Aucune déclaration sélectionnée');
    }
}

// Select all checkbox
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.select-item').forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
