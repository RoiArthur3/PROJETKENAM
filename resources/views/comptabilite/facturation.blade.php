@extends('layouts.app')

@section('title', 'Facturation')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-file-invoice me-2 text-primary"></i>
                        Gestion de la Facturation
                    </h1>
                    <p class="text-muted">Créer et gérer les factures clients et fournisseurs</p>
                </div>
                <div>
                    <button class="btn btn-outline-secondary me-2" onclick="exportExcel()">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                    <button class="btn btn-outline-danger me-2" onclick="exportPDF()">
                        <i class="fas fa-file-pdf me-2"></i>Export PDF
                    </button>
                    <a href="{{ route('comptabilite.facturation.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouvelle Facture
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Factures
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-factures">
                                {{ $stats['total_factures'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Montant Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="montant-total">
                                {{ number_format($stats['montant_total'] ?? 0, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                En Attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="factures-en-attente">
                                {{ $stats['factures_en_attente'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Retard
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="factures-en-retard">
                                5
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter me-2"></i>Filtres
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="form-label">Période</label>
                            <select id="filtre-periode" class="form-select">
                                <option value="">Toutes les périodes</option>
                                <option value="ce-mois">Ce mois</option>
                                <option value="mois-dernier">Le mois dernier</option>
                                <option value="ce-trimestre">Ce trimestre</option>
                                <option value="cette-annee">Cette année</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Statut</label>
                            <select id="filtre-statut" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="en_attente">En attente</option>
                                <option value="payee">Payée</option>
                                <option value="en_retard">En retard</option>
                                <option value="annulee">Annulée</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Client</label>
                            <select id="filtre-client" class="form-select">
                                <option value="">Tous les clients</option>
                                <option value="1">Client A</option>
                                <option value="2">Client B</option>
                                <option value="3">Client C</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Montant min</label>
                            <input type="number" id="filtre-montant-min" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Montant max</label>
                            <input type="number" id="filtre-montant-max" class="form-control" placeholder="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des factures -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table me-2"></i>Liste des Factures
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="facturesTable">
                            <thead>
                                <tr>
                                    <th>Numéro</th>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Échéance</th>
                                    <th>Montant HT</th>
                                    <th>TVA</th>
                                    <th>Montant TTC</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="#" onclick="showFacture(1)">FAC001</a></td>
                                    <td>Client A</td>
                                    <td>01/01/2024</td>
                                    <td>31/01/2024</td>
                                    <td class="text-end">{{ number_format(10000000, 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format(1800000, 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format(11800000, 0, ',', ' ') }}</td>
                                    <td><span class="badge bg-success">Payée</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editFacture(1)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="showFacture(1)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteFacture(1)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="#" onclick="showFacture(2)">FAC002</a></td>
                                    <td>Client B</td>
                                    <td>05/01/2024</td>
                                    <td>05/02/2024</td>
                                    <td class="text-end">{{ number_format(8000000, 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format(1440000, 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format(9440000, 0, ',', ' ') }}</td>
                                    <td><span class="badge bg-warning">En attente</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editFacture(2)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="showFacture(2)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteFacture(2)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="#" onclick="showFacture(3)">FAC003</a></td>
                                    <td>Client C</td>
                                    <td>10/01/2024</td>
                                    <td>10/02/2024</td>
                                    <td class="text-end">{{ number_format(12000000, 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format(2160000, 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format(14160000, 0, ',', ' ') }}</td>
                                    <td><span class="badge bg-danger">En retard</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editFacture(3)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="showFacture(3)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteFacture(3)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique des ventes -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Évolution des Ventes
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="ventesChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy me-2"></i>Top Clients
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Client A</h6>
                                <small class="text-muted">15 factures</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ number_format(15000000, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Client B</h6>
                                <small class="text-muted">12 factures</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ number_format(12000000, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Client C</h6>
                                <small class="text-muted">8 factures</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ number_format(8000000, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Charger les statistiques
    loadStatistics();

    // Initialiser DataTable
    $('#facturesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr.json'
        },
        pageLength: 25,
        responsive: true,
        order: [[2, 'desc']]
    });

    // Appliquer les filtres
    $('#filtre-periode, #filtre-statut, #filtre-client, #filtre-montant-min, #filtre-montant-max').on('change keyup', function() {
        applyFilters();
    });

    // Initialiser le graphique des ventes
    initVentesChart();
});

function loadStatistics() {
    $.get("{{ route('comptabilite.facturation.statistics') }}", function(data) {
        $('#total-factures').text(data.total_factures.toLocaleString('fr-FR'));
        $('#montant-total').text(data.montant_total.toLocaleString('fr-FR') + ' FCFA');
        $('#factures-en-attente').text(data.factures_en_attente.toLocaleString('fr-FR'));
        $('#factures-en-retard').text(data.factures_en_retard.toLocaleString('fr-FR'));
    });
}

function applyFilters() {
    // Logique de filtrage à implémenter
    console.log('Filtres appliqués');
}

function showFacture(id) {
    window.location.href = `/comptabilite/facturation/${id}`;
}

function editFacture(id) {
    window.location.href = `/comptabilite/facturation/${id}/edit`;
}

function deleteFacture(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')) {
        $.ajax({
            url: `/comptabilite/facturation/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Facture supprimée avec succès');
                location.reload();
            },
            error: function(xhr) {
                alert('Erreur lors de la suppression');
            }
        });
    }
}

function exportPDF() {
    const form = $('<form>', {
        method: 'POST',
        action: "{{ route('comptabilite.facturation.export-pdf') }}",
        target: '_blank'
    });

    form.append($('<input>', {
        type: 'hidden',
        name: '_token',
        value: $('meta[name="csrf-token"]').attr('content')
    }));

    $('body').append(form);
    form.submit();
    form.remove();
}

function exportExcel() {
    const form = $('<form>', {
        method: 'POST',
        action: "{{ route('comptabilite.facturation.export-excel') }}",
        target: '_blank'
    });

    form.append($('<input>', {
        type: 'hidden',
        name: '_token',
        value: $('meta[name="csrf-token"]').attr('content')
    }));

    $('body').append(form);
    form.submit();
    form.remove();
}

function initVentesChart() {
    const ctx = document.getElementById('ventesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Ventes (FCFA)',
                data: [12000000, 13500000, 15000000, 14500000, 16000000, 14000000],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR') + ' FCFA';
                        }
                    }
                }
            }
        }
    });
}
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.text-xs {
    font-size: 0.7rem;
}
.card-body {
    padding: 1.25rem;
}
</style>
@endsection
