@extends('layouts.app')

@section('title', 'Transferts Entrepôts - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Transferts entre Entrepôts</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('entrepots.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Entrepôts
            </a>
            <a href="{{ route('entrepots.list') }}" class="btn btn-outline-primary">
                <i class="fas fa-warehouse me-2"></i>Liste Entrepôts
            </a>
            <a href="{{ route('entrepots.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Créer un Entrepôt
            </a>
            <a href="{{ route('entrepots.stock') }}" class="btn btn-outline-info">
                <i class="fas fa-boxes me-2"></i>Stock
            </a>
            <a href="{{ route('entrepots.rapports') }}" class="btn btn-outline-success">
                <i class="fas fa-chart-bar me-2"></i>Rapports
            </a>
            <button class="btn btn-primary">
                <i class="fas fa-exchange-alt me-2"></i>Nouveau Transfert
            </button>
        </div>
    </div>

    <!-- KPIs Transferts -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Transferts</h6>
                            <h3 class="mb-0">{{ $transferts->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Quantité Totale</h6>
                            <h3 class="mb-0">{{ $transferts->sum('quantite') }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
                                <i class="fas fa-calculator"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Livrés</h6>
                            <h3 class="mb-0">{{ $transferts->where('statut', 'livré')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">En Transit</h6>
                            <h3 class="mb-0">{{ $transferts->where('statut', 'en_transit')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-truck"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Transferts -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Source</th>
                            <th>Destination</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Demandeur</th>
                            <th>Motif</th>
                            <th>Transporteur</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transferts as $transfert)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        TRF
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $transfert->reference }}</div>
                                        <div class="text-muted small">ID: {{ $transfert->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ $transfert->date->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $transfert->date->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($transfert->entrepot_source, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $transfert->entrepot_source }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-info text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($transfert->entrepot_destination, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $transfert->entrepot_destination }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $transfert->produit }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $transfert->quantite }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($transfert->demandeur, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $transfert->demandeur }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $transfert->motif }}">
                                    {{ $transfert->motif }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-warning text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($transfert->transporteur, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $transfert->transporteur }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $transfert->statut == 'livré' ? 'success' : ($transfert->statut == 'en_transit' ? 'warning' : ($transfert->statut == 'en_attente' ? 'info' : 'secondary') }}">
                                    {{ ucfirst($transfert->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($transfert->statut == 'en_attente')
                                        <button class="btn btn-outline-success" title="Valider">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Annuler">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    @if($transfert->statut == 'en_transit')
                                        <button class="btn btn-outline-info" title="Suivre">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-outline-dark" title="Imprimer">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun transfert trouvé</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Créer le premier transfert
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques de Transferts -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Statistiques de Transferts</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Transferts par Statut</h6>
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-success">{{ $transferts->where('statut', 'livré')->count() }}</h4>
                                            <p class="mb-0">Livrés</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-warning">{{ $transferts->where('statut', 'en_transit')->count() }}</h4>
                                            <p class="mb-0">En Transit</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-info">{{ $transferts->where('statut', 'en_attente')->count() }}</h4>
                                            <p class="mb-0">En Attente</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-secondary">{{ $transferts->where('statut', 'planifié')->count() }}</h4>
                                            <p class="mb-0">Planifié</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Transferts par Transporteur</h6>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-primary">Express Logistique</h4>
                                            <p class="mb-0">25 transferts</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-info">Transport Express</h4>
                                            <p class="mb-0">15 transferts</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-warning">Logistique KENAM</h4>
                                            <p class="mb-0">20 transferts</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>
@endsection
