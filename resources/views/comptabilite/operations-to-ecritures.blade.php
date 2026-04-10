@extends('layouts.app')

@section('title', 'Transformation Opérations → Écritures')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-exchange-alt me-2 text-primary"></i>
                        Transformation Opérations → Écritures
                    </h1>
                    <p class="text-muted">Conversion des opérations en écritures comptables</p>
                </div>
                <div>
                    <button class="btn btn-outline-success me-2" onclick="transformerSelection()">
                        <i class="fas fa-magic me-2"></i>Transformer Sélection
                    </button>
                    <button class="btn btn-outline-primary me-2" onclick="exporterEcritures()">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                    <a href="{{ route('comptabilite.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
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
                                Total Opérations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-operations">
                                156
                            </div>
                            <div class="text-xs text-muted">Ce mois</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
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
                                En Attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="operations-attente">
                                45
                            </div>
                            <div class="text-xs text-muted">À transformer</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
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
                                Transformées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="operations-transformees">
                                111
                            </div>
                            <div class="text-xs text-muted">Écritures générées</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Taux Transformation
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="taux-transformation">
                                71.2%
                            </div>
                            <div class="text-xs text-muted">Objectif: 85%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter me-2"></i>Filtres et Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Période</label>
                            <select class="form-select" id="periode" onchange="appliquerFiltres()">
                                <option value="jour">Aujourd'hui</option>
                                <option value="semaine">Cette semaine</option>
                                <option value="mois" selected>Ce mois</option>
                                <option value="trimestre">Ce trimestre</option>
                                <option value="annee">Cette année</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" id="type" onchange="appliquerFiltres()">
                                <option value="tous">Tous</option>
                                <option value="depense">Dépenses</option>
                                <option value="recette">Recettes</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" id="statut" onchange="appliquerFiltres()">
                                <option value="tous">Tous</option>
                                <option value="en_attente">En attente</option>
                                <option value="transforme">Transformé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Journal</label>
                            <select class="form-select" id="journal">
                                @if(isset($journaux) && $journaux->count() > 0)
                                    @foreach($journaux as $journal)
                                        <option value="{{ $journal->code }}">{{ $journal->code }} - {{ $journal->libelle }}</option>
                                    @endforeach
                                @else
                                    <option value="AC">Achats</option>
                                    <option value="VT">Ventes</option>
                                    <option value="BQ">Banque</option>
                                    <option value="CA">Caisse</option>
                                    <option value="OD">Opérations Diverses</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button class="btn btn-outline-primary" onclick="reinitialiserFiltres()">
                                <i class="fas fa-redo me-2"></i>Réinitialiser
                            </button>
                            <button class="btn btn-outline-info ms-2" onclick="toutSelectionner()">
                                <i class="fas fa-check-square me-2"></i>Tout sélectionner
                            </button>
                            <button class="btn btn-outline-secondary ms-2" onclick="toutDeselectionner()">
                                <i class="fas fa-square me-2"></i>Tout désélectionner
                            </button>
                            <button class="btn btn-outline-warning ms-2" onclick="previsualiserTransformation()">
                                <i class="fas fa-eye me-2"></i>Prévisualiser
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des opérations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table me-2"></i>Opérations à Transformer
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="operationsTable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                    </th>
                                    <th>Date</th>
                                    <th>Référence</th>
                                    <th>Libellé</th>
                                    <th>Montant</th>
                                    <th>Type</th>
                                    <th>Tiers</th>
                                    <th>Compte Débit</th>
                                    <th>Compte Crédit</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="operationsBody">
                                <!-- Les opérations seront chargées via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan comptable -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-book me-2"></i>Plan Comptable
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <h6>Classe 1 - Capitaux</h6>
                            <div class="small">
                                <div>101 - Capital</div>
                                <div>106 - Réserves</div>
                                <div>120 - Résultat</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <h6>Classe 2 - Immobilisations</h6>
                            <div class="small">
                                <div>211 - Terrains</div>
                                <div>213 - Constructions</div>
                                <div>215 - Matériel</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <h6>Classe 3 - Stocks</h6>
                            <div class="small">
                                <div>311 - Matières premières</div>
                                <div>315 - Produits finis</div>
                                <div>370 - Marchandises</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <h6>Classe 4 - Tiers</h6>
                            <div class="small">
                                <div>401 - Fournisseurs</div>
                                <div>411 - Clients</div>
                                <div>421 - Personnel</div>
                                <div>444 - État</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <h6>Classe 5 - Trésorerie</h6>
                            <div class="small">
                                <div>511 - Valeurs à encaisser</div>
                                <div>514 - Banques</div>
                                <div>531 - Caisse</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <h6>Classe 6-7 - Charges/Produits</h6>
                            <div class="small">
                                <div>601 - Achats MP</div>
                                <div>606 - Achats non stockés</div>
                                <div>701 - Ventes PF</div>
                                <div>707 - Ventes marchandises</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Évolution des Transformations
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Répartition par Type
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="typeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Charger les statistiques
    loadStatistics();

    // Charger les opérations
    loadOperations();

    // Initialiser les graphiques
    initCharts();
});

