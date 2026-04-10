@extends('layouts.app')

@section('title', 'Inventaire - Module Magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Inventaire - Module Magasin
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Actions rapides -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="btn-group" role="group">
                                <a href="{{ route('magasin.dashboard') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                                <a href="{{ route('magasin.entrees') }}" class="btn btn-outline-success">
                                    <i class="fas fa-arrow-down me-2"></i>Entrées
                                </a>
                                <a href="{{ route('magasin.sorties') }}" class="btn btn-outline-warning">
                                    <i class="fas fa-arrow-up me-2"></i>Sorties
                                </a>
                                <a href="{{ route('magasin.produits.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Nouveau Produit
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filtres et recherche -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Rechercher un produit...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option>Toutes les catégories</option>
                                <option>Électronique</option>
                                <option>Bureau</option>
                                <option>Logiciel</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option>Tous les statuts</option>
                                <option>Disponible</option>
                                <option>Rupture</option>
                                <option>Alerte</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tableau d'inventaire -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Référence</th>
                                            <th>Désignation</th>
                                            <th>Catégorie</th>
                                            <th>Stock Actuel</th>
                                            <th>Seuil Alert</th>
                                            <th>Prix Unitaire</th>
                                            <th>Valeur Totale</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($produits) && $produits->count() > 0)
                                            @foreach($produits as $produit)
                                                <tr>
                                                    <td>{{ $produit->reference ?? 'REF-' . $produit->id }}</td>
                                                    <td>{{ $produit->designation ?? 'N/A' }}</td>
                                                    <td>{{ $produit->categorie ?? 'Non défini' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $produit->stock_actuel > 10 ? 'success' : ($produit->stock_actuel > 5 ? 'warning' : 'danger') }}">
                                                            {{ $produit->stock_actuel ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $produit->seuil_alerte ?? 5 }}</td>
                                                    <td>{{ number_format($produit->prix_unitaire ?? 0, 0, ',', ' ') }} FCFA</td>
                                                    <td>{{ number_format(($produit->stock_actuel ?? 0) * ($produit->prix_unitaire ?? 0), 0, ',', ' ') }} FCFA</td>
                                                    <td>
                                                        @if(($produit->stock_actuel ?? 0) <= ($produit->seuil_alerte ?? 5))
                                                            <span class="badge bg-danger">Rupture</span>
                                                        @elseif(($produit->stock_actuel ?? 0) <= (($produit->seuil_alerte ?? 5) * 2))
                                                            <span class="badge bg-warning">Alerte</span>
                                                        @else
                                                            <span class="badge bg-success">Disponible</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button class="btn btn-outline-primary" title="Voir">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-outline-warning" title="Modifier">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-outline-success" title="Entrée">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger" title="Sortie">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-4">
                                                    <i class="fas fa-box-open fa-3x mb-3"></i>
                                                    <p>Aucun produit en inventaire</p>
                                                    <a href="{{ route('magasin.produits.create') }}" class="btn btn-primary">
                                                        <i class="fas fa-plus me-2"></i>Ajouter un produit
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if(isset($produits) && method_exists($produits, 'links'))
                                {{ $produits->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
