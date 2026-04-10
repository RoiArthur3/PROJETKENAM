@extends('layouts.app')

@section('title', 'Rapports Entrepôts - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Rapports des Entrepôts</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('entrepots.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Entrepôts
            </a>
            <a href="{{ route('entrepots.list') }}" class="btn btn-outline-primary">
                <i class="fas fa-warehouse me-2"></i>Liste Entrepôts
            </a>
            <a href="{{ route('entrepots.stock') }}" class="btn btn-outline-info">
                <i class="fas fa-boxes me-2"></i>Stock
            </a>
            <a href="{{ route('entrepots.transferts') }}" class="btn btn-outline-warning">
                <i class="fas fa-exchange-alt me-2"></i>Transferts
            </a>
            <button class="btn btn-primary" onclick="generateReport()">
                <i class="fas fa-file-pdf me-2"></i>Générer un Rapport
            </button>
        </div>
    </div>

    <!-- KPIs Rapports -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Entrepôts</h6>
                            <h3 class="mb-0">{{ $stats['total_entrepots'] ?? 0 }}</h3>
                            <small class="text-success">{{ $stats['entrepots_actifs'] ?? 0 }} actifs</small>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-warehouse"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Produits</h6>
                            <h3 class="mb-0">{{ $stats['total_produits'] ?? 0 }}</h3>
                            <small class="text-success">{{ $stats['produits_actifs'] ?? 0 }} actifs</small>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
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
                            <h6 class="text-muted mb-1">Produits Critiques</h6>
                            <h3 class="mb-0 {{ count($rapports['produits_critiques'] ?? []) > 0 ? 'text-danger' : 'text-success' }}">{{ count($rapports['produits_critiques'] ?? []) }}</h3>
                            <small class="text-muted">stock ≤ seuil min</small>
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
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Transferts</h6>
                            <h3 class="mb-0">{{ $stats['total_transferts'] ?? 0 }}</h3>
                            <small class="text-muted">{{ $stats['transferts_mois'] ?? 0 }} ce mois</small>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
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
                            <h6 class="text-muted mb-1">Valeur Stock</h6>
                            <h3 class="mb-0">{{ number_format($rapports['valeur_totale_stock'] ?? 0, 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Rapports -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Période</th>
                            <th>Date Génération</th>
                            <th>Statut</th>
                            <th>Fichier</th>
                            <th>Taille</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rapports['produits_critiques'] ?? [] as $produit)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $produit->designation }}</div>
                                <div class="text-muted small">{{ $produit->code ?? '' }}</div>
                            </td>
                            <td><span class="badge bg-danger">Stock critique</span></td>
                            <td>{{ $produit->stock_actuel }} / {{ $produit->stock_min }}</td>
                            <td>{{ $produit->updated_at ? \Carbon\Carbon::parse($produit->updated_at)->format('d/m/Y') : '-' }}</td>
                            <td><span class="badge bg-warning">Alerte</span></td>
                            <td>-</td>
                            <td>{{ number_format(($produit->stock_actuel ?? 0) * ($produit->prix_unitaire ?? 0), 0, ',', ' ') }} FCFA</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="text-muted">Aucun produit en stock critique</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Entrepôts les plus utilisés + Produits par catégorie -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-warehouse me-2"></i>Entrepôts les plus utilisés</h6>
                </div>
                <div class="card-body">
                    @if(count($rapports['entrepots_top'] ?? []) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Entrepôt</th>
                                    <th>Adresse</th>
                                    <th>Produits</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rapports['entrepots_top'] as $ent)
                                <tr>
                                    <td class="fw-bold">{{ $ent->nom }}</td>
                                    <td class="text-muted">{{ $ent->adresse ?? '-' }}</td>
                                    <td><span class="badge bg-info">{{ $ent->produits_count }}</span></td>
                                    <td>
                                        @if($ent->actif)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-warehouse fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">Aucun entrepôt enregistré</p>
                        <a href="{{ route('warehouse.create') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus me-1"></i>Créer un entrepôt
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-warning"><i class="fas fa-tags me-2"></i>Produits par catégorie</h6>
                </div>
                <div class="card-body">
                    @if(count($rapports['produits_par_categorie'] ?? []) > 0)
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
                                @foreach($rapports['produits_par_categorie'] as $cat)
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

    <!-- Transferts Récents (Mouvements de stock) -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-success"><i class="fas fa-exchange-alt me-2"></i>Transferts Récents</h6>
                </div>
                <div class="card-body">
                    @if(count($rapports['transferts_recents'] ?? []) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Source</th>
                                    <th>Destination</th>
                                    <th>Motif</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rapports['transferts_recents'] as $t)
                                <tr>
                                    <td>{{ $t->date ? \Carbon\Carbon::parse($t->date)->format('d/m/Y') : ($t->created_at ? $t->created_at->format('d/m/Y') : '-') }}</td>
                                    <td class="fw-bold">{{ $t->produit_nom ?? '-' }}</td>
                                    <td>{{ $t->quantite }}</td>
                                    <td>{{ $t->entrepot_source ?? '-' }}</td>
                                    <td>{{ $t->entrepot_destination ?? '-' }}</td>
                                    <td>{{ $t->motif ?? '-' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($t->statut ?? '') {
                                                'effectué', 'effectue', 'validé' => 'bg-success',
                                                'en_attente', 'en attente' => 'bg-warning',
                                                'annulé' => 'bg-secondary',
                                                default => 'bg-info',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $t->statut ?? '-' }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-exchange-alt fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">Aucun transfert enregistré</p>
                        <a href="{{ route('warehouse.transferts.create') }}" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-plus me-1"></i>Nouveau transfert
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-info"><i class="fas fa-bolt me-2"></i>Actions Rapides</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('warehouse.create') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-warehouse me-2"></i>Nouvel Entrepôt
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('warehouse.entrees.create') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-arrow-down me-2"></i>Nouvelle Entrée
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('warehouse.transferts.create') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-exchange-alt me-2"></i>Nouveau Transfert
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('warehouse.stock') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-boxes me-2"></i>Voir le Stock
                            </a>
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

<script>
function generateReport() {
    window.print();
}
</script>
@endsection
