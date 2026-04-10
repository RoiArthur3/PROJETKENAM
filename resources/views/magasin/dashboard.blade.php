@extends('layouts.app')

@section('title', 'Dashboard Magasin - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Dashboard Magasin</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('magasin.inventaire') }}" class="btn btn-outline-primary">
                <i class="fas fa-boxes me-2"></i>Inventaire
            </a>
            <a href="{{ route('magasin.entrees') }}" class="btn btn-outline-success">
                <i class="fas fa-arrow-down me-2"></i>Entrées
            </a>
            <a href="{{ route('magasin.sorties') }}" class="btn btn-outline-danger">
                <i class="fas fa-arrow-up me-2"></i>Sorties
            </a>
            <a href="{{ route('magasin.rapports') }}" class="btn btn-outline-info">
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
                            <h6 class="text-muted mb-1">Total Articles</h6>
                            <h3 class="mb-0">{{ $stocks->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-boxes"></i>
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
                            <h6 class="text-muted mb-1">Valeur Stock</h6>
                            <h3 class="mb-0">{{ number_format($stocks->sum('valeur'), 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-wallet"></i>
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
                            <h6 class="text-muted mb-1">Entrées Mois</h6>
                            <h3 class="mb-0">{{ $entrees->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
                                <i class="fas fa-arrow-down"></i>
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
                            <h6 class="text-muted mb-1">Sorties Mois</h6>
                            <h3 class="mb-0">{{ $sorties->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-danger text-white">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes Stock -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-warning">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>Alertes de Stock Critique
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-danger text-white me-3">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Chaise de Bureau</div>
                                    <div class="text-muted">Stock: 3 / Min: 10</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-warning text-white me-3">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Imprimante HP LaserJet</div>
                                    <div class="text-muted">Stock: 8 / Min: 5</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-warning text-white me-3">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Papier A4</div>
                                    <div class="text-muted">Stock: 12 / Min: 20</div>
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
                    <h5 class="card-title mb-4">Mouvements de Stock (30 derniers jours)</h5>
                    <canvas id="mouvementsChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Répartition par Catégorie</h5>
                    <canvas id="categorieChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières Activités -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Dernières Entrées</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ENT-2026-001</td>
                                    <td>Ordinateur Dell XPS</td>
                                    <td>10</td>
                                    <td>18/01/2026</td>
                                </tr>
                                <tr>
                                    <td>ENT-2026-002</td>
                                    <td>Imprimante HP</td>
                                    <td>5</td>
                                    <td>17/01/2026</td>
                                </tr>
                                <tr>
                                    <td>ENT-2026-003</td>
                                    <td>Bureau Réglable</td>
                                    <td>8</td>
                                    <td>16/01/2026</td>
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
                    <h5 class="card-title mb-4">Dernières Sorties</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Produit</th>
                                    <th>Demandeur</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SORT-2026-001</td>
                                    <td>Ordinateur Dell XPS</td>
                                    <td>Service IT</td>
                                    <td>19/01/2026</td>
                                </tr>
                                <tr>
                                    <td>SORT-2026-002</td>
                                    <td>Imprimante HP</td>
                                    <td>Direction</td>
                                    <td>18/01/2026</td>
                                </tr>
                                <tr>
                                    <td>SORT-2026-003</td>
                                    <td>Chaise Bureau</td>
                                    <td>Commercial</td>
                                    <td>17/01/2026</td>
                                </tr>
                            </tbody>
                        </table>
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
    // Graphique des mouvements
    const ctx1 = document.getElementById('mouvementsChart');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['01/01', '05/01', '10/01', '15/01', '20/01', '25/01'],
                datasets: [{
                    label: 'Entrées',
                    data: [5, 8, 3, 12, 7, 9],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Sorties',
                    data: [3, 6, 8, 5, 9, 11],
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Graphique des catégories
    const ctx2 = document.getElementById('categorieChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Informatique', 'Mobilier', 'Consommables', 'Équipements'],
                datasets: [{
                    data: [45, 25, 20, 10],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
});
</script>
@endsection
