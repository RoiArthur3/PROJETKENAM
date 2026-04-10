@extends('layouts.app')

@section('title', 'Classification des transactions')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-tags me-2 text-primary"></i>
                        Classification des Transactions
                    </h1>
                    <p class="text-muted">Distinction Dépenses vs Charges et catégorisation automatique</p>
                </div>
                <div>
                    <button class="btn btn-outline-success me-2" onclick="classificationAutomatique()">
                        <i class="fas fa-robot me-2"></i>Classification Auto
                    </button>
                    <button class="btn btn-outline-primary me-2" onclick="exporterClassification()">
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
                                Total Transactions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-transactions">
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
                                Taux Classification
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="taux-classification">
                                91.0%
                            </div>
                            <div class="text-xs text-muted">142/156 classifiées</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
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
                                Dépenses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="depenses">
                                89
                            </div>
                            <div class="text-xs text-muted">Non déductibles</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
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
                                Charges
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="charges">
                                67
                            </div>
                            <div class="text-xs text-muted">Déductibles</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
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
                                <option value="charge">Charges</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Catégorie</label>
                            <select class="form-select" id="categorie" onchange="appliquerFiltres()">
                                <option value="toutes">Toutes</option>
                                <option value="exploitation">Charges d'exploitation</option>
                                <option value="financieres">Charges financières</option>
                                <option value="exceptionnelles">Charges exceptionnelles</option>
                                <option value="personnel">Charges de personnel</option>
                                <option value="externes">Charges externes</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" id="statut" onchange="appliquerFiltres()">
                                <option value="tous">Tous</option>
                                <option value="classifié">Classifié</option>
                                <option value="non classifié">Non classifié</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button class="btn btn-outline-primary" onclick="reinitialiserFiltres()">
                                <i class="fas fa-redo me-2"></i>Réinitialiser
                            </button>
                            <button class="btn btn-outline-success ms-2" onclick="ajouterRegle()">
                                <i class="fas fa-plus me-2"></i>Ajouter une règle
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des transactions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table me-2"></i>Transactions à Classifier
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="transactionsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Libellé</th>
                                    <th>Montant</th>
                                    <th>Fournisseur</th>
                                    <th>Type</th>
                                    <th>Catégorie</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="transactionsBody">
                                <!-- Les transactions seront chargées via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Répartition par Catégorie
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Dépenses vs Charges
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="depensesChargesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Règles de classification automatique -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>Règles de Classification Automatique
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 text-muted">Liste des règles actives</h6>
                        <a href="{{ route('comptabilite.classification.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Nouvelle Règle
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mot-clé</th>
                                    <th>Type</th>
                                    <th>Catégorie</th>
                                    <th>Sous-catégorie</th>
                                    <th>Priorité</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>loyer</td>
                                    <td><span class="badge bg-warning">Charge</span></td>
                                    <td>Exploitation</td>
                                    <td>613 - Loyers</td>
                                    <td>Haute</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="modifierRegle(1)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="supprimerRegle(1)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>carburant</td>
                                    <td><span class="badge bg-info">Dépense</span></td>
                                    <td>Transport</td>
                                    <td>624 - Transports</td>
                                    <td>Moyenne</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="modifierRegle(2)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="supprimerRegle(2)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>salaire</td>
                                    <td><span class="badge bg-warning">Charge</span></td>
                                    <td>Personnel</td>
                                    <td>641 - Salaires</td>
                                    <td>Haute</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="modifierRegle(3)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="supprimerRegle(3)">
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
</div>

<script>
$(document).ready(function() {
    // Charger les statistiques
    loadStatistics();

    // Charger les transactions
    loadTransactions();

    // Initialiser les graphiques
    initCharts();
});

function loadStatistics() {
    $.get("/comptabilite/classification/statistics", function(data) {
        $('#total-transactions').text(data.total_transactions.toLocaleString('fr-FR'));
        $('#taux-classification').text(data.taux_classification + '%');
        $('#depenses').text(data.depenses_vs_charges.depenses.toLocaleString('fr-FR'));
        $('#charges').text(data.depenses_vs_charges.charges.toLocaleString('fr-FR'));
    });
}

