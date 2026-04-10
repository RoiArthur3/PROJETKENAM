@extends('layouts.app')

@section('title', 'Nouvelle Sortie - Module Magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-arrow-up me-2"></i>
                        Nouvelle Sortie de Stock
                    </h4>
                </div>
                <div class="card-body">

                    <form action="{{ route('magasin.sorties.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="produit_id" class="form-label mb-0">Produit *</label>
                                        <a href="{{ route('magasin.produits.create') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-plus me-1"></i>Ajouter un produit
                                        </a>
                                    </div>
                                    <select class="form-select" id="produit_id" name="produit_id" required>
                                        <option value="">Sélectionner un produit</option>
                                        @foreach($produits as $produit)
                                            <option value="{{ $produit->id }}" data-stock="{{ $produit->stock_actuel }}">
                                                {{ $produit->designation }} ({{ $produit->code ?? 'N/A' }}) - Stock: {{ $produit->stock_actuel }} {{ $produit->unite }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(isset($produits) && $produits->count() === 0)
                                        <div class="alert alert-warning mt-2 mb-0">
                                            Aucun produit en stock. Créez un produit puis approvisionnez le stock pour effectuer une sortie.
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="quantite" class="form-label">Quantité *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="quantite" name="quantite" min="1" required>
                                        <span class="input-group-text" id="unite">Unité</span>
                                    </div>
                                    <small class="text-muted" id="stockInfo"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reference_sortie" class="form-label">Référence Sortie</label>
                                    <input type="text" class="form-control" id="reference_sortie" name="reference_sortie"
                                           value="SORT-{{ date('YmdHis') }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="destination" class="form-label">Destination *</label>
                                    <input type="text" class="form-control" id="destination" name="destination"
                                           placeholder="Ex: Chantier A, Bureau, etc." required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="motif" class="form-label">Motif de la sortie *</label>
                            <textarea class="form-control" id="motif" name="motif" rows="3" required
                                      placeholder="Décrivez le motif de cette sortie..."></textarea>
                        </div>

                        <!-- Informations du produit sélectionné -->
                        <div class="card bg-light mb-3" id="produitInfo" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title">Informations du produit</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Stock actuel:</strong> <span id="stockActuel"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Stock minimum:</strong> <span id="stockMin"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Prix unitaire:</strong> <span id="prixUnitaire"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('magasin.sorties.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-warning" id="submitBtn">
                                        <i class="fas fa-save me-2"></i>Enregistrer la Sortie
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
    const produitSelect = document.getElementById('produit_id');
    const quantiteInput = document.getElementById('quantite');
    const stockInfo = document.getElementById('stockInfo');
    const produitInfo = document.getElementById('produitInfo');
    const submitBtn = document.getElementById('submitBtn');

    // Données des produits
    const produits = @json($produits);

    produitSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const produitId = this.value;

        if (produitId) {
            const produit = produits.find(p => p.id == produitId);
            if (produit) {
                // Afficher les informations du produit
                document.getElementById('stockActuel').textContent = produit.stock_actuel + ' ' + produit.unite;
                document.getElementById('stockMin').textContent = produit.stock_min + ' ' + produit.unite;
                document.getElementById('prixUnitaire').textContent = new Intl.NumberFormat('fr-FR').format(produit.prix_unitaire) + ' FCFA';
                document.getElementById('unite').textContent = produit.unite;

                produitInfo.style.display = 'block';
                stockInfo.textContent = 'Stock disponible: ' + produit.stock_actuel + ' ' + produit.unite;

                // Valider la quantité
                validateQuantite(produit.stock_actuel);
            }
        } else {
            produitInfo.style.display = 'none';
            stockInfo.textContent = '';
            document.getElementById('unite').textContent = 'Unité';
        }
    });

    quantiteInput.addEventListener('input', function() {
        const selectedOption = produitSelect.options[produitSelect.selectedIndex];
        const stockDisponible = parseInt(selectedOption.dataset.stock) || 0;
        validateQuantite(stockDisponible);
    });

    function validateQuantite(stockDisponible) {
        const quantite = parseInt(quantiteInput.value) || 0;

        if (quantite > stockDisponible) {
            stockInfo.textContent = '⚠️ Stock insuffisant! Disponible: ' + stockDisponible;
            stockInfo.className = 'text-danger';
            submitBtn.disabled = true;
        } else if (quantite === 0) {
            stockInfo.textContent = 'Stock disponible: ' + stockDisponible;
            stockInfo.className = 'text-muted';
            submitBtn.disabled = true;
        } else {
            stockInfo.textContent = '✅ Quantité valide';
            stockInfo.className = 'text-success';
            submitBtn.disabled = false;
        }
    }
});
</script>
@endsection
