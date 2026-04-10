@extends('layouts.app')

@section('title', 'Indicateurs Clés de Performance (KPI) - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt me-2 text-primary"></i>Indicateurs Clés de Performance
            </h1>
            <p class="text-muted mb-0">Tableau de bord des KPIs principaux</p>
        </div>
        <button class="btn btn-primary" onclick="rafraichirKPIs()">
            <i class="fas fa-sync-alt me-2"></i>Rafraîchir
        </button>
    </div>

    <!-- KPIs Financiers (données dynamiques) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-coins me-2"></i>KPIs Financiers</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border-start border-primary border-4 ps-3">
                                <div class="text-muted small text-uppercase fw-bold">CA Mensuel</div>
                                @php $ca = $kpis['financial']['total_revenue'] ?? 0; @endphp
                                <div class="h3 mb-0 text-primary">{{ number_format($ca, 0, ',', ' ') }} FCFA</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: 85%"></div>
                                </div>
                                <small class="text-muted">Basé sur les factures de la période</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border-start border-success border-4 ps-3">
                                <div class="text-muted small text-uppercase fw-bold">Marge Brute</div>
                                @php
                                    $revenu = $kpis['financial']['total_revenue'] ?? 0;
                                    $depenses = $kpis['financial']['total_expenses'] ?? 0;
                                    $margeBrute = max($revenu - $depenses, 0);
                                @endphp
                                <div class="h3 mb-0 text-success">{{ number_format($margeBrute, 0, ',', ' ') }} FCFA</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 78%"></div>
                                </div>
                                <small class="text-muted">Total revenus - dépenses</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border-start border-warning border-4 ps-3">
                                <div class="text-muted small text-uppercase fw-bold">ROI Global</div>
                                @php $roi = $kpis['financial']['profit_margin'] ?? 0; @endphp
                                <div class="h3 mb-0 text-warning">{{ $roi }}%</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 92%"></div>
                                </div>
                                <small class="text-muted">Marge nette / CA</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border-start border-info border-4 ps-3">
                                <div class="text-muted small text-uppercase fw-bold">Coût Moyen/Op</div>
                                @php
                                    $totalOps = $kpis['operations']['total'] ?? 0;
                                    $coutMoyen = $totalOps > 0 ? ($depenses / $totalOps) : 0;
                                @endphp
                                <div class="h3 mb-0 text-info">{{ number_format($coutMoyen, 0, ',', ' ') }} FCFA</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: 65%"></div>
                                </div>
                                <small class="text-muted">Dépenses / nombre d'opérations</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Opérationnels (données dynamiques) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-tasks me-2"></i>KPIs Opérationnels</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border-start border-primary border-4 ps-3">
                                <div class="text-muted small text-uppercase fw-bold">Opérations (période)</div>
                                <div class="h3 mb-0 text-primary">{{ $kpis['operations']['total'] ?? 0 }}</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: 88%"></div>
                                </div>
                                <small class="text-success"><i class="fas fa-arrow-up"></i> +12% vs mois dernier</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border-start border-success border-4 ps-3">
                                <div class="text-muted small text-uppercase fw-bold">Taux de Livraison</div>
                                @php
                                    $completed = $kpis['operations']['completed'] ?? 0;
                                    $totalOps = $kpis['operations']['total'] ?? 0;
                                    $tauxLivraison = $totalOps > 0 ? round(($completed / $totalOps) * 100, 1) : 0;
                                @endphp
                                <div class="h3 mb-0 text-success">{{ $tauxLivraison }}%</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 96%"></div>
                                </div>
                                <small class="text-success"><i class="fas fa-check"></i> Excellent</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border-start border-warning border-4 ps-3">
                                <div class="text-muted small text-uppercase fw-bold">Temps Moyen</div>
                                <div class="h3 mb-0 text-warning">{{ $kpis['operations']['avg_duration'] ?? 0 }}h</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 70%"></div>
                                </div>
                                <small class="text-success"><i class="fas fa-arrow-down"></i> -8% (amélioration)</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border-start border-info border-4 ps-3">
                                <div class="text-muted small text-uppercase fw-bold">Utilisation Flotte</div>
                                <div class="h3 mb-0 text-info">{{ $kpis['fleet']['utilization_rate'] ?? 0 }}%</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: 78%"></div>
                                </div>
                                <small class="text-success"><i class="fas fa-arrow-up"></i> +5% vs mois dernier</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Clients (données dynamiques) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-users me-2"></i>KPIs Clients</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border-start border-primary border-4 ps-3">
                                <div class="text-muted small text-uppercase fw-bold">Satisfaction</div>
                                <div class="h3 mb-0 text-primary">{{ $kpis['commercial']['client_satisfaction'] ?? 0 }}/5</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: 94%"></div>
                                </div>
                                <small class="text-success"><i class="fas fa-arrow-up"></i> +0.2 vs mois dernier</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border-start border-success border-4 ps-3">
                                <div class="text-muted small text-uppercase fw-bold">Taux Rétention</div>
                                @php
                                    $totalClients = $kpis['commercial']['total_clients'] ?? 0;
                                    $activeClients = $kpis['commercial']['active_clients'] ?? 0;
                                    $tauxRetention = $totalClients > 0 ? round(($activeClients / $totalClients) * 100, 1) : 0;
                                @endphp
                                <div class="h3 mb-0 text-success">{{ $tauxRetention }}%</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 92%"></div>
                                </div>
                                <small class="text-success"><i class="fas fa-check"></i> Très bon</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border-start border-warning border-4 ps-3">
                                <div class="text-muted small text-uppercase fw-bold">Nouveaux Clients</div>
                                <div class="h3 mb-0 text-warning">{{ $kpis['commercial']['new_clients'] ?? 0 }}</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 75%"></div>
                                </div>
                                <small class="text-success"><i class="fas fa-arrow-up"></i> +20% vs mois dernier</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border-start border-info border-4 ps-3">
                                <div class="text-muted small text-uppercase fw-bold">Panier Moyen</div>
                                @php
                                    $totalClients = max($totalClients, 1);
                                    $panierMoyen = $totalClients > 0 ? ($revenu / $totalClients) : 0;
                                @endphp
                                <div class="h3 mb-0 text-info">{{ number_format($panierMoyen, 0, ',', ' ') }} FCFA</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: 82%"></div>
                                </div>
                                <small class="text-success"><i class="fas fa-arrow-up"></i> +8% vs mois dernier</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques KPIs -->
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Évolution des KPIs Financiers
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="kpiFinChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Évolution des KPIs Opérationnels
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="kpiOpChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxFin = document.getElementById('kpiFinChart').getContext('2d');
new Chart(ctxFin, {
    type: 'line',
    data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
        datasets: [{
            label: 'CA (M FCFA)',
            data: [32, 38, 35, 42, 45, 48],
            borderColor: '#4e73df',
            tension: 0.4
        }, {
            label: 'Marge (M FCFA)',
            data: [9, 11, 10, 12, 13, 14],
            borderColor: '#1cc88a',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } }
    }
});

const ctxOp = document.getElementById('kpiOpChart').getContext('2d');
new Chart(ctxOp, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
        datasets: [{
            label: 'Opérations',
            data: [120, 135, 128, 145, 152, 156],
            backgroundColor: '#4e73df'
        }, {
            label: 'Taux Livraison (%)',
            data: [92, 94, 93, 95, 96, 96],
            backgroundColor: '#1cc88a'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } }
    }
});

function rafraichirKPIs() {
    location.reload();
}
</script>
@endsection
