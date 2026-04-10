@extends('layouts.app')

@section('title', 'Tableau de bord Comptabilité | KENAM SERVICES')

@section('content')
<x-dashboard-layout
    title="Tableau de bord Comptabilité"
    icon="fa-calculator"
>

    <x-slot name="kpis">
        <x-kpi-card
            title="Revenus du mois"
            :value="number_format(0, 0, ',', ' ')"
            icon="fa-calendar"
            color="primary"
            subtitle="FCFA"
        />
        <x-kpi-card
            title="Dépenses du mois"
            :value="number_format(0, 0, ',', ' ')"
            icon="fa-euro-sign"
            color="success"
            subtitle="FCFA"
        />
        <x-kpi-card
            title="Solde"
            :value="number_format(0, 0, ',', ' ')"
            icon="fa-wallet"
            color="info"
            subtitle="FCFA"
        />
    </x-slot>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Flux de trésorerie</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="cashFlowChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition des dépenses</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="expensePieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Fournisseurs
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Salaires
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Autres
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dernières transactions</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Référence</th>
                                    <th>Libellé</th>
                                    <th>Montant</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">Aucune transaction récente</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-dashboard-layout>

@push('scripts')
<!-- Page level plugins -->
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

<!-- Page level custom scripts -->
<script>
// Cash Flow Chart
var ctx = document.getElementById("cashFlowChart");
var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc"],
        datasets: [{
            label: "Revenus",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 12
                }
            },
            y: {
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return value.toLocaleString('fr-FR') + ' FCFA';
                    }
                },
                grid: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            },
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(context) {
                        var label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            label += context.parsed.y.toLocaleString('fr-FR') + ' FCFA';
                        }
                        return label;
                    }
                }
            }
        }
    }
});

// Expense Pie Chart
var ctx = document.getElementById("expensePieChart");
var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ["Fournisseurs", "Salaires", "Autres"],
        datasets: [{
            data: [0, 0, 0],
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, data) {
                    var label = data.labels[tooltipItem.index] || '';
                    if (label) {
                        label += ': ';
                    }
                    var value = data.datasets[0].data[tooltipItem.index];
                    if (value) {
                        label += value.toLocaleString('fr-FR') + ' FCFA';
                    }
                    return label;
                }
            }
        },
        legend: {
            display: false
        },
        cutout: '80%',
    },
});
</script>
@endpush
@endsection
