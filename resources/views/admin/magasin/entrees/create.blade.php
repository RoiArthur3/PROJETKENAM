@extends('layouts.app')

@section('title', 'Nouvelle Entrée - Module Magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-arrow-down me-2"></i>
                        Nouvelle Entrée de Stock
                    </h4>
                </div>
                <div class="card-body">

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('magasin.entrees.store') }}" method="POST">
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
                                    <select class="form-select @error('produit_id') is-invalid @enderror" id="produit_id" name="produit_id" required>
                                        <option value="">Sélectionner un produit</option>
                                        @foreach($produits as $produit)
                                            <option value="{{ $produit->id }}" {{ old('produit_id') == $produit->id ? 'selected' : '' }}>
                                                {{ $produit->designation }} ({{ $produit->code ?? 'N/A' }}) - Stock: {{ $produit->stock_actuel }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(isset($produits) && $produits->count() === 0)
                                        <div class="alert alert-warning mt-2 mb-0">
                                            Aucun produit disponible. Créez un produit puis revenez enregistrer l'entrée.
                                        </div>
                                    @endif
                                    @error('produit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="quantite" class="form-label">Quantité *</label>
                                    <input type="number" class="form-control @error('quantite') is-invalid @enderror" id="quantite" name="quantite" min="1" value="{{ old('quantite') }}" required>
                                    @error('quantite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="prix_unitaire" class="form-label">Prix Unitaire (FCFA)</label>
                                    <input type="number" class="form-control @error('prix_unitaire') is-invalid @enderror" id="prix_unitaire" name="prix_unitaire" min="0" step="0.01" value="{{ old('prix_unitaire') }}">
                                    @error('prix_unitaire')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reference_entree" class="form-label">Référence Entrée</label>
                                    <input type="text" class="form-control" id="reference_entree" name="reference_entree"
                                           value="{{ old('reference_entree', 'ENT-' . date('YmdHis')) }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="fournisseur" class="form-label">Fournisseur</label>
                                    @if(isset($fournisseurs) && $fournisseurs->count() > 0)
                                        <select class="form-select @error('fournisseur') is-invalid @enderror" id="fournisseur" name="fournisseur">
                                            <option value="">Sélectionner un fournisseur</option>
                                            @foreach($fournisseurs as $fournisseur)
                                                <option value="{{ $fournisseur->raison_sociale }}" {{ old('fournisseur') == $fournisseur->raison_sociale ? 'selected' : '' }}>
                                                    {{ $fournisseur->raison_sociale }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" class="form-control @error('fournisseur') is-invalid @enderror" id="fournisseur" name="fournisseur"
                                               placeholder="Nom du fournisseur" value="{{ old('fournisseur') }}">
                                    @endif
                                    @error('fournisseur')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="motif" class="form-label">Motif / Observation</label>
                                    <textarea class="form-control @error('motif') is-invalid @enderror" id="motif" name="motif" rows="3"
                                              placeholder="Décrivez le motif de cette entrée...">{{ old('motif') }}</textarea>
                                    @error('motif')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light mb-3" id="produitInfo" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title">Informations du produit</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Stock actuel:</strong> <span id="stockActuel"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Prix unitaire actuel:</strong> <span id="prixActuel"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Nouveau stock:</strong> <span id="nouveauStock" class="text-success fw-bold"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('magasin.entrees') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Enregistrer l'Entrée
                            </button>
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
    const produitInfo = document.getElementById('produitInfo');
    const produits = @json($produits);

    function updateInfo() {
        const produitId = produitSelect.value;
        const quantite = parseInt(quantiteInput.value) || 0;

        if (produitId) {
            const produit = produits.find(p => p.id == produitId);
            if (produit) {
                document.getElementById('stockActuel').textContent = produit.stock_actuel;
                document.getElementById('prixActuel').textContent = new Intl.NumberFormat('fr-FR').format(produit.prix_unitaire) + ' FCFA';
                document.getElementById('nouveauStock').textContent = (produit.stock_actuel + quantite);
                produitInfo.style.display = 'block';
            }
        } else {
            produitInfo.style.display = 'none';
        }
    }

    produitSelect.addEventListener('change', updateInfo);
    quantiteInput.addEventListener('input', updateInfo);
});
</script>
@endsection
