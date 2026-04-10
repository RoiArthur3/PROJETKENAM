@extends('layouts.app')

@section('title', 'Reporting et Analytics - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Reporting et Analytics</h1>
        </div>
    </div>

    <!-- Cartes de Résumé Global -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Opérations Ce Mois</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">120</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Factures Payées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">92%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                Productivité RH</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">88%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Stocks Critiques</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">15</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation des Rapports -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Accès aux Rapports Détaillés</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-primary btn-block" disabled title="Fonctionnalité en développement">
                                <i class="fas fa-clipboard-list fa-fw"></i> Opérations
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-success btn-block" disabled title="Fonctionnalité en développement">
                                <i class="fas fa-money-bill-wave fa-fw"></i> Finances
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-info btn-block" disabled title="Fonctionnalité en développement">
                                <i class="fas fa-users fa-fw"></i> Ressources Humaines
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-warning btn-block" disabled title="Fonctionnalité en développement">
                                <i class="fas fa-chart-bar fa-fw"></i> Export Global
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques de Synthèse -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Évolution Mensuelle</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition par Service</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="serviceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes et Recommandations -->
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Alertes et Recommandations</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 15 articles en stock critique. Réapprovisionnement recommandé.
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 3 opérations en attente depuis plus de 7 jours.
                    </div>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Taux de recouvrement des factures : 92% ce mois-ci.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique mensuel
    var ctx1 = document.getElementById('monthlyChart').getContext('2d');
    var monthlyChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Opérations',
                data: [65, 78, 90, 81, 95, 120],
                borderColor: 'rgba(40, 167, 69, 1)',
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                fill: true
            }]
        }
    });

    // Graphique circulaire par service
    var ctx2 = document.getElementById('serviceChart').getContext('2d');
    var serviceChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Logistique', 'Entretien', 'Comptabilité', 'RH'],
            datasets: [{
                data: [45, 30, 15, 10],
                backgroundColor: ['#28A745', '#FFC107', '#17A2B8', '#DC3545']
            }]
        }
    });
</script>
@endsection