function loadStatistics() {
    $.get("/comptabilite/operations/statistics", function(data) {
        $('#total-operations').text(data.total_operations.toLocaleString('fr-FR'));
        $('#operations-attente').text(data.operations_en_attente.toLocaleString('fr-FR'));
        $('#operations-transformees').text(data.operations_transformees.toLocaleString('fr-FR'));
        $('#taux-transformation').text(data.taux_transformation + '%');
    });
}

function loadOperations() {
    const periode = $('#periode').val();
    const type = $('#type').val();
    const statut = $('#statut').val();

    $.get("/comptabilite/operations/data", {
        periode: periode,
        type: type,
        statut: statut
    }, function(data) {
        let html = '';
        data.forEach(function(operation) {
            const statutBadge = operation.statut === 'transforme' ?
                '<span class="badge bg-success">Transformé</span>' :
                '<span class="badge bg-warning">En attente</span>';

            const typeBadge = operation.type === 'depense' ?
                '<span class="badge bg-danger">Dépense</span>' :
                '<span class="badge bg-success">Recette</span>';

            const disabled = operation.statut === 'transforme' ? 'disabled' : '';

            html += `
                <tr>
                    <td>
                        <input type="checkbox" class="operation-checkbox" value="${operation.id}" ${disabled}>
                    </td>
                    <td>${operation.date}</td>
                    <td>${operation.reference}</td>
                    <td>${operation.libelle}</td>
                    <td>${operation.montant.toLocaleString('fr-FR')} FCFA</td>
                    <td>${typeBadge}</td>
                    <td>${operation.fournisseur || operation.client || '-'}</td>
                    <td>
                        <select class="form-select form-select-sm compte-debit" data-operation="${operation.id}">
                            <option value="${operation.compte_debit}">${operation.compte_debit}</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm compte-credit" data-operation="${operation.id}">
                            <option value="${operation.compte_credit}">${operation.compte_credit}</option>
                        </select>
                    </td>
                    <td>${statutBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="transformerOperation(${operation.id})" ${disabled}>
                            <i class="fas fa-magic"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info" onclick="voirDetails(${operation.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        $('#operationsBody').html(html);
    });
}

function initCharts() {
    // Graphique d'évolution
    const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
    new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: ['Janvier', 'Février', 'Mars', 'Avril'],
            datasets: [{
                label: 'Opérations transformées',
                data: [45, 38, 42, 31],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Graphique par type
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'pie',
        data: {
            labels: ['Dépenses', 'Recettes'],
            datasets: [{
                data: [89, 67],
                backgroundColor: ['#e74a3b', '#1cc88a']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function appliquerFiltres() {
    loadOperations();
}

function reinitialiserFiltres() {
    $('#periode').val('mois');
    $('#type').val('tous');
    $('#statut').val('tous');
    loadOperations();
}

function toggleSelectAll() {
    const selectAll = $('#selectAll').prop('checked');
    $('.operation-checkbox:not(:disabled)').prop('checked', selectAll);
}

function toutSelectionner() {
    $('.operation-checkbox:not(:disabled)').prop('checked', true);
}

function toutDeselectionner() {
    $('.operation-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false);
            <td>601 - Achats de matières premières</td>
            <td>OP-2024-001 - Achat carburant véhicule</td>
            <td>50 000</td>
            <td>-</td>
        </tr>
        <tr>
            <td>2024-01-15</td>
            <td>401 - Fournisseurs</td>
            <td>OP-2024-001 - Achat carburant véhicule</td>
            <td>-</td>
            <td>50 000</td>
        </tr>
        <tr>
            <td>2024-01-16</td>
            <td>625 - Déplacements, missions et réceptions</td>
            <td>OP-2024-002 - Frais de déplacement</td>
            <td>25 000</td>
            <td>-</td>
        </tr>
        <tr>
            <td>2024-01-16</td>
            <td>401 - Fournisseurs</td>
            <td>OP-2024-002 - Frais de déplacement</td>
            <td>-</td>
            <td>25 000</td>
        </tr>
    `;
}

function transformerOperations() {
    alert('Transformation des opérations en écritures comptables... (à implémenter)');
}

function voirOperation(id) {
    window.location.href = '/operations/' + id;
}

function exporter() {
    alert('Exporter les écritures (à implémenter)');
}
</script>
@endsection
