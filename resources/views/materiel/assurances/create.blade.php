@extends('layouts.app')

@section('title', 'Ajouter Assurance | KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">
                    <i class="fas fa-shield-alt text-primary me-2"></i>
                    Ajouter une Assurance
                </h1>
                <a href="{{ route('materiel.assurances.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Détails de l'Assurance</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('materiel.assurances.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="numero_police" class="form-label">N° de Police <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('numero_police') is-invalid @enderror" 
                                    id="numero_police" name="numero_police" value="{{ old('numero_police') }}" required>
                                @error('numero_police')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="assureur" class="form-label">Assureur <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('assureur') is-invalid @enderror" 
                                    id="assureur" name="assureur" value="{{ old('assureur') }}" required>
                                @error('assureur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="vehicule_id" class="form-label">Engin (Optionnel)</label>
                                <select class="form-select @error('vehicule_id') is-invalid @enderror" 
                                    id="vehicule_id" name="vehicule_id">
                                    <option value="">-- Sélectionner un engin --</option>
                                    @foreach($vehicules as $vehicule)
                                        <option value="{{ $vehicule->id }}" 
                                            {{ old('vehicule_id') == $vehicule->id ? 'selected' : '' }}>
                                            {{ $vehicule->immatriculation }} - {{ $vehicule->marque }} {{ $vehicule->modele }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicule_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="prime_annuelle" class="form-label">Prime Annuelle (DZD)</label>
                                <input type="number" step="0.01" class="form-control @error('prime_annuelle') is-invalid @enderror" 
                                    id="prime_annuelle" name="prime_annuelle" value="{{ old('prime_annuelle') }}">
                                @error('prime_annuelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="date_debut" class="form-label">Date de Début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_debut') is-invalid @enderror" 
                                    id="date_debut" name="date_debut" value="{{ old('date_debut') }}" required>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="date_fin" class="form-label">Date d'Expiration <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_fin') is-invalid @enderror" 
                                    id="date_fin" name="date_fin" value="{{ old('date_fin') }}" required>
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">Jours restants: <span id="days_remaining">-</span></small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('materiel.assurances.index') }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Ajouter l'Assurance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations</h5>
                </div>
                <div class="card-body">
                    <p><strong>À savoir:</strong></p>
                    <ul class="small">
                        <li>Le numéro de police doit être unique</li>
                        <li>La date d'expiration est importante pour le suivi</li>
                        <li>Un engin peut avoir plusieurs assurances</li>
                        <li>Les assurances expirées génèrent des alertes</li>
                    </ul>
                    <hr>
                    <p class="text-muted small mb-0">Les champs marqués avec <span class="text-danger">*</span> sont obligatoires.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateFinInput = document.getElementById('date_fin');
    
    function calculateDays() {
        if (dateFinInput.value) {
            const today = new Date();
            const endDate = new Date(dateFinInput.value);
            const diffTime = endDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            document.getElementById('days_remaining').textContent = diffDays;
        }
    }
    
    dateFinInput.addEventListener('change', calculateDays);
    calculateDays();
});
</script>
@endsection
