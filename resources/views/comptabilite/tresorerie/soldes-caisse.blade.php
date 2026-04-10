@extends('layouts.app')

@section('title', 'Soldes de Caisse - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Soldes de Caisse</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Trésorerie
            </a>
            <a href="{{ route('tresorerie.caisses') }}" class="btn btn-outline-info">
                <i class="fas fa-cash-register me-2"></i>Caisses
            </a>
            <a href="{{ route('tresorerie.approvisionnements') }}" class="btn btn-outline-success">
                <i class="fas fa-plus-circle me-2"></i>Approvisionnements
            </a>
            <a href="{{ route('tresorerie.decaissements') }}" class="btn btn-outline-warning">
                <i class="fas fa-money-bill-wave me-2"></i>Décaissements
            </a>
        </div>
    </div>

    <!-- KPIs Soldes -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Caisses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $soldes->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cash-register fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Solde Total Actuel</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($soldes->sum('solde_actuel'), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Solde Total Initial</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($soldes->sum('solde_initial'), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Variation Totale</div>
                            <div class="h5 mb-0 font-weight-bold text-{{ $soldes->sum('variation') > 0 ? 'success' : 'danger' }}">
                                {{ number_format($soldes->sum('variation'), 0, ',', ' ') }} FCFA
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

    <!-- Graphique des soldes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Évolution des Soldes</h5>
                    <canvas id="soldesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Soldes -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Détail des Soldes par Caisse</h5>

            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Rechercher une caisse..." id="searchInput">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="variationFilter">
                        <option value="">Toutes les variations</option>
                        <option value="positif">Variation positive</option>
                        <option value="negatif">Variation négative</option>
                        <option value="nul">Variation nulle</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="triFilter">
                        <option value="">Trier par</option>
                        <option value="nom">Nom de caisse</option>
                        <option value="solde_actuel">Solde actuel</option>
                        <option value="variation">Variation</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info" onclick="exportSoldes()">
                        <i class="fas fa-download me-2"></i>Exporter
                    </button>
                </div>
            </div>

            <!-- Tableau -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Caisse</th>
                            <th>Solde Initial</th>
                            <th>Solde Actuel</th>
                            <th>Variation</th>
                            <th>Pourcentage</th>
                            <th>Tendance</th>
                            <th>Dernière Mise à Jour</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($soldes as $solde)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-{{ $solde->variation > 0 ? 'success' : $solde->variation < 0 ? 'danger' : 'info' }} text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($solde->caisse, -3)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $solde->caisse }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ number_format($solde->solde_initial, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <span class="fw-bold {{ $solde->solde_actuel > $solde->solde_initial ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($solde->solde_actuel, 0, ',', ' ') }} FCFA
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold {{ $solde->variation > 0 ? 'text-success' : $solde->variation < 0 ? 'text-danger' : 'text-info' }}">
                                    {{ $solde->variation > 0 ? '+' : '' }}{{ number_format($solde->variation, 0, ',', ' ') }} FCFA
                                </span>
                            </td>
                            <td>
                                @php
                                $pourcentage = $solde->solde_initial > 0 ? ($solde->variation / $solde->solde_initial) * 100 : 0;
                                @endphp
                                <span class="{{ $pourcentage > 0 ? 'text-success' : $pourcentage < 0 ? 'text-danger' : 'text-info' }}">
                                    {{ number_format($pourcentage, 2, ',', ' ') }}%
                                </span>
                            </td>
                            <td>
                                @if($solde->variation > 0)
                                    <i class="fas fa-arrow-up text-success"></i> Hausse
                                @elseif($solde->variation < 0)
                                    <i class="fas fa-arrow-down text-danger"></i> Baisse
                                @else
                                    <i class="fas fa-minus text-info"></i> Stable
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($solde->date_maj)->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="showDetails('{{ $solde->caisse }}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" onclick="showHistorique('{{ $solde->caisse }}')">
                                        <i class="fas fa-history"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="imprimerSolde('{{ $solde->caisse }}')">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des soldes
    const ctx = document.getElementById('soldesChart');
    if (ctx) {
        const soldesData = @json($soldes->toArray());
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: soldesData.map(s => s.caisse),
                datasets: [
                    {
                        label: 'Solde Initial',
                        data: soldesData.map(s => s.solde_initial),
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Solde Actuel',
                        data: soldesData.map(s => s.solde_actuel),
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' FCFA';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    }

    // Fonction de recherche
    function filterSoldes() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const variation = document.getElementById('variationFilter').value;
        const tri = document.getElementById('triFilter').value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();

            const matchesSearch = !search || rowText.includes(search);

            let matchesVariation = true;
            if (variation === 'positif') {
                matchesVariation = cells[4]?.textContent.includes('+') && !cells[4]?.textContent.includes('0 FCFA');
            } else if (variation === 'negatif') {
                matchesVariation = cells[4]?.textContent.includes('-');
            } else if (variation === 'nul') {
                matchesVariation = cells[4]?.textContent.includes('0 FCFA');
            }

            row.style.display = (matchesSearch && matchesVariation) ? '' : 'none';
        });
    }

    // Fonctions d'action
    function showDetails(caisse) {
        alert('Afficher les détails de la caisse: ' + caisse);
    }

    function showHistorique(caisse) {
        alert('Afficher l\'historique de la caisse: ' + caisse);
    }

    function imprimerSolde(caisse) {
        window.print();
    }

    function exportSoldes() {
        alert('Export des soldes simulé');
    }

    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterSoldes);
    document.getElementById('variationFilter').addEventListener('change', filterSoldes);
    document.getElementById('triFilter').addEventListener('change', filterSoldes);
});
</script>

@endsection
