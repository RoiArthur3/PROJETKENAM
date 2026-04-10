@extends('layouts.app')

@section('title', 'Charges')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-receipt me-2 text-primary"></i>
                        Gestion des Charges
                    </h1>
                    <p class="text-muted">Suivi des charges et dépenses de l'entreprise</p>
                </div>
                <div>
                    <button class="btn btn-outline-secondary me-2" onclick="exportExcel()">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                    <button class="btn btn-outline-danger me-2" onclick="exportPDF()">
                        <i class="fas fa-file-pdf me-2"></i>Export PDF
                    </button>
                    <a href="{{ route('comptabilite.charges.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouvelle Charge
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
                                Total Charges
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-charges">
                                {{ number_format($stats['total_charges'] ?? 0, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                Charges Mois
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="charges-mois">
                                {{ number_format($stats['charges_mois'] ?? 0, 0, ',', ' ') }} FCFA
                            </div>
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
                                Moyenne Mensuelle
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="moyenne-mensuelle">
                                {{ number_format($stats['moyenne_mensuelle'] ?? 0, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
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
                                Charges Année
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="charges-annee">
                                {{ number_format($stats['charges_annee'] ?? 0, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
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
                        <div class="col-md-3">
                            <label class="form-label">Période</label>
                            <select id="filtre-periode" class="form-select">
                                <option value="">Toutes les périodes</option>
                                <option value="ce-mois">Ce mois</option>
                                <option value="mois-dernier">Le mois dernier</option>
                                <option value="ce-trimestre">Ce trimestre</option>
                                <option value="cette-annee">Cette année</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Catégorie</label>
                            <select id="filtre-categorie" class="form-select">
                                <option value="">Toutes les catégories</option>
                                <option value="Loyer">Loyer</option>
                                <option value="Salaires">Salaires</option>
                                <option value="Fournitures">Fournitures</option>
                                <option value="Services">Services</option>
                                <option value="Transport">Transport</option>
                                <option value="Autres">Autres</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select id="filtre-statut" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="Payé">Payé</option>
                                <option value="En attente">En attente</option>
                                <option value="Annulé">Annulé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Recherche</label>
                            <input type="text" id="filtre-recherche" class="form-control" placeholder="Rechercher...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des charges -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table me-2"></i>Liste des Charges
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="chargesTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Libellé</th>
                                    <th>Catégorie</th>
                                    <th>Montant</th>
                                    <th>Mode Paiement</th>
                                    <th>Fournisseur</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>01/01/2024</td>
                                    <td>Loyer bureau - Janvier</td>
                                    <td><span class="badge bg-info">Loyer</span></td>
                                    <td class="text-end">{{ number_format(1500000, 0, ',', ' ') }} FCFA</td>
                                    <td>Virement</td>
                                    <td>SCI KENAM</td>
                                    <td><span class="badge bg-success">Payé</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editCharge(1)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCharge(1)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>31/01/2024</td>
                                    <td>Salaires - Janvier</td>
                                    <td><span class="badge bg-warning">Salaires</span></td>
                                    <td class="text-end">{{ number_format(8000000, 0, ',', ' ') }} FCFA</td>
                                    <td>Virement</td>
                                    <td>Divers</td>
                                    <td><span class="badge bg-success">Payé</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editCharge(2)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCharge(2)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>15/01/2024</td>
                                    <td>Fournitures de bureau</td>
                                    <td><span class="badge bg-secondary">Fournitures</span></td>
                                    <td class="text-end">{{ number_format(200000, 0, ',', ' ') }} FCFA</td>
                                    <td>Espèces</td>
                                    <td>Papeterie ABC</td>
                                    <td><span class="badge bg-success">Payé</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editCharge(3)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCharge(3)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>20/01/2024</td>
                                    <td>Internet et Téléphonie</td>
                                    <td><span class="badge bg-primary">Services</span></td>
                                    <td class="text-end">{{ number_format(350000, 0, ',', ' ') }} FCFA</td>
                                    <td>Virement</td>
                                    <td>Orange CI</td>
                                    <td><span class="badge bg-warning">En attente</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editCharge(4)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCharge(4)">
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

    // Initialiser DataTable
    $('#chargesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr.json'
        },
        pageLength: 10,
        responsive: true
    });

    // Appliquer les filtres
    $('#filtre-periode, #filtre-categorie, #filtre-statut, #filtre-recherche').on('change keyup', function() {
        applyFilters();
    });
});

function loadStatistics() {
    $.get("{{ route('comptabilite.charges.statistics') }}", function(data) {
        $('#total-charges').text(data.total_charges.toLocaleString('fr-FR') + ' FCFA');
        $('#charges-mois').text(data.charges_mois.toLocaleString('fr-FR') + ' FCFA');
        $('#moyenne-mensuelle').text(data.moyenne_mensuelle.toLocaleString('fr-FR') + ' FCFA');
        $('#charges-annee').text(data.charges_annee.toLocaleString('fr-FR') + ' FCFA');
    });
}

function applyFilters() {
    // Logique de filtrage à implémenter
    console.log('Filtres appliqués');
}

function editCharge(id) {
    // Rediriger vers la page d'édition
    window.location.href = `/comptabilite/charges/${id}/edit`;
}

function deleteCharge(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette charge ?')) {
        $.ajax({
            url: `/comptabilite/charges/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Charge supprimée avec succès');
                location.reload();
            },
            error: function(xhr) {
                alert('Erreur lors de la suppression');
            }
        });
    }
}

function saveCharge() {
    const chargeId = $('#chargeId').val();
    const formData = $('#chargeForm').serialize();

    if (chargeId) {
        // Mise à jour
        $.ajax({
            url: `/comptabilite/charges/${chargeId}`,
            method: 'PUT',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Charge mise à jour avec succès');
                location.reload();
            },
            error: function(xhr) {
                alert('Erreur lors de la mise à jour');
            }
        });
    } else {
        // Ajout
        $.ajax({
            url: "{{ route('comptabilite.charges.store') }}",
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Charge ajoutée avec succès');
                location.reload();
            },
            error: function(xhr) {
                alert('Erreur lors de l\'ajout');
            }
        });
    }
}

function exportPDF() {
    const form = $('<form>', {
        method: 'POST',
        action: "{{ route('comptabilite.charges.export-pdf') }}",
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
        action: "{{ route('comptabilite.charges.export-excel') }}",
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
