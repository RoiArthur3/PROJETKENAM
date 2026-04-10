@extends('layouts.app')

@section('title', 'Commercial - Statistiques | KENAM SERVICES')

@section('content')
<x-dashboard-layout title="Statistiques Commerciales" icon="fa-chart-line" subtitle="Analyse des performances commerciales et tendances">
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="CA Total"
            value="45.2M FCFA"
            icon="fa-euro-sign"
            color="primary"
            subtitle="+12% vs année dernière"
            trend="up"
            trendValue="+12%"
        />

        <x-kpi-card
            title="Nouveaux Clients"
            value="28"
            icon="fa-user-plus"
            color="success"
            subtitle="Ce trimestre"
        />

        <x-kpi-card
            title="Taux Conversion"
            value="24%"
            icon="fa-percentage"
            color="warning"
            subtitle="Devis vers contrats"
        />

        <x-kpi-card
            title="Panier Moyen"
            value="1.8M FCFA"
            icon="fa-shopping-cart"
            color="info"
            subtitle="Par commande"
        />
    </x-slot>

    <!-- Graphiques et Analyses -->
    <div class="row mb-4">
        <!-- Évolution du CA -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area mr-2"></i>Évolution du Chiffre d'Affaires
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:350px;">
                        <canvas id="caEvolutionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition par secteur -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-2"></i>Répartition par Secteur
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="secteursChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux de synthèse -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy mr-2"></i>Top 5 Clients par CA
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Secteur</th>
                                    <th>CA Total</th>
                                    <th>Évolution</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Tech Solutions</strong></td>
                                    <td>Technologie</td>
                                    <td class="text-success fw-bold">8.5M FCFA</td>
                                    <td><span class="badge bg-success">+15%</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Construction Moderne</strong></td>
                                    <td>BTP</td>
                                    <td class="text-success fw-bold">6.2M FCFA</td>
                                    <td><span class="badge bg-success">+8%</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Logistics Pro</strong></td>
                                    <td>Transport</td>
                                    <td class="text-success fw-bold">5.8M FCFA</td>
                                    <td><span class="badge bg-warning">+3%</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Industries ABC</strong></td>
                                    <td>Industrie</td>
                                    <td class="text-success fw-bold">4.9M FCFA</td>
                                    <td><span class="badge bg-success">+12%</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Commerce Plus</strong></td>
                                    <td>Commerce</td>
                                    <td class="text-success fw-bold">4.1M FCFA</td>
                                    <td><span class="badge bg-danger">-2%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar mr-2"></i>Performance par Commercial
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Commercial</th>
                                    <th>Objectif</th>
                                    <th>Réalisé</th>
                                    <th>Taux</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Jean Dupont</strong></td>
                                    <td>15M FCFA</td>
                                    <td class="text-success fw-bold">16.8M FCFA</td>
                                    <td class="text-success fw-bold">112%</td>
                                    <td><span class="badge bg-success">Excellent</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Marie Curie</strong></td>
                                    <td>12M FCFA</td>
                                    <td class="text-success fw-bold">13.2M FCFA</td>
                                    <td class="text-success fw-bold">110%</td>
                                    <td><span class="badge bg-success">Excellent</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Paul Martin</strong></td>
                                    <td>10M FCFA</td>
                                    <td class="text-warning fw-bold">9.2M FCFA</td>
                                    <td class="text-warning fw-bold">92%</td>
                                    <td><span class="badge bg-warning">Bon</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Sophie Dubois</strong></td>
                                    <td>8M FCFA</td>
                                    <td class="text-danger fw-bold">6.1M FCFA</td>
                                    <td class="text-danger fw-bold">76%</td>
                                    <td><span class="badge bg-danger">À améliorer</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicateurs de performance -->
    <div class="row mb-4">
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tachometer-alt mr-2"></i>Indicateurs Clés de Performance
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="h4 text-success font-weight-bold">24%</div>
                                <div class="text-muted small">Taux de conversion devis</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 24%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="h4 text-info font-weight-bold">8.5j</div>
                                <div class="text-muted small">Délai moyen de clôture</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: 75%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="h4 text-primary font-weight-bold">156</div>
                                <div class="text-muted small">Contacts qualifiés/mois</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: 85%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="h4 text-warning font-weight-bold">4.2/5</div>
                                <div class="text-muted small">Note moyenne satisfaction</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 84%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

@push('scripts')
<script src="{{ asset('js/chart.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour l'évolution du CA
    const caData = {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
        datasets: [{
            label: 'CA Mensuel (FCFA)',
            data: [3200000, 3500000, 3800000, 4200000, 3900000, 4100000, 4500000, 4800000, 4600000, 5000000, 5200000, 5500000],
            borderColor: 'rgb(78, 115, 223)',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.4,
            fill: true
        }]
    };

    // Graphique évolution CA
    const caCtx = document.getElementById('caEvolutionChart').getContext('2d');
    new Chart(caCtx, {
        type: 'line',
        data: caData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return (value / 1000000) + 'M FCFA';
                        }
                    }
                }
            }
        }
    });

    // Données pour la répartition par secteur
    const secteursData = {
        labels: ['Technologie', 'BTP', 'Transport', 'Industrie', 'Commerce', 'Services'],
        datasets: [{
            data: [28, 22, 18, 15, 12, 5],
            backgroundColor: [
                'rgb(78, 115, 223)',
                'rgb(28, 200, 138)',
                'rgb(246, 194, 62)',
                'rgb(23, 162, 184)',
                'rgb(220, 53, 69)',
                'rgb(108, 117, 125)'
            ],
            borderWidth: 2
        }]
    };

    // Graphique répartition par secteur
    const secteursCtx = document.getElementById('secteursChart').getContext('2d');
    new Chart(secteursCtx, {
        type: 'doughnut',
        data: secteursData,
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
@endpush
@endsection
