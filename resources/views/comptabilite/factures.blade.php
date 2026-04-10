@extends('layouts.app')

@section('title', 'Factures')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
                        Gestion des Factures
                    </h1>
                    <p class="text-muted">Liste et gestion de toutes les factures clients et fournisseurs</p>
                </div>
                <div>
                    <a href="{{ route('comptabilite.factures.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouvelle Facture
                    </a>
                    <a href="{{ route('comptabilite.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Message d'erreur ou d'information -->
    @if ($errors->any())
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erreur!</strong>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erreur!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Factures Clients
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="factures-clients">
                                {{ $statsFactures['clients'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
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
                                Factures Fournisseurs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="factures-fournisseurs">
                                {{ $statsFactures['fournisseurs'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
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
                                {{ $statsFactures['en_attente'] ?? 0 }}
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
                                {{ $statsFactures['en_retard'] ?? 0 }}
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
                            <label class="form-label">Type</label>
                            <select id="filtre-type" class="form-select">
                                <option value="">Tous les types</option>
                                <option value="client">Client</option>
                                <option value="fournisseur">Fournisseur</option>
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
                        <div class="col-md-3">
                            <label class="form-label">Tiers</label>
                            <select id="filtre-tiers" class="form-select">
                                <option value="">Tous les tiers</option>
                                <option value="1">Client A</option>
                                <option value="2">Client B</option>
                                <option value="3">Fournisseur X</option>
                                <option value="4">Fournisseur Y</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Recherche</label>
                            <input type="text" id="filtre-recherche" class="form-control" placeholder="Numéro ou libellé">
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
                                    <th>Type</th>
                                    <th>Tiers</th>
                                    <th>Date</th>
                                    <th>Montant HT</th>
                                    <th>TVA</th>
                                    <th>Montant TTC</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($factures as $facture)
                                <tr>
                                    <td>
                                        <a href="{{ route('comptabilite.factures.show', $facture->id) }}">
                                            {{ $facture->numero }}
                                        </a>
                                    </td>
                                    <td><span class="badge bg-info">{{ $facture->type ?? 'Client' }}</span></td>
                                    <td>{{ $facture->client_nom ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($facture->date_facture ?? now())->format('d/m/Y') }}</td>
                                    <td class="text-end">{{ number_format($facture->montant_ht ?? 0, 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format(($facture->montant_ht ?? 0) * (($facture->tva ?? 0) / 100), 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format($facture->montant_ttc ?? 0, 0, ',', ' ') }}</td>
                                    <td>
                                        @if($facture->statut == 'payee')
                                            <span class="badge bg-success">Payée</span>
                                        @elseif($facture->statut == 'en_attente')
                                            <span class="badge bg-warning">En attente</span>
                                        @elseif($facture->statut == 'en_retard')
                                            <span class="badge bg-danger">En retard</span>
                                        @elseif($facture->statut == 'annulee')
                                            <span class="badge bg-secondary">Annulée</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $facture->statut }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('comptabilite.factures.show', $facture->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('comptabilite.factures.edit', $facture->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('comptabilite.factures.print', $facture->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Imprimer">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @if($facture->statut == 'en_attente' || $facture->statut == 'impayee')
                                                <button class="btn btn-sm btn-outline-success" onclick="markAsPaid({{ $facture->id }})" title="Marquer comme payée">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteFacture({{ $facture->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Aucune facture trouvée</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
        order: [[3, 'desc']]
    });

    // Appliquer les filtres
    $('#filtre-type, #filtre-statut, #filtre-periode, #filtre-tiers, #filtre-recherche').on('change keyup', function() {
        applyFilters();
    });
});

function loadStatistics() {
    $.get("{{ route('comptabilite.factures.statistics') }}", function(data) {
        $('#factures-clients').text(data.total_factures_clients.toLocaleString('fr-FR'));
        $('#factures-fournisseurs').text(data.total_factures_fournisseurs.toLocaleString('fr-FR'));
        $('#factures-en-attente').text(data.factures_en_attente.toLocaleString('fr-FR'));
        $('#factures-en-retard').text(data.factures_en_retard.toLocaleString('fr-FR'));
    });
}

function applyFilters() {
    // Logique de filtrage à implémenter
    console.log('Filtres appliqués');
}

function showFacture(id) {
    window.location.href = `/comptabilite/factures/${id}`;
}

function editFacture(id) {
    window.location.href = `/comptabilite/factures/${id}/edit`;
}

function deleteFacture(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')) {
        const form = document.getElementById('deleteForm');
        if (!form) {
            alert('Formulaire de suppression introuvable');
            return;
        }
        form.action = '{{ route("comptabilite.factures.destroy", ":id") }}'.replace(':id', id);
        form.submit();
    }
}

function markAsPaid(id) {
    if (confirm('Êtes-vous sûr de vouloir marquer cette facture comme payée ?')) {
        const form = document.getElementById('paidForm');
        if (!form) {
            alert('Formulaire de mise à jour introuvable');
            return;
        }
        form.action = '{{ route("comptabilite.factures.markPaid", ":id") }}'.replace(':id', id);
        form.submit();
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

<!-- Formulaires cachés pour les actions -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="paidForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>

@endsection
