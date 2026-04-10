@extends('layouts.app')

@section('title', 'Ajouter un Produit - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Ajouter un Nouveau Produit</h1>
        <div>
            <a href="{{ route('magasin.inventaire') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à l'inventaire
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>Informations du Produit
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('magasin.produits.store') }}">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Référence -->
                            <div class="col-md-6">
                                <label for="reference" class="form-label">Référence *</label>
                                <input type="text" class="form-control" id="reference" name="reference" 
                                       placeholder="Ex: PROD-001" required>
                                <div class="form-text">Référence unique du produit</div>
                            </div>

                            <!-- Nom du produit -->
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom du Produit *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       placeholder="Ex: Ordinateur Portable" required>
                            </div>

                            <!-- Catégorie -->
                            <div class="col-md-6">
                                <label for="categorie" class="form-label">Catégorie *</label>
                                <select class="form-select" id="categorie" name="categorie" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    <option value="Informatique">Informatique</option>
                                    <option value="Fournitures">Fournitures</option>
                                    <option value="Consommables">Consommables</option>
                                    <option value="Matériel">Matériel</option>
                                    <option value="Logiciel">Logiciel</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>

                            <!-- Unité -->
                            <div class="col-md-6">
                                <label for="unite" class="form-label">Unité *</label>
                                <select class="form-select" id="unite" name="unite" required>
                                    <option value="">Sélectionner une unité</option>
                                    <option value="Unité">Unité</option>
                                    <option value="Rame">Rame</option>
                                    <option value="Boîte">Boîte</option>
                                    <option value="Paquet">Paquet</option>
                                    <option value="Litre">Litre</option>
                                    <option value="Kg">Kg</option>
                                    <option value="Mètre">Mètre</option>
                                </select>
                            </div>

                            <!-- Quantité initiale -->
                            <div class="col-md-4">
                                <label for="quantite" class="form-label">Quantité Initiale *</label>
                                <input type="number" class="form-control" id="quantite" name="quantite" 
                                       min="0" step="1" value="0" required>
                            </div>

                            <!-- Seuil d'alerte -->
                            <div class="col-md-4">
                                <label for="seuil_alerte" class="form-label">Seuil d'Alerte *</label>
                                <input type="number" class="form-control" id="seuil_alerte" name="seuil_alerte" 
                                       min="0" step="1" value="5" required>
                                <div class="form-text">Alerte quand le stock atteint ce niveau</div>
                            </div>

                            <!-- Prix unitaire -->
                            <div class="col-md-4">
                                <label for="prix_unitaire" class="form-label">Prix Unitaire (FCFA) *</label>
                                <input type="number" class="form-control" id="prix_unitaire" name="prix_unitaire" 
                                       min="0" step="0.01" required>
                            </div>

                            <!-- Emplacement -->
                            <div class="col-md-6">
                                <label for="emplacement" class="form-label">Emplacement *</label>
                                <select class="form-select" id="emplacement" name="emplacement" required>
                                    <option value="">Sélectionner un emplacement</option>
                                    <option value="Magasin A">Magasin A</option>
                                    <option value="Magasin B">Magasin B</option>
                                    <option value="Magasin C">Magasin C</option>
                                    <option value="Réserve">Réserve</option>
                                    <option value="Extérieur">Extérieur</option>
                                </select>
                            </div>

                            <!-- Fournisseur -->
                            <div class="col-md-6">
                                <label for="fournisseur" class="form-label">Fournisseur Principal</label>
                                <input type="text" class="form-control" id="fournisseur" name="fournisseur" 
                                       placeholder="Nom du fournisseur">
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                          placeholder="Description détaillée du produit (caractéristiques, usage, etc.)"></textarea>
                            </div>

                            <!-- Boutons -->
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('magasin.inventaire') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer le Produit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Informations supplémentaires -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Conseils
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Utilisez une référence unique pour chaque produit
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Définissez un seuil d'alerte approprié
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Indiquez l'emplacement de stockage
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Ajoutez une description détaillée
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Stock Actuel
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h4 class="text-primary">{{ \App\Models\Stock::count() ?? 0 }}</h4>
                        <p class="text-muted mb-0">Produits en stock</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
