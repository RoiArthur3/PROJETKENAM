@extends('layouts.app')

@section('title', 'Nouveau Contrôle - KENAM SERVICES')

@push('styles')
<style>
    .form-check-label {
        cursor: pointer;
    }
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nouveau Contrôle</h1>
        <a href="{{ route('controleur.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Formulaire de Contrôle</h6>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('controleur.store') }}" method="POST" id="controleForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vehicule_id" class="form-label">Véhicule <span class="text-danger">*</span></label>
                                <select class="form-select @error('vehicule_id') is-invalid @enderror" id="vehicule_id" name="vehicule_id" required>
                                    <option value="">Sélectionner un véhicule</option>
                                    @foreach($vehicules as $vehicule)
                                        <option value="{{ $vehicule->id }}" {{ old('vehicule_id') == $vehicule->id ? 'selected' : '' }}>
                                            {{ $vehicule->immatriculation }} - {{ $vehicule->marque }} {{ $vehicule->modele }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicule_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="type_controle" class="form-label">Type de contrôle <span class="text-danger">*</span></label>
                                <select class="form-select @error('type_controle') is-invalid @enderror" id="type_controle" name="type_controle" required>
                                    <option value="technique" {{ old('type_controle') == 'technique' ? 'selected' : '' }}>Contrôle technique</option>
                                    <option value="securite" {{ old('type_controle') == 'securite' ? 'selected' : '' }}>Contrôle de sécurité</option>
                                    <option value="entretien" {{ old('type_controle') == 'entretien' ? 'selected' : '' }}>Contrôle d'entretien</option>
                                    <option value="autre" {{ old('type_controle') == 'autre' ? 'selected' : '' }}>Autre type de contrôle</option>
                                </select>
                                @error('type_controle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="date_controle" class="form-label">Date du contrôle <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_controle') is-invalid @enderror" 
                                       id="date_controle" name="date_controle" 
                                       value="{{ old('date_controle', now()->format('Y-m-d')) }}" required>
                                @error('date_controle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="kilometrage" class="form-label">Kilométrage</label>
                                <input type="number" class="form-control @error('kilometrage') is-invalid @enderror" 
                                       id="kilometrage" name="kilometrage" 
                                       value="{{ old('kilometrage') }}" min="0">
                                @error('kilometrage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="prochain_controle" class="form-label">Prochain contrôle</label>
                                <input type="date" class="form-control @error('prochain_controle') is-invalid @enderror" 
                                       id="prochain_controle" name="prochain_controle" 
                                       value="{{ old('prochain_controle') }}">
                                @error('prochain_controle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label required-field">Résultat du contrôle</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="resultat" id="resultat_conforme" value="conforme" {{ old('resultat') == 'conforme' ? 'checked' : '' }} required>
                                        <label class="form-check-label text-success fw-bold" for="resultat_conforme">
                                            <i class="fas fa-check-circle"></i> Conforme
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="resultat" id="resultat_non_conforme" value="non_conforme" {{ old('resultat') == 'non_conforme' ? 'checked' : '' }}>
                                        <label class="form-check-label text-danger fw-bold" for="resultat_non_conforme">
                                            <i class="fas fa-times-circle"></i> Non conforme
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="resultat" id="resultat_en_attente" value="en_attente" {{ old('resultat') == 'en_attente' ? 'checked' : '' }}>
                                        <label class="form-check-label text-warning fw-bold" for="resultat_en_attente">
                                            <i class="fas fa-clock"></i> En attente
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('resultat')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Commentaires</label>
                            <textarea class="form-control @error('commentaire') is-invalid @enderror" 
                                     id="commentaire" 
                                     name="commentaire" 
                                     rows="3" 
                                     placeholder="Détails sur le contrôle, observations...">{{ old('commentaire') }}</textarea>
                            @error('commentaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-4">
                            <a href="{{ route('controleur.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                <i class="fas fa-save me-1"></i>
                                <span class="btn-text">Enregistrer le Contrôle</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" id="spinner"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Gestion de la soumission du formulaire
document.getElementById('controleForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');
    const btnText = submitBtn.querySelector('.btn-text');
    
    // Afficher le spinner et désactiver le bouton
    spinner.classList.remove('d-none');
    btnText.textContent = 'Enregistrement...';
    submitBtn.disabled = true;
});

// Gestion de la date du prochain contrôle
const dateControle = document.getElementById('date_controle');
const prochainControle = document.getElementById('prochain_controle');

if (dateControle && prochainControle) {
    dateControle.addEventListener('change', function() {
        if (this.value && !prochainControle.value) {
            const date = new Date(this.value);
            date.setFullYear(date.getFullYear() + 1);
            prochainControle.value = date.toISOString().split('T')[0];
        }
    });
}
</script>
@endpush

@endsection