function loadTransactions() {
    const periode = $('#periode').val();
    const type = $('#type').val();
    const categorie = $('#categorie').val();
    const statut = $('#statut').val();

    $.get("/comptabilite/classification/transactions", {
        periode: periode,
        type: type,
        categorie: categorie,
        statut: statut
    }, function(data) {
        let html = '';
        data.forEach(function(transaction) {
            const statutBadge = transaction.statut === 'classifié' ?
                '<span class="badge bg-success">Classifié</span>' :
                '<span class="badge bg-warning">Non classifié</span>';

            const typeBadge = transaction.type === 'charge' ?
                '<span class="badge bg-warning">Charge</span>' :
                '<span class="badge bg-info">Dépense</span>';

            html += `
                <tr>
                    <td>${transaction.date}</td>
                    <td>${transaction.libelle}</td>
                    <td>${transaction.montant.toLocaleString('fr-FR')} FCFA</td>
                    <td>${transaction.fournisseur}</td>
                    <td>${typeBadge}</td>
                    <td>${transaction.categorie}</td>
                    <td>${statutBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="classifierTransaction(${transaction.id})">
                            <i class="fas fa-tag"></i> Classifier
                        </button>
                        <button class="btn btn-sm btn-outline-info" onclick="voirDetails(${transaction.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        $('#transactionsBody').html(html);
    });
}

function initCharts() {
    // Graphique des catégories
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    new Chart(categoriesCtx, {
        type: 'pie',
        data: {
            labels: ['Exploitation', 'Financières', 'Exceptionnelles', 'Personnel', 'Externes'],
            datasets: [{
                data: [45, 23, 12, 38, 24],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
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

    // Graphique Dépenses vs Charges
    const depensesChargesCtx = document.getElementById('depensesChargesChart').getContext('2d');
    new Chart(depensesChargesCtx, {
        type: 'bar',
        data: {
            labels: ['Dépenses', 'Charges'],
            datasets: [{
                label: 'Nombre de transactions',
                data: [89, 67],
                backgroundColor: ['#36b9cc', '#f6c23e']
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
}

function appliquerFiltres() {
    loadTransactions();
}

function reinitialiserFiltres() {
    $('#periode').val('mois');
    $('#type').val('tous');
    $('#categorie').val('toutes');
    $('#statut').val('tous');
    loadTransactions();
}

function classifierTransaction(id) {
    // Rediriger vers la page de classification
    window.location.href = `/comptabilite/classification/${id}/classifier`;
}

function voirDetails(id) {
    // Rediriger vers la page de détails
    window.location.href = `/comptabilite/classification/${id}/details`;
}

function classificationAutomatique() {
    $.ajax({
        url: '/comptabilite/classification/automatique',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            alert(response.message);
            loadStatistics();
            loadTransactions();
        },
        error: function(xhr) {
            alert('Erreur lors de la classification automatique');
        }
    });
}

function exporterClassification() {
    const format = prompt('Format d\'export (pdf/excel):');
    if (!format) return;

    $.ajax({
        url: '/comptabilite/classification/export',
        method: 'POST',
        data: { format: format },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            alert(response.message);
            window.open(response.file_url, '_blank');
        },
        error: function(xhr) {
            alert('Erreur lors de l\'export');
        }
    });
}

function ajouterRegle() {
    // Rediriger vers la page d'ajout de règle
    window.location.href = `/comptabilite/classification/regles/create`;
}

function modifierRegle(id) {
    // Rediriger vers la page de modification de règle
    window.location.href = `/comptabilite/classification/regles/${id}/edit`;
}

function supprimerRegle(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette règle ?')) {
        $.ajax({
            url: `/comptabilite/classification/regles/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Règle supprimée avec succès');
                location.reload();
            },
            error: function(xhr) {
                alert('Erreur lors de la suppression');
            }
        });
    }
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
