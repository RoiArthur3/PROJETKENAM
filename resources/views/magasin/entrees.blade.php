@extends('layouts.app')

@section('title', 'Entrées Stock - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Entrées de Stock</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('magasin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Magasin
            </a>
            <a href="{{ route('magasin.inventaire') }}" class="btn btn-outline-primary">
                <i class="fas fa-boxes me-2"></i>Inventaire
            </a>
            <a href="{{ route('magasin.sorties') }}" class="btn btn-outline-danger">
                <i class="fas fa-arrow-up me-2"></i>Sorties
            </a>
            <a href="{{ route('magasin.rapports') }}" class="btn btn-outline-info">
                <i class="fas fa-chart-bar me-2"></i>Rapports
            </a>
            <button class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Entrée
            </button>
        </div>
    </div>

    <!-- KPIs Entrées -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Entrées</h6>
                            <h3 class="mb-0">{{ $entrees->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-arrow-down"></i>
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
                            <h6 class="text-muted mb-1">Valeur Totale</h6>
                            <h3 class="mb-0">{{ number_format($entrees->sum('montant_total'), 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-wallet"></i>
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
                            <h3 class="mb-0">{{ $entrees->where('statut', 'validée')->count() }}</h3>
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
                            <h3 class="mb-0">{{ $entrees->where('statut', 'en_attente')->count() }}</h3>
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

    <!-- Tableau des Entrées -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Fournisseur</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Montant Total</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entrees as $entree)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success text-white me-3">
                                        ENT
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $entree->reference }}</div>
                                        <div class="text-muted small">ID: {{ $entree->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ $entree->date->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $entree->date->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($entree->fournisseur, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $entree->fournisseur }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $entree->produit }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $entree->quantite }}</div>
                            </td>
                            <td>
                                <div class="text-primary">{{ number_format($entree->montant / $entree->quantite, 0, ',', ' ') }} FCFA</div>
                            </td>
                            <td>
                                <div class="fw-bold text-success">{{ number_format($entree->montant, 0, ',', ' ') }} FCFA</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $entree->statut == 'confirme' ? 'success' : 'warning' }}">
                                    {{ ucfirst($entree->statut) }}
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
                                    @if($entree->statut == 'en_attente')
                                        <button class="btn btn-outline-success" title="Valider">
                                            <i class="fas fa-check"></i>
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
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-arrow-down fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucune entrée de stock trouvée</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Enregistrer la première entrée
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
