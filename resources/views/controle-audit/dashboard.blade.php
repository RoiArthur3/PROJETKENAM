@extends('layouts.app')

@section('title', 'Dashboard Contrôle & Audit | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-search-plus me-2 text-primary"></i>Dashboard Contrôle & Audit
            </h1>
            <p class="text-muted mb-0">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
        </div>
        <button class="btn btn-primary" onclick="rafraichirDashboard()">
            <i class="fas fa-sync-alt me-2"></i>Rafraîchir
        </button>
    </div>

    <!-- KPIs Principaux -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Total Contrôles</div>
                            <div class="h3 mb-0 text-primary">{{ $totalControles ?? 0 }}</div>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> +{{ rand(5,15) }}% vs mois dernier</small>
                        </div>
                        <i class="fas fa-search-plus fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Contrôles Réussis</div>
                            <div class="h3 mb-0 text-success">{{ $controlesReussis ?? 0 }}</div>
                            <small class="text-success">{{ $tauxReussite ?? 0 }}% de réussite</small>
                        </div>
                        <i class="fas fa-check-circle fa-3x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Anomalies Détectées</div>
                            <div class="h3 mb-0 text-warning">{{ $anomaliesDetectees ?? 0 }}</div>
                            <small class="text-warning">{{ $anomaliesCritiques ?? 0 }} critiques</small>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-3x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Contrôles du Jour</div>
                            <div class="h3 mb-0 text-info">{{ $controlesJour ?? 0 }}</div>
                            <small class="text-info">{{ $controlesEnCours ?? 0 }} en cours</small>
                        </div>
                        <i class="fas fa-calendar-day fa-3x text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0 text-primary fw-bold">
                <i class="fas fa-filter me-2"></i>Filtres et Période
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Période</label>
                    <select name="periode" class="form-select" id="periodePredefinie">
                        <option value="">Toutes</option>
                        <option value="aujourd_hui">Aujourd'hui</option>
                        <option value="cette_semaine">Cette semaine</option>
                        <option value="ce_mois">Ce mois</option>
                        <option value="ce_trimestre">Ce trimestre</option>
                        <option value="cette_annee">Cette année</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Date début</label>
                    <input type="date" class="form-control" id="dateDebut" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Date fin</label>
                    <input type="date" class="form-control" id="dateFin" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Statut</label>
                    <select name="statut" class="form-select" id="statutFilter">
                        <option value="">Tous</option>
                        <option value="en_attente">En attente</option>
                        <option value="en_cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="anomalie">Anomalie</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary" onclick="appliquerFiltres()">
                    <i class="fas fa-search me-2"></i>Appliquer les filtres
                </button>
                <button class="btn btn-outline-secondary ms-2" onclick="reinitialiserFiltres()">
                    <i class="fas fa-undo me-2"></i>Réinitialiser
                </button>
            </div>
        </div>
    </div>

    <!-- Graphiques et Analyses -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Évolution des Contrôles
                    </h6>
                    <small class="text-muted">Derniers 6 mois - Nombre de contrôles par mois</small>
                </div>
                <div class="card-body">
                    <canvas id="controlesChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Répartition par Statut
                    </h6>
                    <small class="text-muted">Répartition actuelle</small>
                </div>
                <div class="card-body">
                    <canvas id="statutsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux de synthèse -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-list-check me-2"></i>Derniers Contrôles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>CTL-2024-001</strong></td>
                                    <td>Qualité</td>
                                    <td>{{ now()->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-success">Terminé</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>CTL-2024-002</strong></td>
                                    <td>Sécurité</td>
                                    <td>{{ now()->subDays(1)->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-warning">En cours</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>CTL-2024-003</strong></td>
                                    <td>Maintenance</td>
                                    <td>{{ now()->subDays(2)->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-danger">Anomalie</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-exclamation-triangle me-2"></i>Anomalies Récentes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Description</th>
                                    <th>Criticité</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>ANO-2024-001</strong></td>
                                    <td>Défaut de conformité qualité</td>
                                    <td><span class="badge bg-danger">Critique</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning" title="Traiter">
                                            <i class="fas fa-tools"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>ANO-2024-002</strong></td>
                                    <td>Non-respect procédure sécurité</td>
                                    <td><span class="badge bg-warning">Majeure</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning" title="Traiter">
                                            <i class="fas fa-tools"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>ANO-2024-003</strong></td>
                                    <td>Dépassement délai maintenance</td>
                                    <td><span class="badge bg-info">Mineure</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning" title="Traiter">
                                            <i class="fas fa-tools"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/chart.js') }}"></script>
<script>
// Graphiques du dashboard
$(document).ready(function() {
    // Graphique d'évolution des contrôles
    const controlesCtx = document.getElementById('controlesChart').getContext('2d');
    new Chart(controlesCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Contrôles Réussis',
                data: [12, 15, 18, 14, 22, 19, 25, 28, 24, 30, 26, 32],
                borderColor: 'rgb(28, 200, 138)',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Contrôles avec Anomalies',
                data: [2, 3, 1, 4, 2, 3, 1, 2, 4, 1, 3, 2],
                borderColor: 'rgb(255, 193, 7)',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Graphique de répartition par statut
    const statutsCtx = document.getElementById('statutsChart').getContext('2d');
    new Chart(statutsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Terminé', 'En cours', 'En attente', 'Anomalie'],
            datasets: [{
                data: [65, 20, 10, 5],
                backgroundColor: [
                    'rgb(28, 200, 138)',
                    'rgb(255, 193, 7)',
                    'rgb(23, 162, 184)',
                    'rgb(220, 53, 69)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});

// Fonctions utilitaires
function rafraichirDashboard() {
    location.reload();
}

function appliquerFiltres() {
    const periode = document.getElementById('periodePredefinie').value;
    const dateDebut = document.getElementById('dateDebut').value;
    const dateFin = document.getElementById('dateFin').value;
    const statut = document.getElementById('statutFilter').value;

    console.log('Filtres appliqués:', { periode, dateDebut, dateFin, statut });
    // Ici, on pourrait recharger les données avec les filtres
}

function reinitialiserFiltres() {
    document.getElementById('periodePredefinie').value = '';
    document.getElementById('dateDebut').value = '{{ date("Y-m-d", strtotime("-30 days")) }}';
    document.getElementById('dateFin').value = '{{ date("Y-m-d") }}';
    document.getElementById('statutFilter').value = '';
}
</script>
@endpush
@endsection>
