@extends('layouts.app')

@section('title', 'Stock des Entrepôts - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Stock des Entrepôts</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('entrepots.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Entrepôts
            </a>
            <a href="{{ route('entrepots.list') }}" class="btn btn-outline-primary">
                <i class="fas fa-warehouse me-2"></i>Liste Entrepôts
            </a>
            <a href="{{ route('entrepots.transferts') }}" class="btn btn-outline-info">
                <i class="fas fa-exchange-alt me-2"></i>Transferts
            </a>
            <a href="{{ route('entrepots.rapports') }}" class="btn btn-outline-success">
                <i class="fas fa-chart-bar me-2"></i>Rapports
            </a>
            <a href="{{ route('warehouse.entrees.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Entrée
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Entrepôt</label>
                    <select name="entrepot" class="form-select">
                        <option value="">Tous les entrepôts</option>
                        <option value="Entrepôt Principal - Abidjan">Entrepôt Principal - Abidjan</option>
                        <option value="Entrepôt Nord - Bouaké">Entrepôt Nord - Bouaké</option>
                        <option value="Entrepôt Sud - San Pedro">Entrepôt Sud - San Pedro</option>
                        <option value="Entrepôt Central - Yamoussoukro">Entrepôt Central - Yamoussoukro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Catégorie</label>
                    <select name="categorie" class="form-select">
                        <option value="">Toutes les catégories</option>
                        <option value="Informatique">Informatique</option>
                        <option value="Mobilier">Mobilier</option>
                        <option value="Équipements">Équipements</option>
                        <option value="Consommables">Consommables</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="disponible">Disponible</option>
                        <option value="alerte">Alerte</option>
                        <option value="rupture">Rupture</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="recherche" class="form-control" placeholder="Rechercher un produit...">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                    <a href="{{ route('entrepots.stock') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPIs Stock -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Articles</h6>
                            <h3 class="mb-0">{{ $produits->count() }}</h3>
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
                            <h3 class="mb-0">{{ number_format($stats['valeur_totale'], 0, ',', ' ') }} FCFA</h3>
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
                            <h6 class="text-muted mb-1">Quantité Totale</h6>
                            <h3 class="mb-0">{{ $produits->sum('stock_actuel') }}</h3>
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
                            <h6 class="text-muted mb-1">Alertes</h6>
                            <h3 class="mb-0">{{ $stats['alertes_stock'] }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau du Stock -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Entrepôt</th>
                            <th>Produit</th>
                            <th>Catégorie</th>
                            <th>Quantité</th>
                            <th>Valeur</th>
                            <th>Dernière Entrée</th>
                            <th>Dernière Sortie</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produits as $produit)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $produit->code }}</div>
                                <div class="text-muted small">ID: {{ $produit->id }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        PR
                                    </div>
                                    <div>
                                        <div class="fw-bold">Entrepôt Principal</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $produit->designation }}</div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $produit->categorie ?? 'Non définie' }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $produit->stock_actuel }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ number_format($produit->stock_actuel * $produit->prix_unitaire, 0, ',', ' ') }} FCFA</div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-muted">Non disponible</div>
                                </div>
                            </td>
                            <td>
                                <div class="text-muted">Jamais</div>
                            </td>
                            <td>
                                @php
                                    $statut = 'disponible';
                                    if ($produit->stock_actuel <= $produit->stock_min) {
                                        $statut = 'alerte';
                                    }
                                    if ($produit->stock_actuel == 0) {
                                        $statut = 'rupture';
                                    }
                                @endphp
                                <span class="badge bg-{{ $statut == 'disponible' ? 'success' : ($statut == 'alerte' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($statut) }}
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
                                    <button class="btn btn-outline-success" title="Transférer">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    @if($statut == 'alerte')
                                        <button class="btn btn-outline-warning" title="Réapprovisionner">
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
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun article trouvé dans les entrepôts</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Ajouter le premier article
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques par Entrepôt -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Statistiques par Entrepôt</h5>
                    <div class="row text-center">
                        @if(isset($entrepots) && $entrepots->count() > 0)
                            @foreach($entrepots as $entrepot)
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="text-primary">{{ $entrepot->nom }} - {{ $entrepot->ville ?? 'N/A' }}</h6>
                                            <p class="mb-0">Articles: {{ $entrepot->produits_count ?? 0 }} | Valeur: {{ number_format($entrepot->valeur_stock ?? 0, 1, ',', ' ') }}M FCFA</p>
                                            <small>Capacité: {{ $entrepot->capacite ?? 0 }} | Utilisé: {{ $entrepot->espace_utilise ?? 0 }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Aucun entrepôt configuré. Les statistiques s'afficheront ici une fois les entrepôts créés.
                                </div>
                            </div>
                        @endif
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
