@extends('layouts.app')

@section('title', 'Nouveau Produit - Module Magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus me-2"></i>
                        Nouveau Produit
                    </h4>
                </div>
                <div class="card-body">

                    <form action="{{ route('magasin.produits.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="designation" class="form-label">Désignation du produit *</label>
                                    <input type="text" class="form-control" id="designation" name="designation" required>
                                </div>

                                <div class="mb-3">
                                    <label for="code" class="form-label">Code *</label>
                                    <input type="text" class="form-control" id="code" name="code" required>
                                </div>

                                <div class="mb-3">
                                    <label for="categorie" class="form-label">Catégorie *</label>
                                    <select class="form-select" id="categorie" name="categorie" required>
                                        <option value="">Sélectionner une catégorie</option>
                                        <option value="Matériel">Matériel</option>
                                        <option value="Consommable">Consommable</option>
                                        <option value="Outillage">Outillage</option>
                                        <option value="Équipement">Équipement</option>
                                        <option value="Pièce détachée">Pièce détachée</option>
                                        <option value="Autre">Autre</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="unite" class="form-label">Unité *</label>
                                    <select class="form-select" id="unite" name="unite" required>
                                        <option value="">Sélectionner une unité</option>
                                        <option value="Unité">Unité</option>
                                        <option value="Pièce">Pièce</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Litre">Litre</option>
                                        <option value="Mètre">Mètre</option>
                                        <option value="Mètre carré">Mètre carré</option>
                                        <option value="Mètre cube">Mètre cube</option>
                                        <option value="Boîte">Boîte</option>
                                        <option value="Carton">Carton</option>
                                        <option value="Palette">Palette</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prix_unitaire" class="form-label">Prix Unitaire (FCFA) *</label>
                                    <input type="number" class="form-control" id="prix_unitaire" name="prix_unitaire"
                                           min="0" step="0.01" required>
                                </div>

                                <div class="mb-3">
                                    <label for="stock_actuel" class="form-label">Quantité en Stock *</label>
                                    <input type="number" class="form-control" id="stock_actuel" name="stock_actuel"
                                           min="0" required>
                                </div>

                                <div class="mb-3">
                                    <label for="stock_min" class="form-label">Stock Minimum *</label>
                                    <input type="number" class="form-control" id="stock_min" name="stock_min"
                                           min="0" required>
                                    <small class="text-muted">Alerte lorsque le stock atteint ce niveau</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="Description détaillée du produit..."></textarea>
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
                                        <span id="valeurTotale">0 FCFA</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Statut:</strong>
                                        <span id="statutStock" class="badge bg-success">Normal</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Marge de sécurité:</strong>
                                        <span id="margeSecurite">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('magasin.dashboard') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Annuler
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

    // Générer un code automatiquement
    const designation = document.getElementById('designation');
    const code = document.getElementById('code');

    designation.addEventListener('blur', function() {
        if (!code.value && this.value) {
            const prefix = this.value.substring(0, 3).toUpperCase();
            const timestamp = Date.now().toString().slice(-6);
            code.value = 'PROD-' + prefix + '-' + timestamp;
        }
    });
});
</script>
@endsection
