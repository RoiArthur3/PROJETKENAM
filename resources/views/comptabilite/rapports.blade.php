@extends('layouts.app')

@section('title', 'Rapports financiers')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Rapports Financiers
                    </h1>
                    <p class="text-muted">Génération et suivi des rapports financiers</p>
                </div>
                <div>
                    <button class="btn btn-outline-success me-2" onclick="planifierRapport()">
                        <i class="fas fa-clock me-2"></i>Planifier
                    </button>
                    <button class="btn btn-outline-primary me-2" onclick="exporterTousRapports()">
                        <i class="fas fa-download me-2"></i>Export Global
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
                                Rapports Générés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rapports-generes">
                                45
                            </div>
                            <div class="text-xs text-muted">Total</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
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
                                Ce Mois
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rapports-mois">
                                12
                            </div>
                            <div class="text-xs text-muted">Rapports</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rapports-attente">
                                3
                            </div>
                            <div class="text-xs text-muted">Planifiés</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
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
                                Dernier Rapport
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="dernier-rapport">
                                15/01
                            </div>
                            <div class="text-xs text-muted">Janvier 2024</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Types de rapports -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-th-large me-2"></i>Types de Rapports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-left-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                                    <h5>Chiffre d'Affaires</h5>
                                    <p class="text-muted">Analyse des ventes et revenus</p>
                                    <button class="btn btn-primary" onclick="genererRapport('chiffre_affaires')">
                                        <i class="fas fa-plus me-2"></i>Générer
                                    </button>
                                    <button class="btn btn-outline-info ms-2" onclick="voirRapport('chiffre_affaires')">
                                        <i class="fas fa-eye me-2"></i>Voir
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-left-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-money-bill-wave fa-3x text-success mb-3"></i>
                                    <h5>Dépenses</h5>
                                    <p class="text-muted">Suivi des charges et dépenses</p>
                                    <button class="btn btn-success" onclick="genererRapport('depenses')">
                                        <i class="fas fa-plus me-2"></i>Générer
                                    </button>
                                    <button class="btn btn-outline-info ms-2" onclick="voirRapport('depenses')">
                                        <i class="fas fa-eye me-2"></i>Voir
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-left-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-pie fa-3x text-info mb-3"></i>
                                    <h5>Bénéfices</h5>
                                    <p class="text-muted">Analyse de rentabilité</p>
                                    <button class="btn btn-info" onclick="genererRapport('benefices')">
                                        <i class="fas fa-plus me-2"></i>Générer
                                    </button>
                                    <button class="btn btn-outline-info ms-2" onclick="voirRapport('benefices')">
                                        <i class="fas fa-eye me-2"></i>Voir
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-left-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-wallet fa-3x text-warning mb-3"></i>
                                    <h5>Trésorerie</h5>
                                    <p class="text-muted">Flux de trésorerie</p>
                                    <button class="btn btn-warning" onclick="genererRapport('tresorerie')">
                                        <i class="fas fa-plus me-2"></i>Générer
                                    </button>
                                    <button class="btn btn-outline-info ms-2" onclick="voirRapport('tresorerie')">
                                        <i class="fas fa-eye me-2"></i>Voir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Génération rapide -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Génération Rapide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Type de Rapport</label>
                            <select class="form-select" id="rapport-type">
                                <option value="">Sélectionner...</option>
                                <option value="chiffre_affaires">Chiffre d'Affaires</option>
                                <option value="depenses">Dépenses</option>
                                <option value="benefices">Bénéfices</option>
                                <option value="tresorerie">Trésorerie</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Période</label>
                            <select class="form-select" id="rapport-periode">
                                <option value="jour">Aujourd'hui</option>
                                <option value="semaine">Cette semaine</option>
                                <option value="mois" selected>Ce mois</option>
                                <option value="trimestre">Ce trimestre</option>
                                <option value="annee">Cette année</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Format</label>
                            <select class="form-select" id="rapport-format">
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button class="btn btn-primary" onclick="genererRapportRapide()">
                                    <i class="fas fa-cog me-2"></i>Générer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rapports récents -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Rapports Récents
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Période</th>
                                    <th>Date Génération</th>
                                    <th>Format</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-primary">Chiffre d'Affaires</span></td>
                                    <td>Juin 2024</td>
                                    <td>15/01/2024 14:30</td>
                                    <td><span class="badge bg-secondary">PDF</span></td>
                                    <td><span class="badge bg-success">Terminé</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="telechargerRapport('ca-juin-2024.pdf')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="visualiserRapport('ca-juin-2024.pdf')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="supprimerRapport(1)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">Dépenses</span></td>
                                    <td>Juin 2024</td>
                                    <td>14/01/2024 10:15</td>
                                    <td><span class="badge bg-secondary">Excel</span></td>
                                    <td><span class="badge bg-success">Terminé</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="telechargerRapport('depenses-juin-2024.xlsx')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="visualiserRapport('depenses-juin-2024.xlsx')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="supprimerRapport(2)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info">Bénéfices</span></td>
                                    <td>Mai 2024</td>
                                    <td>12/01/2024 16:45</td>
                                    <td><span class="badge bg-secondary">PDF</span></td>
                                    <td><span class="badge bg-warning">En cours</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="telechargerRapport('benefices-mai-2024.pdf')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="visualiserRapport('benefices-mai-2024.pdf')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="supprimerRapport(3)">
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

    <!-- Graphiques -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Rapports par Type
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="rapportsTypeChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Rapports par Période
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="rapportsPeriodeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Charger les statistiques
    loadStatistics();

    // Initialiser les graphiques
    initCharts();
});

