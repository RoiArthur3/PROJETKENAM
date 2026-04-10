@extends('layouts.app')

@section('title', 'Nouvelle Entrée Stock - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-arrow-down me-2"></i>Nouvelle Entrée de Stock
                    </h6>
                    <a href="{{ route('stock.entrees') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('stock.entrees.store') }}" id="entreeForm">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Informations générales</h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label required">Date</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-calendar"></i></span>
                                    <input type="date" name="date" id="date" 
                                           class="form-control @error('date') is-invalid @enderror" 
                                           value="{{ old('date', date('Y-m-d')) }}" required>
                                </div>
                                @error('date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label required">Type d'entrée</label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="Achat" {{ old('type') == 'Achat' ? 'selected' : '' }}>Achat</option>
                                    <option value="Retour" {{ old('type') == 'Retour' ? 'selected' : '' }}>Retour</option>
                                    <option value="Ajustement" {{ old('type') == 'Ajustement' ? 'selected' : '' }}>Ajustement</option>
                                    <option value="Transfert" {{ old('type') == 'Transfert' ? 'selected' : '' }}>Transfert</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Produit</h5>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="produit_nom" class="form-label required">Nom du produit</label>
                                <input type="text" name="produit_nom" id="produit_nom" 
                                       class="form-control @error('produit_nom') is-invalid @enderror" 
                                       value="{{ old('produit_nom') }}" 
                                       placeholder="Ex: Pneu 195/65 R15, Huile moteur 5W30..." required>
                                @error('produit_nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">Saisissez le nom complet du produit</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="quantite" class="form-label required">Quantité</label>
                                <input type="number" name="quantite" id="quantite" 
                                       class="form-control @error('quantite') is-invalid @enderror" 
                                       value="{{ old('quantite') }}" 
                                       min="0.01" step="0.01" required
                                       oninput="calculerMontant()">
                                @error('quantite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="prix_unitaire" class="form-label">Prix unitaire (FCFA)</label>
                                <input type="number" name="prix_unitaire" id="prix_unitaire" 
                                       class="form-control @error('prix_unitaire') is-invalid @enderror" 
                                       value="{{ old('prix_unitaire') }}" 
                                       min="0" step="0.01"
                                       oninput="calculerMontant()">
                                @error('prix_unitaire')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="montant_total" class="form-label">Montant total (FCFA)</label>
                                <input type="number" id="montant_total" 
                                       class="form-control bg-light" readonly>
                                <small class="form-text">Calculé automatiquement</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Informations complémentaires</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fournisseur" class="form-label">Fournisseur</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-truck"></i></span>
                                    <input type="text" name="fournisseur" id="fournisseur" 
                                           class="form-control @error('fournisseur') is-invalid @enderror" 
                                           value="{{ old('fournisseur') }}" 
                                           placeholder="Nom du fournisseur">
                                </div>
                                @error('fournisseur')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence document</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                                    <input type="text" name="reference" id="reference" 
                                           class="form-control @error('reference') is-invalid @enderror" 
                                           value="{{ old('reference') }}" 
                                           placeholder="N° bon de livraison, facture...">
                                </div>
                                @error('reference')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" rows="3" 
                                          class="form-control @error('notes') is-invalid @enderror" 
                                          placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                            <a href="{{ route('stock.entrees') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer l'entrée
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculerMontant() {
    const quantite = parseFloat(document.getElementById('quantite').value) || 0;
    const prixUnitaire = parseFloat(document.getElementById('prix_unitaire').value) || 0;
    const montantTotal = quantite * prixUnitaire;
    
    document.getElementById('montant_total').value = montantTotal > 0 ? Math.round(montantTotal) : '';
}

// Calculer au chargement si valeurs présentes
document.addEventListener('DOMContentLoaded', function() {
    calculerMontant();
});
</script>
@endsection
