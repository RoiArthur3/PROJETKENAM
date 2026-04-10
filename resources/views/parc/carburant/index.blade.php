@extends('layouts.app')

@section('title', 'Gestion du Carburant - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion du Carburant</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="#">Parc</a></li>
                <li class="breadcrumb-item active" aria-current="page">Carburant</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Suivi des consommations</h6>
            <button class="btn btn-primary btn-sm">
                <i class="fas fa-plus fa-sm"></i> Nouvelle entrée
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="carburantTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Véhicule</th>
                            <th>Type carburant</th>
                            <th>Quantité (L)</th>
                            <th>Prix/L</th>
                            <th>Montant</th>
                            <th>Kilométrage</th>
                            <th>Conducteur</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Les données seront chargées via JavaScript -->
                        <tr>
                            <td colspan="9" class="text-center">Aucune donnée disponible</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques mensuelles</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyFuelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition par véhicule</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="vehicleFuelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour ajouter/éditer une entrée carburant -->
<div class="modal fade" id="fuelEntryModal" tabindex="-1" role="dialog" aria-labelledby="fuelEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fuelEntryModalLabel">Nouvelle entrée carburant</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="fuelEntryForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vehicle_id">Véhicule <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="vehicle_id" name="vehicle_id" required>
                                    <option value="">Sélectionner un véhicule</option>
                                    <!-- Options will be loaded via JavaScript -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fuel_type">Type de carburant <span class="text-danger">*</span></label>
                                <select class="form-control" id="fuel_type" name="fuel_type" required>
                                    <option value="">Sélectionner</option>
                                    <option value="diesel">Diesel</option>
                                    <option value="essence">Essence</option>
                                    <option value="gpl">GPL</option>
                                    <option value="electrique">Électrique</option>
                                    <option value="hybride">Hybride</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quantity">Quantité (L) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="quantity" name="quantity" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="price_per_liter">Prix au litre (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" step="0.001" class="form-control" id="price_per_liter" name="price_per_liter" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="odometer">Kilométrage <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="odometer" name="odometer" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="driver_id">Conducteur <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="driver_id" name="driver_id" required>
                                    <option value="">Sélectionner un conducteur</option>
                                    <!-- Options will be loaded via JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="invoice_number">N° de facture</label>
                                <input type="text" class="form-control" id="invoice_number" name="invoice_number">
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
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Initialize DataTable
        var table = $('#carburantTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("api.parc.carburant.datatable") }}',
                type: 'GET'
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'vehicle', name: 'vehicle' },
                { data: 'fuel_type', name: 'fuel_type' },
                { data: 'quantity', name: 'quantity' },
                { data: 'price_per_liter', name: 'price_per_liter' },
                { data: 'total_amount', name: 'total_amount' },
                { data: 'odometer', name: 'odometer' },
                { data: 'driver', name: 'driver' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json'
            }
        });

        // Handle new fuel entry button click
        $('button[data-target="#fuelEntryModal"]').on('click', function() {
            $('#fuelEntryModal').modal('show');
        });

        // Initialize charts
        var monthlyFuelCtx = document.getElementById('monthlyFuelChart');
        var monthlyFuelChart = new Chart(monthlyFuelCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [{
                    label: 'Consommation (L)',
                    data: [65, 59, 80, 81, 56, 55, 40, 45, 50, 60, 70, 75],
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: 'start',
                }]
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
                            maxTicksLimit: 12
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value, index, values) {
                                return value + ' L';
                            }
                        },
                        gridLines: {
                            color: 'rgb(234, 236, 244)',
                            zeroLineColor: 'rgb(234, 236, 244)',
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: 'rgb(255,255,255)',
                    bodyFontColor: '#858796',
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
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ' + tooltipItem.yLabel + ' L';
                        }
                    }
                }
            }
        });

        // Vehicle fuel distribution chart
        var vehicleFuelCtx = document.getElementById('vehicleFuelChart');
        var vehicleFuelChart = new Chart(vehicleFuelCtx, {
            type: 'doughnut',
            data: {
                labels: ['Véhicule 1', 'Véhicule 2', 'Véhicule 3', 'Véhicule 4', 'Véhicule 5'],
                datasets: [{
                    data: [35, 25, 20, 15, 5],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                    hoverBorderColor: 'rgba(234, 236, 244, 1)',
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: 'rgb(255,255,255)',
                    bodyFontColor: '#858796',
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 15,
                        padding: 20
                    }
                },
                cutoutPercentage: 70,
            },
        });

        // Form submission
        $('#fuelEntryForm').on('submit', function(e) {
            e.preventDefault();
            // Add your form submission logic here
            alert('Fonctionnalité à implémenter');
        });
    });
</script>
@endpush