function loadStatistics() {
    $.get("/comptabilite/rapports/statistics", function(data) {
        $('#rapports-generes').text(data.rapports_generes.toLocaleString('fr-FR'));
        $('#rapports-mois').text(data.rapports_mois.toLocaleString('fr-FR'));
        $('#rapports-attente').text(data.rapports_en_attente.toLocaleString('fr-FR'));
        $('#dernier-rapport').text(data.dernier_rapport);
    });
}

function initCharts() {
    // Graphique des rapports par type
    const typeCtx = document.getElementById('rapportsTypeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'bar',
        data: {
            labels: ['Chiffre d\'Affaires', 'Dépenses', 'Bénéfices', 'Trésorerie'],
            datasets: [{
                label: 'Nombre de rapports',
                data: [15, 12, 10, 8],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
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

    // Graphique des rapports par période
    const periodeCtx = document.getElementById('rapportsPeriodeChart').getContext('2d');
    new Chart(periodeCtx, {
        type: 'pie',
        data: {
            labels: ['Journalier', 'Hebdomadaire', 'Mensuel', 'Trimestriel', 'Annuel'],
            datasets: [{
                data: [5, 8, 20, 7, 5],
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
}

function genererRapport(type) {
    const periode = prompt('Période du rapport (ex: 2024-01):');
    if (!periode) return;

    const format = prompt('Format (pdf/excel/csv):', 'pdf');
    if (!format) return;

    $.ajax({
        url: '/comptabilite/rapports/generate',
        method: 'POST',
        data: {
            type: type,
            periode: periode,
            format: format
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            alert(response.message);
            setTimeout(() => {
                window.open(response.data.file_url, '_blank');
            }, 1000);
            loadStatistics();
        },
        error: function(xhr) {
            alert('Erreur lors de la génération du rapport');
        }
    });
}

function genererRapportRapide() {
    const type = $('#rapport-type').val();
    const periode = $('#rapport-periode').val();
    const format = $('#rapport-format').val();

    if (!type) {
        alert('Veuillez sélectionner un type de rapport');
        return;
    }

    $.ajax({
        url: '/comptabilite/rapports/generate',
        method: 'POST',
        data: {
            type: type,
            periode: periode,
            format: format
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            alert(response.message);
            setTimeout(() => {
                window.open(response.data.file_url, '_blank');
            }, 1000);
            loadStatistics();
        },
        error: function(xhr) {
            alert('Erreur lors de la génération du rapport');
        }
    });
}

function voirRapport(type) {
    // Rediriger vers la page du rapport
    window.location.href = `/comptabilite/rapports/${type}`;
}

function telechargerRapport(filename) {
    window.open(`/exports/rapports/${filename}`, '_blank');
}

function visualiserRapport(filename) {
    window.open(`/rapports/view/${filename}`, '_blank');
}

function supprimerRapport(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce rapport ?')) {
        $.ajax({
            url: `/comptabilite/rapports/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Rapport supprimé avec succès');
                location.reload();
            },
            error: function(xhr) {
                alert('Erreur lors de la suppression');
            }
        });
    }
}

function planifierRapport() {
    const type = prompt('Type de rapport:');
    const periode = prompt('Période:');
    const frequence = prompt('Fréquence (quotidien/hebdomadaire/mensuel):');

    if (!type || !periode || !frequence) return;

    $.ajax({
        url: '/comptabilite/rapports/planifier',
        method: 'POST',
        data: {
            type: type,
            periode: periode,
            frequence: frequence
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            alert(response.message);
            loadStatistics();
        },
        error: function(xhr) {
            alert('Erreur lors de la planification');
        }
    });
}

function exporterTousRapports() {
    const format = prompt('Format d\'export (pdf/excel/zip):', 'zip');
    if (!format) return;

    window.open(`/comptabilite/rapports/export-all?format=${format}`, '_blank');
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
