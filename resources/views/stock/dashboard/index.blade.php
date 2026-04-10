@extends('layouts.app')

@section('title', 'Tableau de Bord Stock - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tableau de Bord Stock</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item active" aria-current="page">Stock</li>
            </ol>
        </nav>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Articles en stock Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Articles en stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">1,245</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Valeur totale Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Valeur totale</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">24,589 FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Articles en alerte Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Articles en alerte</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">24</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commandes en attente Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Commandes en attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">8</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Graphique des mouvements -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Mouvements de stock (6 derniers mois)</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Options :</div>
                            <a class="dropdown-item" href="#">Exporter en PDF</a>
                            <a class="dropdown-item" href="#">Exporter en Excel</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Voir le détail</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="stockMovementChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique des catégories -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition par catégorie</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Options :</div>
                            <a class="dropdown-item" href="#">Exporter en PDF</a>
                            <a class="dropdown-item" href="#">Exporter en Excel</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Pièces détachées
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Fournitures
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Équipements
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Liste des articles en alerte -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Articles en alerte de stock</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Désignation</th>
                                    <th>Stock</th>
                                    <th>Seuil</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>REF-00123</td>
                                    <td>Disque de frein avant droit</td>
                                    <td class="text-danger font-weight-bold">2</td>
                                    <td>5</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">Commander</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>REF-00456</td>
                                    <td>Filtre à huile</td>
                                    <td class="text-warning font-weight-bold">3</td>
                                    <td>10</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">Commander</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>REF-00789</td>
                                    <td>Plaquettes de frein avant</td>
                                    <td class="text-danger font-weight-bold">1</td>
                                    <td>4</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">Commander</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <a href="#" class="btn btn-outline-warning btn-block mt-2">Voir tous les articles en alerte</a>
                </div>
            </div>
        </div>

        <!-- Dernières entrées -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dernières entrées en stock</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Référence</th>
                                    <th>Désignation</th>
                                    <th>Qté</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>24/10/2023</td>
                                    <td>REF-01234</td>
                                    <td>Huile moteur 5W30</td>
                                    <td>20L</td>
                                </tr>
                                <tr>
                                    <td>23/10/2023</td>
                                    <td>REF-05678</td>
                                    <td>Pneus été 205/55 R16</td>
                                    <td>4</td>
                                </tr>
                                <tr>
                                    <td>20/10/2023</td>
                                    <td>REF-09012</td>
                                    <td>Batterie 12V 60Ah</td>
                                    <td>2</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <a href="#" class="btn btn-outline-primary btn-block mt-2">Voir tous les mouvements</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouvelle Entrée -->
<div class="modal fade" id="newEntryModal" tabindex="-1" role="dialog" aria-labelledby="newEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newEntryModalLabel">Nouvelle entrée en stock</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="stockEntryForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="article">Article <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="article" name="article" required>
                                    <option value="">Sélectionner un article</option>
                                    <option value="1">Huile moteur 5W30</option>
                                    <option value="2">Filtre à huile</option>
                                    <option value="3">Plaquettes de frein</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity">Quantité <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reference">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
    }
</style>
@endpush

@push('scripts')
<!-- Page level plugins -->
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Stock Movement Chart
        var ctx = document.getElementById("stockMovementChart");
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["Juil", "Août", "Sep", "Oct", "Nov", "Déc"],
                datasets: [
                    {
                        label: "Entrées",
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
                        data: [150, 180, 210, 190, 230, 250],
                    },
                    {
                        label: "Sorties",
                        lineTension: 0.3,
                        backgroundColor: "rgba(231, 74, 59, 0.05)",
                        borderColor: "rgba(231, 74, 59, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(231, 74, 59, 1)",
                        pointBorderColor: "rgba(231, 74, 59, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(231, 74, 59, 1)",
                        pointHoverBorderColor: "rgba(231, 74, 59, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: [120, 150, 170, 160, 200, 220],
                    }
                ],
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
                    xAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 6
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value, index, values) {
                                return value + ' unités';
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltips: {
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
                }
            }
        });

        // Category Chart
        var ctx2 = document.getElementById("categoryChart");
        var myPieChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ["Pièces détachées", "Fournitures", "Équipements", "Lubrifiants", "Autres"],
                datasets: [{
                    data: [35, 30, 20, 10, 5],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
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
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 70,
            },
        });

        // Form submission
        $('#stockEntryForm').on('submit', function(e) {
            e.preventDefault();
            // Add your form submission logic here
            alert('Fonctionnalité à implémenter');
        });
    });
</script>
@endpush
