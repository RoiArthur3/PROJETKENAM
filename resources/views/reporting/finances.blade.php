@extends('layouts.app')

@section('title', 'Rapport Financier - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Rapport Financier</h1>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-md-3">
            <select class="form-select" id="periodFilter">
                <option value="month">Ce Mois</option>
                <option value="quarter">Ce Trimestre</option>
                <option value="year">Cette Année</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-success" onclick="generateReport()">
                <i class="fas fa-chart-bar"></i> Générer Rapport
            </button>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-primary" onclick="exportPDF()">
                <i class="fas fa-download"></i> Exporter PDF
            </button>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-secondary" onclick="exportExcel()">
                <i class="fas fa-file-excel"></i> Exporter Excel
            </button>
        </div>
    </div>

    <!-- KPI Financiers -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Recettes Totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">4 800 000 FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Dépenses Totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">2 500 000 FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
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
                                Bénéfice Net</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">2 300 000 FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                Taux de Recouvrement</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">92%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques Financiers -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Évolution des Recettes et Dépenses</h6>
                </div>
                <div class="card-body">
                    <canvas id="financeChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition des Dépenses</h6>
                </div>
                <div class="card-body">
                    <canvas id="expenseChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Transactions -->
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dernières Transactions</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2023-10-15</td>
                                    <td>Recette</td>
                                    <td>Paiement Facture FAC-2025-001</td>
                                    <td class="text-success">+150 000 FCFA</td>
                                    <td><span class="badge bg-success">Confirmé</span></td>
                                </tr>
                                <tr>
                                    <td>2023-10-14</td>
                                    <td>Dépense</td>
                                    <td>Achat Fournitures</td>
                                    <td class="text-danger">-75 000 FCFA</td>
                                    <td><span class="badge bg-warning">En Attente</span></td>
                                </tr>
                                <tr>
                                    <td>2023-10-13</td>
                                    <td>Recette</td>
                                    <td>Paiement Facture FAC-2025-002</td>
                                    <td class="text-success">+200 000 FCFA</td>
                                    <td><span class="badge bg-success">Confirmé</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique des finances
    var ctx1 = document.getElementById('financeChart').getContext('2d');
    var financeChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Recettes',
                data: [3000000, 3200000, 3500000, 3800000, 4200000, 4800000],
                borderColor: 'rgba(40, 167, 69, 1)',
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                fill: true
            }, {
                label: 'Dépenses',
                data: [1500000, 1600000, 1800000, 2000000, 2200000, 2500000],
                borderColor: 'rgba(220, 53, 69, 1)',
                backgroundColor: 'rgba(220, 53, 69, 0.2)',
                fill: true
            }]
        }
    });

    // Graphique des dépenses
    var ctx2 = document.getElementById('expenseChart').getContext('2d');
    var expenseChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['Fournitures', 'Carburant', 'Maintenance', 'Salaires'],
            datasets: [{
                data: [800000, 600000, 500000, 600000],
                backgroundColor: ['#28A745', '#FFC107', '#17A2B8', '#DC3545']
            }]
        }
    });

    function generateReport() {
        // Logique pour générer le rapport
        alert('Rapport généré !');
    }

    function exportPDF() {
        // Logique pour exporter en PDF
        alert('Export PDF en cours...');
    }

    function exportExcel() {
        // Logique pour exporter en Excel
        alert('Export Excel en cours...');
    }
</script>
@endsection
