@extends('layouts.app')

@section('title', 'Magasin - Vente de Produits')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="mb-0">
                <i class="fas fa-store text-primary me-2"></i>
                Magasin - Vente de Produits
            </h4>
            <small class="text-muted">Interface de vente et gestion des produits en magasin.</small>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-success btn-lg" onclick="window.location.href='{{ route('shop.sell') }}'">
                <i class="fas fa-cash-register me-2"></i>
                Nouvelle Vente
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Statistiques du magasin -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line text-success me-2"></i>
                        Statistiques du Magasin
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="text-muted small">Produits en Stock</div>
                                <div class="h3 text-success mb-0">0</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="text-muted small">Ventes Aujourd'hui</div>
                                <div class="h3 text-primary mb-0">0</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="text-muted small">Chiffre d'Affaires</div>
                                <div class="h3 text-info mb-0">0 FCFA</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="text-muted small">Clients Servis</div>
                                <div class="h3 text-warning mb-0">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produits disponibles -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-boxes text-primary me-2"></i>
                        Produits Disponibles
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Liste des produits disponibles en magasin...</p>
                    <!-- Ici on affichera la liste des produits -->
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success btn-lg">
                            <i class="fas fa-plus-circle me-2"></i>
                            Nouvelle Vente
                        </button>
                        <button type="button" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>
                            Rechercher Produit
                        </button>
                        <button type="button" class="btn btn-info">
                            <i class="fas fa-history me-2"></i>
                            Historique Ventes
                        </button>
                        <button type="button" class="btn btn-secondary">
                            <i class="fas fa-cog me-2"></i>
                            Paramètres Magasin
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dernières ventes -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-clock text-info me-2"></i>
                        Dernières Ventes
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Aucune vente récente...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
