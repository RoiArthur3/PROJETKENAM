@extends('layouts.app')

@section('title', 'Modifier Produit - Module Magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Modifier le Produit
                    </h4>
                </div>
                <div class="card-body">
                    
                    <form action="{{ route('magasin.produits.update', $produit->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="designation" class="form-label">Désignation du produit *</label>
                                    <input type="text" class="form-control" id="designation" name="designation" 
                                           value="{{ $produit->designation }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code *</label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="{{ $produit->code }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="categorie" class="form-label">Catégorie *</label>
                                    <select class="form-select" id="categorie" name="categorie" required>
                                        <option value="">Sélectionner une catégorie</option>
                                        <option value="Matériel" {{ $produit->categorie == 'Matériel' ? 'selected' : '' }}>Matériel</option>
                                        <option value="Consommable" {{ $produit->categorie == 'Consommable' ? 'selected' : '' }}>Consommable</option>
                                        <option value="Outillage" {{ $produit->categorie == 'Outillage' ? 'selected' : '' }}>Outillage</option>
                                        <option value="Équipement" {{ $produit->categorie == 'Équipement' ? 'selected' : '' }}>Équipement</option>
                                        <option value="Pièce détachée" {{ $produit->categorie == 'Pièce détachée' ? 'selected' : '' }}>Pièce détachée</option>
                                        <option value="Autre" {{ $produit->categorie == 'Autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="unite" class="form-label">Unité *</label>
                                    <select class="form-select" id="unite" name="unite" required>
                                        <option value="">Sélectionner une unité</option>
                                        <option value="Unité" {{ $produit->unite == 'Unité' ? 'selected' : '' }}>Unité</option>
                                        <option value="Pièce" {{ $produit->unite == 'Pièce' ? 'selected' : '' }}>Pièce</option>
                                        <option value="Kg" {{ $produit->unite == 'Kg' ? 'selected' : '' }}>Kg</option>
                                        <option value="Litre" {{ $produit->unite == 'Litre' ? 'selected' : '' }}>Litre</option>
                                        <option value="Mètre" {{ $produit->unite == 'Mètre' ? 'selected' : '' }}>Mètre</option>
                                        <option value="Mètre carré" {{ $produit->unite == 'Mètre carré' ? 'selected' : '' }}>Mètre carré</option>
                                        <option value="Mètre cube" {{ $produit->unite == 'Mètre cube' ? 'selected' : '' }}>Mètre cube</option>
                                        <option value="Boîte" {{ $produit->unite == 'Boîte' ? 'selected' : '' }}>Boîte</option>
                                        <option value="Carton" {{ $produit->unite == 'Carton' ? 'selected' : '' }}>Carton</option>
                                        <option value="Palette" {{ $produit->unite == 'Palette' ? 'selected' : '' }}>Palette</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prix_unitaire" class="form-label">Prix Unitaire (FCFA) *</label>
                                    <input type="number" class="form-control" id="prix_unitaire" name="prix_unitaire" 
                                           value="{{ $produit->prix_unitaire }}" min="0" step="0.01" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="stock_actuel" class="form-label">Quantité en Stock *</label>
                                    <input type="number" class="form-control" id="stock_actuel" name="stock_actuel" 
                                           value="{{ $produit->stock_actuel }}" min="0" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="stock_min" class="form-label">Stock Minimum *</label>
                                    <input type="number" class="form-control" id="stock_min" name="stock_min" 
                                           value="{{ $produit->stock_min }}" min="0" required>
                                    <small class="text-muted">Alerte lorsque le stock atteint ce niveau</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ $produit->description ?? '' }}</textarea>
                        </div>
                        
                        <!-- Aperçu du produit -->
                        <div class="card bg-light mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Aperçu du produit</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Valeur totale:</strong> 
                                        <span id="valeurTotale">{{ number_format($produit->prix_unitaire * $produit->stock_actuel, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Statut:</strong> 
                                        <span id="statutStock" class="badge {{ $produit->stock_actuel <= $produit->stock_min ? 'bg-danger' : 'bg-success' }}">
                                            {{ $produit->stock_actuel <= $produit->stock_min ? 'Stock Critique' : 'Normal' }}
                                        </span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Marge de sécurité:</strong> 
                                        <span id="margeSecurite">{{ $produit->stock_actuel - $produit->stock_min }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('magasin.produits.show', $produit->id) }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const prixUnitaire = document.getElementById('prix_unitaire');
    const stockActuel = document.getElementById('stock_actuel');
    const stockMin = document.getElementById('stock_min');
    const valeurTotale = document.getElementById('valeurTotale');
    const statutStock = document.getElementById('statutStock');
    const margeSecurite = document.getElementById('margeSecurite');
    
    function updateApercu() {
        const prix = parseFloat(prixUnitaire.value) || 0;
        const quantite = parseFloat(stockActuel.value) || 0;
        const min = parseFloat(stockMin.value) || 0;
        
        // Calculer la valeur totale
        const valeur = prix * quantite;
        valeurTotale.textContent = new Intl.NumberFormat('fr-FR').format(valeur) + ' FCFA';
        
        // Calculer la marge de sécurité
        const marge = quantite - min;
        margeSecurite.textContent = marge;
        
        // Déterminer le statut du stock
        if (quantite <= min) {
            statutStock.textContent = 'Stock Critique';
            statutStock.className = 'badge bg-danger';
        } else if (quantite <= (min * 1.5)) {
            statutStock.textContent = 'Stock Bas';
            statutStock.className = 'badge bg-warning';
        } else {
            statutStock.textContent = 'Normal';
            statutStock.className = 'badge bg-success';
        }
    }
    
    // Écouter les changements
    prixUnitaire.addEventListener('input', updateApercu);
    stockActuel.addEventListener('input', updateApercu);
    stockMin.addEventListener('input', updateApercu);
});
</script>
@endsection
