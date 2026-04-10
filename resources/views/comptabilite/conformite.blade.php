@extends('layouts.app')

@section('title', 'Conformité fiscale')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-shield-alt me-2 text-primary"></i>
                        Conformité Fiscale
                    </h1>
                    <p class="text-muted">Suivi des obligations fiscales et déclarations</p>
                </div>
                <div>
                    <button class="btn btn-outline-secondary me-2" onclick="exportAllDeclarations()">
                        <i class="fas fa-download me-2"></i>Export Global
                    </button>
                    <a href="{{ route('comptabilite.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                TVA
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="tva-statut">
                                <span class="badge bg-success">A jour</span>
                            </div>
                            <div class="text-xs text-muted">Prochaine: 15/02/2024</div>
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
                                CNSS
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="cnss-statut">
                                <span class="badge bg-success">A jour</span>
                            </div>
                            <div class="text-xs text-muted">Prochaine: 10/02/2024</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Impôt Revenu
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="impot-statut">
                                <span class="badge bg-danger">En retard</span>
                            </div>
                            <div class="text-xs text-muted">Prochaine: 31/03/2024</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
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
                                Taxe Professionnelle
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="taxe-statut">
                                <span class="badge bg-success">A jour</span>
                            </div>
                            <div class="text-xs text-muted">Prochaine: 31/12/2024</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Déclarations fiscales -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-invoice me-2"></i>Déclarations TVA
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Période</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Janvier 2024</td>
                                    <td><span class="badge bg-success">Déposée</span></td>
                                    <td>15/01/2024</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" onclick="viewDeclaration('tva', '2024-01')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" onclick="exportDeclaration('tva', '2024-01')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Décembre 2023</td>
                                    <td><span class="badge bg-success">Déposée</span></td>
                                    <td>15/12/2023</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" onclick="viewDeclaration('tva', '2023-12')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" onclick="exportDeclaration('tva', '2023-12')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm" onclick="generateDeclaration('tva')">
                            <i class="fas fa-plus me-2"></i>Générer déclaration TVA
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-2"></i>Déclarations CNSS
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Période</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Janvier 2024</td>
                                    <td><span class="badge bg-success">Déposée</span></td>
                                    <td>10/01/2024</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" onclick="viewDeclaration('cnss', '2024-01')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" onclick="exportDeclaration('cnss', '2024-01')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Décembre 2023</td>
                                    <td><span class="badge bg-success">Déposée</span></td>
                                    <td>10/12/2023</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" onclick="viewDeclaration('cnss', '2023-12')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" onclick="exportDeclaration('cnss', '2023-12')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm" onclick="generateDeclaration('cnss')">
                            <i class="fas fa-plus me-2"></i>Générer déclaration CNSS
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents fiscaux -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-book me-2"></i>Documents Fiscaux Obligatoires
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card border-left-success shadow h-100 py-2 mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Registre des achats</h6>
                                            <small class="text-muted">Dernière MAJ: 20/01/2024</small>
                                        </div>
                                        <span class="badge bg-success">A jour</span>
                                    </div>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewDocument('registre_achats')">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" onclick="exportDocument('registre_achats')">
                                            <i class="fas fa-download me-1"></i>Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-success shadow h-100 py-2 mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Registre des ventes</h6>
                                            <small class="text-muted">Dernière MAJ: 20/01/2024</small>
                                        </div>
                                        <span class="badge bg-success">A jour</span>
                                    </div>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewDocument('registre_ventes')">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" onclick="exportDocument('registre_ventes')">
                                            <i class="fas fa-download me-1"></i>Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-success shadow h-100 py-2 mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Livre journal</h6>
                                            <small class="text-muted">Dernière MAJ: 20/01/2024</small>
                                        </div>
                                        <span class="badge bg-success">A jour</span>
                                    </div>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewDocument('livre_journal')">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" onclick="exportDocument('livre_journal')">
                                            <i class="fas fa-download me-1"></i>Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-success shadow h-100 py-2 mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Grand livre</h6>
                                            <small class="text-muted">Dernière MAJ: 20/01/2024</small>
                                        </div>
                                        <span class="badge bg-success">A jour</span>
                                    </div>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewDocument('grand_livre')">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" onclick="exportDocument('grand_livre')">
                                            <i class="fas fa-download me-1"></i>Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calrier des échéances -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Calendrier des Échéances Fiscales
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Obligation</th>
                                    <th>Fréquence</th>
                                    <th>Prochaine échéance</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>TVA</td>
                                    <td>Mensuelle</td>
                                    <td>15/02/2024</td>
                                    <td><span class="badge bg-success">A jour</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="scheduleReminder('tva')">
                                            <i class="fas fa-bell"></i> Rappel
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>CNSS</td>
                                    <td>Mensuelle</td>
                                    <td>10/02/2024</td>
                                    <td><span class="badge bg-success">A jour</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="scheduleReminder('cnss')">
                                            <i class="fas fa-bell"></i> Rappel
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Impôt sur le revenu</td>
                                    <td>Trimestrielle</td>
                                    <td>31/03/2024</td>
                                    <td><span class="badge bg-danger">En retard</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning" onclick="generateDeclaration('impot_revenu')">
                                            <i class="fas fa-exclamation-triangle"></i> Générer
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Taxe professionnelle</td>
                                    <td>Annuelle</td>
                                    <td>31/12/2024</td>
                                    <td><span class="badge bg-success">A jour</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="scheduleReminder('taxe_professionnelle')">
                                            <i class="fas fa-bell"></i> Rappel
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
});

function loadStatistics() {
    console.log('Chargement des statistiques de conformité (à implémenter)');
}

function updateStatutBadge(elementId, statut) {
    const element = document.getElementById(elementId);
    let badgeClass = 'bg-success';

    if (statut === 'En retard') {
        badgeClass = 'bg-danger';
    } else if (statut === 'En attente') {
        badgeClass = 'bg-warning';
    }

    element.innerHTML = `<span class="badge ${badgeClass}">${statut}</span>`;
}

function generateDeclaration(type) {
    const periode = prompt('Entrez la période (ex: 2024-01):');
    if (!periode) return;

    $.ajax({
        url: '/comptabilite/conformite/generate-declaration',
        method: 'POST',
        data: {
            type: type,
            periode: periode
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            alert(response.message);
            location.reload();
        },
        error: function(xhr) {
            alert('Erreur lors de la génération de la déclaration');
        }
    });
}

function exportDeclaration(type, periode) {
    $.ajax({
        url: '/comptabilite/conformite/export-declaration',
        method: 'POST',
        data: {
            type: type,
            periode: periode
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            alert(response.message);
            window.open(response.file_url, '_blank');
        },
        error: function(xhr) {
            alert('Erreur lors de l\'export de la déclaration');
        }
    });
}

function viewDeclaration(type, periode) {
    // Ouvrir la déclaration dans une nouvelle fenêtre
    window.open(`/comptabilite/conformite/declaration/${type}/${periode}`, '_blank');
}

function viewDocument(documentType) {
    // Ouvrir le document dans une nouvelle fenêtre
    window.open(`/comptabilite/conformite/document/${documentType}`, '_blank');
}

function exportDocument(documentType) {
    // Exporter le document
    window.open(`/comptabilite/conformite/export/${documentType}`, '_blank');
}

function scheduleReminder(type) {
    const date = prompt('Entrez la date du rappel (YYYY-MM-DD):');
    if (!date) return;

    alert(`Rappel programmé pour ${type} le ${date}`);
}

function exportAllDeclarations() {
    // Exporter toutes les déclarations
    window.open('/comptabilite/conformite/export-all', '_blank');
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
