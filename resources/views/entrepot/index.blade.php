@extends('layouts.app')

@section('title', 'Gestion de l\'Entrepôt - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Gestion de l'Entrepôt</h1>
            <a href="{{ route('warehouse.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Entrée/Sortie
            </a>
        </div>
    </div>

    <!-- KPI Entrepôt -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Articles en Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">150</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-gradient-warning text-white shadow">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Stock Faible</h5>
                        <h2>12</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-gradient-success text-white shadow">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-arrow-down fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Entrées Ce Mois</h5>
                        <h2>45</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-gradient-danger text-white shadow">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-arrow-up fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Sorties Ce Mois</h5>
                        <h2>38</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="row mb-4">
        <div class="col-md-4">
            <select class="form-select" id="categorieFilter">
                <option value="">Toutes les Catégories</option>
                <option value="Fournitures">Fournitures</option>
                <option value="Pièces">Pièces</option>
                <option value="Outils">Outils</option>
            </select>
        </div>
        <div class="col-md-4">
            <input class="form-control" type="search" placeholder="Rechercher un article..." id="searchFilter">
        </div>
        <div class="col-md-4">
            <button class="btn btn-outline-success" onclick="filterItems()">
                <i class="fas fa-search"></i> Filtrer
            </button>
        </div>
    </div>

    <!-- Cartes Articles -->
    <div class="row" id="itemCards">
        <div class="col-md-4 mb-4">
            <div class="card shadow hover-effect">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title">Huile Moteur 10W40</h6>
                            <p class="card-text text-muted">Pièces</p>
                            <p class="card-text">Quantité: 25 | Seuil: 10</p>
                            <p class="card-text">Localisation: Rayon A1</p>
                        </div>
                        <span class="badge bg-success">Disponible</span>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-success me-2">Entrée</button>
                        <button class="btn btn-sm btn-warning me-2">Sortie</button>
                        <button class="btn btn-sm btn-info">Détails</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow hover-effect">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title">Papier A4</h6>
                            <p class="card-text text-muted">Fournitures</p>
                            <p class="card-text">Quantité: 5 | Seuil: 20</p>
                            <p class="card-text">Localisation: Rayon B2</p>
                        </div>
                        <span class="badge bg-danger">Stock Faible</span>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-success me-2">Entrée</button>
                        <button class="btn btn-sm btn-warning me-2">Sortie</button>
                        <button class="btn btn-sm btn-info">Détails</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau Traditionnel -->
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Liste Détaillée</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Article</th>
                                    <th>Catégorie</th>
                                    <th>Quantité</th>
                                    <th>Seuil Min</th>
                                    <th>Localisation</th>
                                    <th>Dernière Mise à Jour</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="itemTable">
                                <tr>
                                    <td>Huile Moteur 10W40</td>
                                    <td>Pièces</td>
                                    <td>25</td>
                                    <td>10</td>
                                    <td>Rayon A1</td>
                                    <td>2023-10-15</td>
                                    <td>
                                        <button class="btn btn-sm btn-success">Entrée</button>
                                        <button class="btn btn-sm btn-warning">Sortie</button>
                                        <button class="btn btn-sm btn-info">Détails</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Papier A4</td>
                                    <td>Fournitures</td>
                                    <td>5</td>
                                    <td>20</td>
                                    <td>Rayon B2</td>
                                    <td>2023-10-14</td>
                                    <td>
                                        <button class="btn btn-sm btn-success">Entrée</button>
                                        <button class="btn btn-sm btn-warning">Sortie</button>
                                        <button class="btn btn-sm btn-info">Détails</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes Stock -->
    <div class="row mt-4">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Alertes Stock Faible</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Papier A4 : Quantité (5) inférieure au seuil (20). Réapprovisionnement recommandé.
                    </div>
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> Toner Imprimante : Stock épuisé. Commande urgente nécessaire.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-effect {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-effect:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>

<script>
function filterItems() {
    const categorie = document.getElementById('categorieFilter').value;
    const search = document.getElementById('searchFilter').value;
    // Logique pour filtrer les articles
    console.log('Filtrage:', categorie, search);
}
</script>
@endsection
