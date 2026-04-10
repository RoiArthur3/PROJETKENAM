@extends('layouts.app')

@section('title', 'Module Magasin - Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-warehouse me-2 text-primary"></i>Dashboard Magasin
            </h1>
            <p class="text-muted mb-0">Vue globale du stock et des mouvements</p>
        </div>
        <button class="btn btn-primary" onclick="location.reload()">
            <i class="fas fa-sync-alt me-2"></i>Rafraîchir
        </button>
    </div>

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Produits</div>
                    <div class="h3 mb-0 text-primary">{{ $stats['total_produits'] ?? 0 }}</div>
                    <small class="text-success">{{ $stats['produits_actifs'] ?? 0 }} actifs</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-start border-danger border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Alertes Stock</div>
                    <div class="h3 mb-0 {{ ($stats['alertes_stock'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">{{ $stats['alertes_stock'] ?? 0 }}</div>
                    <small class="text-muted">stock ≤ seuil min</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Entrées</div>
                    <div class="h3 mb-0 text-success">{{ $stats['total_entrees'] ?? 0 }}</div>
                    <small class="text-muted">aujourd'hui ({{ $stats['entrees_mois'] ?? 0 }} ce mois)</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Sorties</div>
                    <div class="h3 mb-0 text-warning">{{ $stats['total_sorties'] ?? 0 }}</div>
                    <small class="text-muted">aujourd'hui ({{ $stats['sorties_mois'] ?? 0 }} ce mois)</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Catégories</div>
                    <div class="h3 mb-0 text-info">{{ $stats['total_categories'] ?? 0 }}</div>
                    <small class="text-muted">de produits</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-start border-dark border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Valeur Stock</div>
                    <div class="h4 mb-0">{{ number_format($stats['valeur_stock'] ?? 0, 0, ',', ' ') }}</div>
                    <small class="text-muted">FCFA</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Produits Critiques -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Produits en Alerte Stock</h6>
                </div>
                <div class="card-body">
                    @if(isset($produits_critiques) && count($produits_critiques) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Désignation</th>
                                    <th>Stock actuel</th>
                                    <th>Stock min</th>
                                    <th>Valeur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produits_critiques as $p)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $p->code ?? '-' }}</span></td>
                                    <td class="fw-bold">{{ $p->designation }}</td>
                                    <td><span class="text-danger fw-bold">{{ $p->stock_actuel }}</span></td>
                                    <td>{{ $p->stock_min }}</td>
                                    <td>{{ number_format(($p->stock_actuel ?? 0) * ($p->prix_unitaire ?? 0), 0, ',', ' ') }} FCFA</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3 d-block"></i>
                        <p class="text-muted">Aucun produit en alerte stock</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-info"><i class="fas fa-tags me-2"></i>Produits par Catégorie</h6>
                </div>
                <div class="card-body">
                    @if(isset($produits_par_categorie) && count($produits_par_categorie) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th>Produits</th>
                                    <th>Stock total</th>
                                    <th>Valeur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produits_par_categorie as $cat)
                                <tr>
                                    <td class="fw-bold">{{ $cat->categorie }}</td>
                                    <td>{{ $cat->nb }}</td>
                                    <td>{{ number_format($cat->stock_total ?? 0, 0, ',', ' ') }}</td>
                                    <td class="text-success fw-bold">{{ number_format($cat->valeur ?? 0, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-boxes fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">Aucun produit enregistré</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Mouvements récents -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-success"><i class="fas fa-arrow-down me-2"></i>Entrées Récentes</h6>
                </div>
                <div class="card-body">
                    @if(isset($recent_entrees) && count($recent_entrees) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Fournisseur</th>
                                    <th>Référence</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_entrees as $entree)
                                <tr>
                                    <td>{{ $entree->created_at ? \Carbon\Carbon::parse($entree->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="fw-bold">{{ $entree->produit_nom ?? $entree->produit_id }}</td>
                                    <td><span class="badge bg-success">+{{ $entree->quantite ?? 0 }}</span></td>
                                    <td>{{ $entree->fournisseur ?? '-' }}</td>
                                    <td class="text-muted">{{ $entree->reference ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-arrow-down fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">Aucune entrée récente</p>
                        <a href="{{ route('magasin.entrees.create') }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-plus me-1"></i>Nouvelle entrée
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-warning"><i class="fas fa-arrow-up me-2"></i>Sorties Récentes</h6>
                </div>
                <div class="card-body">
                    @if(isset($recent_sorties) && count($recent_sorties) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Destinataire</th>
                                    <th>Motif</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_sorties as $sortie)
                                <tr>
                                    <td>{{ $sortie->created_at ? \Carbon\Carbon::parse($sortie->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="fw-bold">{{ $sortie->produit_nom ?? $sortie->produit_id }}</td>
                                    <td><span class="badge bg-warning text-dark">-{{ $sortie->quantite ?? 0 }}</span></td>
                                    <td>{{ $sortie->destinataire ?? '-' }}</td>
                                    <td class="text-muted">{{ $sortie->motif ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-arrow-up fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">Aucune sortie récente</p>
                        <a href="{{ route('magasin.sorties.create') }}" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-plus me-1"></i>Nouvelle sortie
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-bolt me-2"></i>Actions Rapides</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('magasin.inventaire') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-list me-2"></i>Inventaire
                            </a>
                        </div>
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('magasin.entrees') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-arrow-down me-2"></i>Entrées
                            </a>
                        </div>
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('magasin.sorties') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-arrow-up me-2"></i>Sorties
                            </a>
                        </div>
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('magasin.rapports') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-chart-bar me-2"></i>Rapports
                            </a>
                        </div>
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('magasin.produits.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-2"></i>Nouveau Produit
                            </a>
                        </div>
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('magasin.entrees.create') }}" class="btn btn-outline-dark w-100">
                                <i class="fas fa-truck me-2"></i>Réception
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
