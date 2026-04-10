@extends('layouts.app')

@section('title', 'Inventaire - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Inventaire du Magasin</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('magasin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Magasin
            </a>
            <a href="{{ route('magasin.entrees') }}" class="btn btn-outline-success">
                <i class="fas fa-arrow-down me-2"></i>Entrées
            </a>
            <a href="{{ route('magasin.sorties') }}" class="btn btn-outline-danger">
                <i class="fas fa-arrow-up me-2"></i>Sorties
            </a>
            <a href="{{ route('magasin.rapports') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-2"></i>Rapports
            </a>
            <a href="{{ route('magasin.produits.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Produit
            </a>
        </div>
    </div>

    <!-- KPIs Inventaire -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Articles</h6>
                            <h3 class="mb-0">{{ $inventaire->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-boxes"></i>
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
                            <h3 class="mb-0">{{ number_format($inventaire->sum('valeur_totale'), 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
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
                            <h6 class="text-muted mb-1">Stock Critique</h6>
                            <h3 class="mb-0">{{ $inventaire->where('statut', 'rupture')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-danger text-white">
                                <i class="fas fa-exclamation-triangle"></i>
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
                            <h6 class="text-muted mb-1">Alertes</h6>
                            <h3 class="mb-0">{{ $inventaire->where('statut', 'alerte')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-bell"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau d'Inventaire -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Produit</th>
                            <th>Catégorie</th>
                            <th>Quantité Réelle</th>
                            <th>Quantité Théorique</th>
                            <th>Valeur Unitaire</th>
                            <th>Valeur Totale</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventaire as $item)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $item->reference }}</div>
                                <div class="text-muted small">ID: {{ $item->id }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $item->produit }}</div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $item->categorie }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $item->quantite_reelle }}</div>
                            </td>
                            <td>
                                <div class="text-muted">{{ $item->quantite_theorique }}</div>
                            </td>
                            <td>
                                <div class="text-primary">{{ number_format($item->valeur_unitaire, 0, ',', ' ') }} FCFA</div>
                            </td>
                            <td>
                                <div class="fw-bold text-success">{{ number_format($item->quantite_reelle * $item->valeur_unitaire, 0, ',', ' ') }} FCFA</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $item->statut == 'ok' ? 'success' : ($item->statut == 'ecart' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($item->statut) }}
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
                                    @if($item->statut == 'rupture')
                                        <button class="btn btn-outline-success" title="Réapprovisionner">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-outline-info" title="Historique">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun produit trouvé dans l'inventaire</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Ajouter le premier produit
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
