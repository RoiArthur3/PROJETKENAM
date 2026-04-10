@extends('layouts.app')

@section('title', 'Dashboard Matériel - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Dashboard Matériel</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('materiel.vehicules') }}" class="btn btn-outline-primary">
                <i class="fas fa-car me-2"></i>Véhicules
            </a>
            <a href="{{ route('materiel.maintenance.index') }}" class="btn btn-outline-warning">
                <i class="fas fa-wrench me-2"></i>Maintenance
            </a>
            <a href="{{ route('materiel.carburant.index') }}" class="btn btn-outline-info">
                <i class="fas fa-gas-pump me-2"></i>Carburant
            </a>
            <a href="{{ route('materiel.rapports') }}" class="btn btn-outline-success">
                <i class="fas fa-chart-bar me-2"></i>Rapports
            </a>
        </div>
    </div>

    <!-- KPIs Principaux -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Véhicules</h6>
                            <h3 class="mb-0">{{ $vehicules->count() ?? 0 }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-car"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Kilométrage Total</h6>
                            <h3 class="mb-0">{{ number_format($vehicules->sum('kilometrage') ?? 0, 0, ',', ' ') }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">En Maintenance</h6>
                            <h3 class="mb-0">{{ $maintenances->count() ?? 0 }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-wrench"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Coût Carburant (30j)</h6>
                            <h3 class="mb-0">{{ number_format(array_sum($carburantChart['montants'] ?? []), 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-gas-pump"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes Véhicules -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-warning">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>Alertes du Parc Automobile
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-warning text-white me-3">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Ford Transit - CI-789-SAN</div>
                                    <div class="text-muted">En maintenance depuis 5 jours</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-warning text-white me-3">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Mercedes Sprinter - CI-012-YMK</div>
                                    <div class="text-muted">Consommation élevée (14.0 L/100km)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et Statistiques -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Consommation Carburant (30 derniers jours)</h5>
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="carburantChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Réparation par Type</h5>
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="maintenanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières Activités -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Dernières Maintenances</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Véhicule</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Coût</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>MAINT-2026-001</td>
                                    <td>Toyota Hilux</td>
                                    <td>Préventive</td>
                                    <td>15/01/2026</td>
                                    <td>150 000 FCFA</td>
                                    <td><span class="badge bg-success">Terminée</span></td>
                                </tr>
                                <tr>
                                    <td>MAINT-2026-002</td>
                                    <td>Ford Transit</td>
                                    <td>Corrective</td>
                                    <td>18/01/2026</td>
                                    <td>350 000 FCFA</td>
                                    <td><span class="badge bg-info">En cours</span></td>
                                </tr>
                                <tr>
                                    <td>MAINT-2026-003</td>
                                    <td>Nissan Patrol</td>
                                    <td>Préventive</td>
                                    <td>23/01/2026</td>
                                    <td>200 000 FCFA</td>
                                    <td><span class="badge bg-success">Terminée</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Derniers Pleins Carburant</h5>
                    <div class="table-responsive">
                        <table class="table-sm">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Véhicule</th>
                                    <th>Quantité</th>
                                    <th>Coût</th>
                                    <th>Date</th>
                                    <th>Chauffeur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>CARB-2026-001</td>
                                    <td>Toyota Hilux</td>
                                    <td>65 L</td>
                                    <td>42 250 FCFA</td>
                                    <td>20/01/2026</td>
                                    <td>Konan Yves</td>
                                </tr>
                                <tr>
                                    <td>CARB-2026-002</td>
                                    <td>Nissan Patrol</td>
                                    <td>55 L</td>
                                    <td>35 750 FCFA</td>
                                    <td>21/01/2026</td>
                                    <td>Bamba Mamadou</td>
                                </tr>
                                <tr>
                                    <td>CARB-2026-003</td>
                                    <td>Mercedes Sprinter</td>
                                    <td>70 L</td>
                                    <td>45 500 FCFA</td>
                                    <td>22/01/2026</td>
                                    <td>Yao Sébastien</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- État du Parc -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">État du Parc Automobile</h5>
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary">Toyota Hilux - CI-123-ABJ</h6>
                                    <p class="mb-0">Pickup - 2022</p>
                                    <small>Km: 45 000 | Actif</small>
                                    <small>Carburant: 75/80L</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-info">Nissan Patrol - CI-456-BKE</h6>
                                    <p class="mb-0">SUV - 2021</p>
                                    <small>Km: 62 000 | Actif</small>
                                    <small>Carburant: 60/75L</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-warning">Ford Transit - CI-789-SAN</h6>
                                    <p class="mb-0">Utilitaire - 2023</p>
                                    <small>Km: 28 000 | Maintenance</small>
                                    <small>Carburant: 40/70L</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-success">Mercedes Sprinter - CI-012-YMK</h6>
                                    <p class="mb-0">Fourgon - 2020</p>
                                    <small>Km: 85 000 | Actif</small>
                                    <small>Carburant: 85/90L</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique de consommation carburant
    const ctx1 = document.getElementById('carburantChart');
    if (ctx1) {
        const carburantChartData = @json($carburantChart ?? ['labels' => [], 'litres' => [], 'montants' => []]);
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: carburantChartData.labels || [],
                datasets: [{
                    label: 'Coût (FCFA)',
                    data: carburantChartData.montants || [],
                    backgroundColor: 'rgba(255, 99, 132, 0.35)',
                    borderColor: 'rgb(255, 99, 132)',
                    borderWidth: 1
                }, {
                    label: 'Consommation (L)',
                    data: carburantChartData.litres || [],
                    backgroundColor: 'rgba(75, 192, 192, 0.35)',
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 45,
                            autoSkip: true,
                            maxTicksLimit: 10
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Graphique des maintenances
    const ctx2 = document.getElementById('maintenanceChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Préventive', 'Corrective'],
                datasets: [{
                    data: [3, 1],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
@endsection
