@extends('layouts.app')

@section('title', 'Produits - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-boxes me-2 text-primary"></i>Gestion des Produits
            </h1>
            <p class="text-muted mb-0">Catalogue des produits en stock</p>
        </div>
        <a href="{{ route('stock.produits.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau Produit
        </a>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Total Produits</div>
                    <div class="h3 mb-0 text-primary">{{ $produits->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Actifs</div>
                    <div class="h3 mb-0 text-success">{{ $produits->where('actif', true)->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Stock Faible</div>
                    <div class="h3 mb-0 text-warning">{{ $produits->filter(fn($p) => $p->stock_actuel <= $p->stock_min)->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Rupture</div>
                    <div class="h3 mb-0 text-danger">{{ $produits->where('stock_actuel', 0)->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Produits
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="produitsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Désignation</th>
                            <th>Catégorie</th>
                            <th>Stock Actuel</th>
                            <th>Stock Min</th>
                            <th>Prix Unitaire</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produits as $produit)
                            <tr>
                                <td><code>{{ $produit->code }}</code></td>
                                <td class="fw-semibold">{{ $produit->designation }}</td>
                                <td><span class="badge bg-secondary">{{ $produit->categorie ?? 'Non catégorisé' }}</span></td>
                                <td class="text-end">
                                    @php
                                        $stockClass = 'success';
                                        if ($produit->stock_actuel == 0) $stockClass = 'danger';
                                        elseif ($produit->stock_actuel <= $produit->stock_min) $stockClass = 'warning';
                                    @endphp
                                    <span class="badge bg-{{ $stockClass }}">{{ $produit->stock_actuel }}</span>
                                </td>
                                <td class="text-end">{{ $produit->stock_min }}</td>
                                <td class="text-end">{{ $produit->prix_unitaire ? number_format($produit->prix_unitaire, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $produit->actif ? 'success' : 'secondary' }}">
                                        {{ $produit->actif ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-sm btn-info" title="Voir détails" data-bs-toggle="modal" data-bs-target="#detailModal{{ $produit->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('stock.produits.edit', $produit->id) }}" class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" title="Supprimer" onclick="supprimerProduit({{ $produit->id }}, '{{ $produit->designation }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Détails -->
                            <div class="modal fade" id="detailModal{{ $produit->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Détails Produit</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <dl class="row">
                                                <dt class="col-sm-4">Code:</dt>
                                                <dd class="col-sm-8"><code>{{ $produit->code }}</code></dd>
                                                
                                                <dt class="col-sm-4">Désignation:</dt>
                                                <dd class="col-sm-8">{{ $produit->designation }}</dd>
                                                
                                                <dt class="col-sm-4">Catégorie:</dt>
                                                <dd class="col-sm-8">{{ $produit->categorie ?? 'Non catégorisé' }}</dd>
                                                
                                                <dt class="col-sm-4">Unité:</dt>
                                                <dd class="col-sm-8">{{ $produit->unite }}</dd>
                                                
                                                <dt class="col-sm-4">Stock actuel:</dt>
                                                <dd class="col-sm-8"><span class="badge bg-{{ $stockClass }}">{{ $produit->stock_actuel }}</span></dd>
                                                
                                                <dt class="col-sm-4">Stock minimum:</dt>
                                                <dd class="col-sm-8">{{ $produit->stock_min }}</dd>
                                                
                                                <dt class="col-sm-4">Prix unitaire:</dt>
                                                <dd class="col-sm-8">{{ $produit->prix_unitaire ? number_format($produit->prix_unitaire, 0, ',', ' ') . ' FCFA' : 'Non renseigné' }}</dd>
                                                
                                                @if($produit->emplacement)
                                                    <dt class="col-sm-4">Emplacement:</dt>
                                                    <dd class="col-sm-8">{{ $produit->emplacement }}</dd>
                                                @endif
                                                
                                                @if($produit->description)
                                                    <dt class="col-sm-4">Description:</dt>
                                                    <dd class="col-sm-8">{{ $produit->description }}</dd>
                                                @endif
                                                
                                                <dt class="col-sm-4">Statut:</dt>
                                                <dd class="col-sm-8">
                                                    <span class="badge bg-{{ $produit->actif ? 'success' : 'secondary' }}">
                                                        {{ $produit->actif ? 'Actif' : 'Inactif' }}
                                                    </span>
                                                </dd>
                                            </dl>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucun produit enregistré
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($produits->hasPages())
                <div class="mt-3">
                    {{ $produits->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#produitsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        order: [[1, 'asc']],
        pageLength: 25
    });
});
</script>

<script>
function supprimerProduit(id, nom) {
    if (confirm('Êtes-vous sûr de vouloir supprimer le produit "' + nom + '" ?')) {
        // Créer un formulaire temporaire pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/stock/produits/' + id;
        
        // Ajouter le token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Ajouter la méthode DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
