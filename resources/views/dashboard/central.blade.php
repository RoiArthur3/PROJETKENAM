@extends('layouts.app')

@section('title', 'Tableau de Bord - ERP Logistique')

@section('content')
<x-dashboard-layout title="Tableau de Bord Principal" icon="fa-tachometer-alt">
    <x-slot name="kpis">
        <!-- Filtres -->
        <div class="row mb-4">
            <div class="col-md-6">
                <select class="form-select" id="periodFilter" onchange="changeFilters()">
                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Cette semaine</option>
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Ce mois</option>
                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Cette année</option>
                    <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Toute période</option>
                </select>
            </div>
            <div class="col-md-6">
                <select class="form-select" id="agenceFilter" onchange="changeFilters()">
                    <option value="all" {{ $agence === 'all' ? 'selected' : '' }}>Toutes les agences</option>
                    <option value="abidjan" {{ $agence === 'abidjan' ? 'selected' : '' }}>Abidjan</option>
                    <option value="yamoussoukro" {{ $agence === 'yamoussoukro' ? 'selected' : '' }}>Yamoussoukro</option>
                    <option value="bouake" {{ $agence === 'bouake' ? 'selected' : '' }}>Bouaké</option>
                </select>
            </div>
        </div>

        <!-- Alertes critiques -->
        @if($alerts->count() > 0)
            <div class="row mb-4">
                @foreach($alerts as $alert)
                    <div class="col-md-4">
                        <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                            <i class="fas {{ $alert['icon'] }} me-2"></i>
                            <strong>{{ $alert['title'] }}:</strong> {{ $alert['count'] }} éléments
                            <a href="{{ $alert['link'] }}" class="alert-link">Voir</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- KPIs principaux -->
        <div class="row">
            <!-- Opérations -->
            <div class="col-lg-3 col-md-6 mb-4">
                <x-kpi-card 
                    title="Opérations" 
                    value="{{ $kpis['operations']['total'] }}" 
                    icon="fa-truck" 
                    color="primary"
                    :details="[
                        'En cours: ' . $kpis['operations']['en_cours'],
                        'Terminées: ' . $kpis['operations']['terminees'],
                        'Taux: ' . $kpis['operations']['taux_completion'] . '%'
                    ]"
                />
            </div>

            <!-- Factures -->
            <div class="col-lg-3 col-md-6 mb-4">
                <x-kpi-card 
                    title="Factures" 
                    value="{{ $kpis['factures']['total'] }}" 
                    icon="fa-file-invoice" 
                    color="success"
                    :details="[
                        'Payées: ' . $kpis['factures']['payees'],
                        'Impayées: ' . $kpis['factures']['impayees'],
                        'Taux paiement: ' . $kpis['factures']['taux_paiement'] . '%'
                    ]"
                />
            </div>

            <!-- Parc Auto -->
            <div class="col-lg-3 col-md-6 mb-4">
                <x-kpi-card 
                    title="Véhicules" 
                    value="{{ $kpis['parc_auto']['total'] }}" 
                    icon="fa-car" 
                    color="info"
                    :details="[
                        'Disponibles: ' . $kpis['parc_auto']['disponibles'],
                        'En mission: ' . $kpis['parc_auto']['en_mission'],
                        'Taux dispo: ' . $kpis['parc_auto']['taux_disponibilite'] . '%'
                    ]"
                />
            </div>

            <!-- Stock -->
            <div class="col-lg-3 col-md-6 mb-4">
                <x-kpi-card 
                    title="Stock" 
                    value="{{ $kpis['stock']['total_produits'] }}" 
                    icon="fa-boxes" 
                    color="warning"
                    :details="[
                        'Alertes: ' . $kpis['stock']['alertes_stock'],
                        'Ruptures: ' . $kpis['stock']['rupture_stock'],
                        'Valeur: ' . number_format($kpis['stock']['valeur_totale'], 0, ',', ' ') . ' FCFA'
                    ]"
                />
            </div>
        </div>

        <!-- KPIs secondaires -->
        <div class="row">
            <!-- RH -->
            <div class="col-lg-3 col-md-6 mb-4">
                <x-kpi-card 
                    title="Personnel" 
                    value="{{ $kpis['rh']['total'] }}" 
                    icon="fa-users" 
                    color="secondary"
                    :details="[
                        'Actifs: ' . $kpis['rh']['actifs'],
                        'En mission: ' . $kpis['rh']['en_mission'],
                        'En congé: ' . $kpis['rh']['en_conge']
                    ]"
                />
            </div>

            <!-- Commercial -->
            <div class="col-lg-3 col-md-6 mb-4">
                <x-kpi-card 
                    title="Clients" 
                    value="{{ $kpis['commercial']['total_clients'] }}" 
                    icon="fa-handshake" 
                    color="primary"
                    :details="[
                        'Nouveaux: ' . $kpis['commercial']['nouveaux_clients'],
                        'Actifs: ' . $kpis['commercial']['clients_actifs']
                    ]"
                />
            </div>

            <!-- Fournisseurs -->
            <div class="col-lg-3 col-md-6 mb-4">
                <x-kpi-card 
                    title="Fournisseurs" 
                    value="{{ $kpis['fournisseurs']['total'] }}" 
                    icon="fa-truck-loading" 
                    color="info"
                    :details="[
                        'Actifs: ' . $kpis['fournisseurs']['actifs'],
                        'Évalués: ' . $kpis['fournisseurs']['avec_evaluation']
                    ]"
                />
            </div>

            <!-- Finances -->
            <div class="col-lg-3 col-md-6 mb-4">
                <x-kpi-card 
                    title="Marge brute" 
                    value="{{ number_format($kpis['finances']['marge_brute'], 0, ',', ' ') }} FCFA" 
                    icon="fa-chart-line" 
                    color="{{ $kpis['finances']['taux_marge'] >= 20 ? 'success' : ($kpis['finances']['taux_marge'] >= 10 ? 'warning' : 'danger') }}"
                    :details="[
                        'Revenus: ' . number_format($kpis['finances']['revenus'], 0, ',', ' ') . ' FCFA',
                        'Dépenses: ' . number_format($kpis['finances']['depenses'], 0, ',', ' ') . ' FCFA',
                        'Taux: ' . $kpis['finances']['taux_marge'] . '%'
                    ]"
                />
            </div>
        </div>
    </x-slot>

    <!-- Graphiques -->
    <div class="row">
        <!-- Opérations mensuelles -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Évolution des opérations
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="operationsChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenus vs Dépenses -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line text-success me-2"></i>
                        Revenus vs Dépenses
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="financesChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Performance véhicules -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-car text-info me-2"></i>
                        État du parc automobile
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="vehiclesChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Stock par entrepôt -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-warehouse text-warning me-2"></i>
                        Répartition du stock
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="stockChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Activités récentes -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-history text-secondary me-2"></i>
                        Activités récentes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($recent_activities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $activity['color'] }}">
                                    <i class="fas {{ $activity['icon'] }} text-white"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ $activity['title'] }}</h6>
                                    <p class="text-muted mb-1">{{ $activity['description'] }}</p>
                                    <small class="text-muted">{{ $activity['time'] }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

@endsection

@push('styles')
<style>
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
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique des opérations mensuelles
const operationsCtx = document.getElementById('operationsChart').getContext('2d');
new Chart(operationsCtx, {
    type: 'bar',
    data: {
        labels: @json($charts['operations_mensuelles']->pluck('month')),
        datasets: [{
            label: 'Opérations',
            data: @json($charts['operations_mensuelles']->pluck('count')),
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Graphique des finances
const financesCtx = document.getElementById('financesChart').getContext('2d');
new Chart(financesCtx, {
    type: 'line',
    data: {
        labels: @json($charts['revenus_depenses']['revenus']->pluck('month')),
        datasets: [{
            label: 'Revenus',
            data: @json($charts['revenus_depenses']['revenus']->pluck('amount')),
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.4
        }, {
            label: 'Dépenses',
            data: @json($charts['revenus_depenses']['depenses']->pluck('amount')),
            borderColor: 'rgba(255, 99, 132, 1)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Graphique des véhicules
const vehiclesCtx = document.getElementById('vehiclesChart').getContext('2d');
new Chart(vehiclesCtx, {
    type: 'doughnut',
    data: {
        labels: @json($charts['performance_vehicules']->pluck('statut')),
        datasets: [{
            data: @json($charts['performance_vehicules']->pluck('count')),
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(220, 53, 69, 0.8)',
                'rgba(108, 117, 125, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Graphique du stock
const stockCtx = document.getElementById('stockChart').getContext('2d');
new Chart(stockCtx, {
    type: 'pie',
    data: {
        labels: @json($charts['stock_par_entrepot']->pluck('warehouse.name')),
        datasets: [{
            data: @json($charts['stock_par_entrepot']->pluck('total_stock')),
            backgroundColor: [
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

function changeFilters() {
    const period = document.getElementById('periodFilter').value;
    const agence = document.getElementById('agenceFilter').value;
    window.location.href = `{{ route('dashboard') }}?period=${period}&agence=${agence}`;
}
</script>
@endpush
