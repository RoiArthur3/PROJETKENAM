@extends('layouts.app')

@section('title', 'Inventaire - Module Magasin')

@section('content')
<x-list-layout
    title="Inventaire - Module Magasin"
    icon="fa-list"
    createRoute="magasin.produits.create"
    createText="Nouveau Produit"
>

    <x-slot name="filters">
        <form method="GET" action="{{ route('magasin.inventaire') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Nom, référence, code...">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Catégorie</label>
                    <select name="categorie" class="form-select">
                        <option value="">Toutes</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('categorie')==$cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="disponible" {{ request('statut')=='disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="alerte" {{ request('statut')=='alerte' ? 'selected' : '' }}>Alerte Stock</option>
                        <option value="rupture" {{ request('statut')=='rupture' ? 'selected' : '' }}>Rupture de Stock</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                </div>
            </div>
        </form>
    </x-slot>

    <!-- Statistiques Rapides -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Total Produits</div>
                            <div class="h4 mb-0">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-box fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Valeur Stock</div>
                            <div class="h4 mb-0">{{ number_format($stats['valeur_stock'] ?? 0, 0, ',', ' ') }} <small>FCFA</small></div>
                        </div>
                        <i class="fas fa-coins fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Alerte Stock</div>
                            <div class="h4 mb-0">{{ $stats['alerte'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Rupture Stock</div>
                            <div class="h4 mb-0">{{ $stats['rupture'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-times-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau d'inventaire -->
    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-table me-2"></i>Liste des Produits
            </h6>
            <div class="d-flex gap-2">
                <a href="{{ route('magasin.entrees.create') }}" class="btn btn-sm btn-success"><i class="fas fa-arrow-down me-1"></i>Entrée</a>
                <a href="{{ route('magasin.sorties.create') }}" class="btn btn-sm btn-warning"><i class="fas fa-arrow-up me-1"></i>Sortie</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Désignation</th>
                            <th>Catégorie</th>
                            <th>Stock Actuel</th>
                            <th>Seuil Alerte</th>
                            <th class="text-end">Prix Unitaire</th>
                            <th class="text-end">Valeur Totale</th>
                            <th class="text-center">Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produits as $produit)
                            <tr>
                                <td class="fw-bold text-primary">{{ $produit->reference ?? 'REF-' . $produit->id }}</td>
                                <td>
                                    <div class="fw-bold">{{ $produit->designation ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $produit->code }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $produit->categorie ?? 'Non défini' }}</span></td>
                                <td class="fw-bold {{ $produit->stock_actuel <= $produit->stock_min ? 'text-danger' : '' }}">
                                    {{ $produit->stock_actuel ?? 0 }} <small class="text-muted">{{ $produit->unite ?? '' }}</small>
                                </td>
                                <td>{{ $produit->seuil_alerte ?? $produit->stock_min ?? 5 }}</td>
                                <td class="text-end text-nowrap">{{ number_format($produit->prix_unitaire ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end fw-bold text-nowrap">{{ number_format(($produit->stock_actuel ?? 0) * ($produit->prix_unitaire ?? 0), 0, ',', ' ') }} FCFA</td>
                                <td class="text-center">
                                    @if(($produit->stock_actuel ?? 0) <= ($produit->stock_min ?? 5))
                                        <span class="badge bg-danger">Rupture</span>
                                    @elseif(($produit->stock_actuel ?? 0) <= (($produit->stock_min ?? 5) * 2))
                                        <span class="badge bg-warning">Alerte</span>
                                    @else
                                        <span class="badge bg-success">Disponible</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('magasin.produits.show', $produit->id) }}" class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('magasin.produits.edit', $produit->id) }}" class="btn btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0">Aucun produit trouvé selon vos critères.</p>
                                    @if(request()->anyFilled(['search', 'categorie', 'statut']))
                                        <a href="{{ route('magasin.inventaire') }}" class="btn btn-sm btn-link">Réinitialiser les filtres</a>
                                    @else
                                        <a href="{{ route('magasin.produits.create') }}" class="btn btn-sm btn-primary mt-3">
                                            <i class="fas fa-plus me-2"></i>Ajouter le premier produit
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($produits) && method_exists($produits, 'links'))
                <div class="mt-4 d-flex justify-content-end">
                    {{ $produits->links() }}
                </div>
            @endif
        </div>
    </div>
</x-list-layout>
@endsection
