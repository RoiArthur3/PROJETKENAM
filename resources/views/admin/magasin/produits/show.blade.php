@extends('layouts.app')

@section('title', 'Détails Produit - Module Magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        Détails du Produit
                    </h4>
                </div>
                <div class="card-body">
                    
                    <!-- Informations du produit -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Informations générales</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Code:</strong></td>
                                            <td>{{ $produit->code }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Désignation:</strong></td>
                                            <td>{{ $produit->designation }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Catégorie:</strong></td>
                                            <td>{{ $produit->categorie }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Unité:</strong></td>
                                            <td>{{ $produit->unite }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Informations de stock</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Stock Actuel:</strong></td>
                                            <td>
                                                <span class="badge {{ $produit->stock_actuel <= $produit->stock_min ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $produit->stock_actuel }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Stock Minimum:</strong></td>
                                            <td>{{ $produit->stock_min }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Prix Unitaire:</strong></td>
                                            <td>{{ number_format($produit->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Valeur Stock:</strong></td>
                                            <td>{{ number_format($produit->prix_unitaire * $produit->stock_actuel, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    @if($produit->description)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Description</h6>
                        </div>
                        <div class="card-body">
                            <p>{{ $produit->description }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Mouvements récents -->
                    @if($mouvements->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Mouvements récents</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Quantité</th>
                                            <th>Référence</th>
                                            <th>Motif</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($mouvements as $mouvement)
                                            <tr>
                                                <td>{{ $mouvement->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <span class="badge {{ $mouvement->type == 'entree' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $mouvement->type == 'entree' ? 'Entrée' : 'Sortie' }}
                                                    </span>
                                                </td>
                                                <td>{{ $mouvement->quantite }}</td>
                                                <td>{{ $mouvement->reference ?? '-' }}</td>
                                                <td>{{ $mouvement->motif ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('magasin.inventaire') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à l'inventaire
                        </a>
                        <div>
                            <button class="btn btn-outline-primary" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Imprimer
                            </button>
                            <a href="{{ route('magasin.produits.edit', $produit->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Modifier
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
