@extends('layouts.app')

@section('title', 'Module Magasin - Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-warehouse me-2"></i>
                        Dashboard - Module Magasin
                    </h4>
                </div>
                <div class="card-body">
                    
                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Produits</h5>
                                    <h3>{{ $stats['total_produits'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Entrées Aujourd'hui</h5>
                                    <h3>{{ $stats['total_entrees'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Sorties Aujourd'hui</h5>
                                    <h3>{{ $stats['total_sorties'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Valeur Stock</h5>
                                    <h3>{{ number_format($stats['valeur_stock'] ?? 0, 0, ',', ' ') }} FCFA</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Actions Rapides</h5>
                            <div class="btn-group" role="group">
                                <a href="{{ route('magasin.inventaire') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list me-2"></i>Inventaire
                                </a>
                                <a href="{{ route('magasin.entrees') }}" class="btn btn-outline-success">
                                    <i class="fas fa-arrow-down me-2"></i>Entrées
                                </a>
                                <a href="{{ route('magasin.sorties') }}" class="btn btn-outline-warning">
                                    <i class="fas fa-arrow-up me-2"></i>Sorties
                                </a>
                                <a href="{{ route('magasin.rapports') }}" class="btn btn-outline-info">
                                    <i class="fas fa-chart-bar me-2"></i>Rapports
                                </a>
                                <a href="{{ route('magasin.produits.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Nouveau Produit
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Mouvements récents -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Entrées Récentes</h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($recent_entrees) && $recent_entrees->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Produit</th>
                                                        <th>Quantité</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recent_entrees as $entree)
                                                        <tr>
                                                            <td>{{ $entree->produit_nom ?? 'N/A' }}</td>
                                                            <td>{{ $entree->quantite ?? 0 }}</td>
                                                            <td>{{ isset($entree->created_at) ? $entree->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">Aucune entrée récente</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Sorties Récentes</h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($recent_sorties) && $recent_sorties->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Produit</th>
                                                        <th>Quantité</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recent_sorties as $sortie)
                                                        <tr>
                                                            <td>{{ $sortie->produit_nom ?? 'N/A' }}</td>
                                                            <td>{{ $sortie->quantite ?? 0 }}</td>
                                                            <td>{{ isset($sortie->created_at) ? $sortie->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">Aucune sortie récente</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
