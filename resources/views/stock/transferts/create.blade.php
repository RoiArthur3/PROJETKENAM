@extends('layouts.app')

@section('title', 'Nouveau Transfert - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-exchange-alt me-2"></i>Nouveau Transfert de Stock
                    </h6>
                    <a href="{{ route('warehouse.transferts') }}" class="btn btn-sm btn-secondary">
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

                    <form method="POST" action="{{ route('warehouse.transferts.store') }}" id="transfertForm">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Informations générales</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label required">Date du transfert</label>
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
                                <label for="quantite" class="form-label required">Quantité</label>
                                <input type="number" name="quantite" id="quantite"
                                       class="form-control @error('quantite') is-invalid @enderror"
                                       value="{{ old('quantite') }}"
                                       min="0.01" step="0.01" required>
                                @error('quantite')
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
                                <small class="form-text">Saisissez le nom complet du produit à transférer</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Entrepôts</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="entrepot_source" class="form-label required">Entrepôt source</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-warehouse"></i></span>
                                    <select name="entrepot_source" id="entrepot_source"
                                            class="form-select @error('entrepot_source') is-invalid @enderror" required>
                                        <option value="">-- Sélectionner --</option>
                                        <option value="Entrepôt Principal" {{ old('entrepot_source') == 'Entrepôt Principal' ? 'selected' : '' }}>Entrepôt Principal</option>
                                        <option value="Entrepôt Secondaire" {{ old('entrepot_source') == 'Entrepôt Secondaire' ? 'selected' : '' }}>Entrepôt Secondaire</option>
                                        <option value="Entrepôt Zone Nord" {{ old('entrepot_source') == 'Entrepôt Zone Nord' ? 'selected' : '' }}>Entrepôt Zone Nord</option>
                                        <option value="Entrepôt Zone Sud" {{ old('entrepot_source') == 'Entrepôt Zone Sud' ? 'selected' : '' }}>Entrepôt Zone Sud</option>
                                    </select>
                                </div>
                                @error('entrepot_source')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="entrepot_destination" class="form-label required">Entrepôt destination</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-warehouse"></i></span>
                                    <select name="entrepot_destination" id="entrepot_destination"
                                            class="form-select @error('entrepot_destination') is-invalid @enderror" required>
                                        <option value="">-- Sélectionner --</option>
                                        <option value="Entrepôt Principal" {{ old('entrepot_destination') == 'Entrepôt Principal' ? 'selected' : '' }}>Entrepôt Principal</option>
                                        <option value="Entrepôt Secondaire" {{ old('entrepot_destination') == 'Entrepôt Secondaire' ? 'selected' : '' }}>Entrepôt Secondaire</option>
                                        <option value="Entrepôt Zone Nord" {{ old('entrepot_destination') == 'Entrepôt Zone Nord' ? 'selected' : '' }}>Entrepôt Zone Nord</option>
                                        <option value="Entrepôt Zone Sud" {{ old('entrepot_destination') == 'Entrepôt Zone Sud' ? 'selected' : '' }}>Entrepôt Zone Sud</option>
                                    </select>
                                </div>
                                @error('entrepot_destination')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Note:</strong> L'entrepôt de destination doit être différent de l'entrepôt source.
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Informations complémentaires</h5>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="motif" class="form-label">Motif du transfert</label>
                                <textarea name="motif" id="motif" rows="3"
                                          class="form-control @error('motif') is-invalid @enderror"
                                          placeholder="Raison du transfert, besoin spécifique...">{{ old('motif') }}</textarea>
                                @error('motif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                            <a href="{{ route('warehouse.transferts') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Créer le transfert
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Vérifier que source et destination sont différents
document.getElementById('transfertForm').addEventListener('submit', function(e) {
    const source = document.getElementById('entrepot_source').value;
    const destination = document.getElementById('entrepot_destination').value;

    if (source && destination && source === destination) {
        e.preventDefault();
        alert('L\'entrepôt de destination doit être différent de l\'entrepôt source.');
        document.getElementById('entrepot_destination').focus();
    }
});
</script>
@endsection
