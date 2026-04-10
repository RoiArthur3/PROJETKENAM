@extends('layouts.app')

@section('title', 'Sorties Stock - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Sorties de Stock</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('magasin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Magasin
            </a>
            <a href="{{ route('magasin.inventaire') }}" class="btn btn-outline-primary">
                <i class="fas fa-boxes me-2"></i>Inventaire
            </a>
            <a href="{{ route('magasin.entrees') }}" class="btn btn-outline-success">
                <i class="fas fa-arrow-down me-2"></i>Entrées
            </a>
            <a href="{{ route('magasin.rapports') }}" class="btn btn-outline-info">
                <i class="fas fa-chart-bar me-2"></i>Rapports
            </a>
            <a href="{{ route('magasin.sorties.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Sortie
            </a>
        </div>
    </div>

    <!-- KPIs Sorties -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Sorties</h6>
                            <h3 class="mb-0">{{ $sorties->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-danger text-white">
                                <i class="fas fa-arrow-up"></i>
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
                            <h3 class="mb-0">{{ $sorties->sum('quantite') }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
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
                            <h6 class="text-muted mb-1">Validées</h6>
                            <h3 class="mb-0">{{ $sorties->where('statut', 'validée')->count() }}</h3>
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
                            <h6 class="text-muted mb-1">En Attente</h6>
                            <h3 class="mb-0">{{ $sorties->where('statut', 'en_attente')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Sorties -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Demandeur</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Motif</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sorties as $sortie)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-danger text-white me-3">
                                        SORT
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $sortie->reference }}</div>
                                        <div class="text-muted small">ID: {{ $sortie->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ $sortie->date->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $sortie->date->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($sortie->demandeur, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $sortie->demandeur }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $sortie->produit }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $sortie->quantite }}</div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $sortie->motif }}">
                                    {{ $sortie->motif }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $sortie->statut == 'validée' ? 'success' : 'warning' }}">
                                    {{ ucfirst($sortie->statut) }}
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
                                    @if($sortie->statut == 'en_attente')
                                        <button class="btn btn-outline-success" title="Valider">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Annuler">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-outline-info" title="Imprimer">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-arrow-up fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucune sortie de stock trouvée</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Enregistrer la première sortie
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
