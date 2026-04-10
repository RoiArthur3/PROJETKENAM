@extends('layouts.app')

@section('title', 'Gestion de la Facturation - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Gestion de la Facturation</h1>
            <a href="{{ route('invoicing.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Facture
            </a>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="row mb-4">
        <div class="col-md-3">
            <select class="form-select" id="typeFilter">
                <option value="">Tous les types</option>
                <option value="client">Client</option>
                <option value="supplier">Fournisseur</option>
                <option value="internal">Interne</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" id="statusFilter">
                <option value="">Tous les statuts</option>
                <option value="draft">Brouillon</option>
                <option value="issued">Émise</option>
                <option value="paid">Payée</option>
                <option value="cancelled">Annulée</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" class="form-control" id="dateFilter" placeholder="Date d'échéance">
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-success" onclick="filterInvoices()">
                <i class="fas fa-search"></i> Filtrer
            </button>
        </div>
    </div>

    <!-- Tableau des Factures -->
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Liste des Factures</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>N° Facture</th>
                                    <th>Type</th>
                                    <th>Client/Fournisseur</th>
                                    <th>Montant Net</th>
                                    <th>Montant Payé</th>
                                    <th>Restant</th>
                                    <th>Statut</th>
                                    <th>Date d'Échéance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="invoiceTable">
                                <tr>
                                    <td>FAC-2025-001</td>
                                    <td><span class="badge bg-info">Client</span></td>
                                    <td>Entreprise XYZ</td>
                                    <td>150 000 FCFA</td>
                                    <td>150 000 FCFA</td>
                                    <td>0 FCFA</td>
                                    <td><span class="badge bg-success">Payée</span></td>
                                    <td>2023-10-15</td>
                                    <td>
                                        <a href="{{ route('invoicing.show', 1) }}" class="btn btn-sm btn-info">Voir</a>
                                        <button class="btn btn-sm btn-warning">Modifier</button>
                                        <button class="btn btn-sm btn-danger">Annuler</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FAC-2025-002</td>
                                    <td><span class="badge bg-warning">Fournisseur</span></td>
                                    <td>Fournisseur ABC</td>
                                    <td>75 000 FCFA</td>
                                    <td>50 000 FCFA</td>
                                    <td>25 000 FCFA</td>
                                    <td><span class="badge bg-warning">Partiellement Payée</span></td>
                                    <td>2023-10-20</td>
                                    <td>
                                        <a href="{{ route('invoicing.show', 2) }}" class="btn btn-sm btn-info">Voir</a>
                                        <button class="btn btn-sm btn-warning">Modifier</button>
                                        <button class="btn btn-sm btn-success">Ajouter Paiement</button>
                                        <button class="btn btn-sm btn-danger">Annuler</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FAC-2025-003</td>
                                    <td><span class="badge bg-info">Client</span></td>
                                    <td>Client DEF</td>
                                    <td>200 000 FCFA</td>
                                    <td>0 FCFA</td>
                                    <td>200 000 FCFA</td>
                                    <td><span class="badge bg-danger">En Attente</span></td>
                                    <td>2023-10-25</td>
                                    <td>
                                        <a href="{{ route('invoicing.show', 3) }}" class="btn btn-sm btn-info">Voir</a>
                                        <button class="btn btn-sm btn-warning">Modifier</button>
                                        <button class="btn btn-sm btn-success">Ajouter Paiement</button>
                                        <button class="btn btn-sm btn-danger">Annuler</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé Financier -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Facturé</h5>
                    <h2>225 000 FCFA</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Payé</h5>
                    <h2>150 000 FCFA</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">En Attente</h5>
                    <h2>75 000 FCFA</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Échu</h5>
                    <h2>0 FCFA</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterInvoices() {
    const type = document.getElementById('typeFilter').value;
    const status = document.getElementById('statusFilter').value;
    const date = document.getElementById('dateFilter').value;
    // Logique pour filtrer les factures
    console.log('Filtrage:', type, status, date);
}
</script>
@endsection
