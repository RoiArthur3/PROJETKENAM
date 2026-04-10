@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nouvelle fiche de maintenance</h5>
                    <a href="{{ route('materiel.maintenance.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('materiel.maintenance.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="vehicule_id" class="form-label">Véhicule *</label>
                                <select class="form-select @error('vehicule_id') is-invalid @enderror" id="vehicule_id" name="vehicule_id" required>
                                    <option value="">Sélectionner un véhicule</option>
                                    @foreach($vehicules as $vehicule)
                                        <option value="{{ $vehicule->id }}" {{ old('vehicule_id') == $vehicule->id ? 'selected' : '' }}>
                                            {{ $vehicule->marque }} {{ $vehicule->modele }} ({{ $vehicule->immatriculation }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicule_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="type" class="form-label">Type de maintenance *</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Sélectionner un type</option>
                                    @foreach($typesMaintenance as $key => $label)
                                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="date" class="form-label">Date *</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="kilometrage" class="form-label">Kilométrage *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('kilometrage') is-invalid @enderror" id="kilometrage" name="kilometrage" value="{{ old('kilometrage') }}" required>
                                    <span class="input-group-text">km</span>
                                    @error('kilometrage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="statut" class="form-label">Statut *</label>
                                <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                    @foreach($statuts as $key => $label)
                                        <option value="{{ $key }}" {{ old('statut') == $key ? 'selected' : ($key == 'planifiée' ? 'selected' : '') }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="technicien_id" class="form-label">Technicien *</label>
                                <select class="form-select @error('technicien_id') is-invalid @enderror" id="technicien_id" name="technicien_id" required>
                                    <option value="">Sélectionner un technicien</option>
                                    @foreach($techniciens as $technicien)
                                        <option value="{{ $technicien->id }}" {{ old('technicien_id') == $technicien->id ? 'selected' : '' }}>
                                            {{ $technicien->nom }} ({{ $technicien->specialite }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('technicien_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="cout" class="form-label">Coût (FCFA) *</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control @error('cout') is-invalid @enderror" id="cout" name="cout" value="{{ old('cout') }}" required>
                                    <span class="input-group-text">FCFA</span>
                                    @error('cout')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="duree" class="form-label">Durée</label>
                                <input type="text" class="form-control @error('duree') is-invalid @enderror" id="duree" name="duree" placeholder="Ex: 2h30" value="{{ old('duree') }}">
                                @error('duree')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description détaillée *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pieces_utilisees" class="form-label">Pièces utilisées</label>
                                <textarea class="form-control @error('pieces_utilisees') is-invalid @enderror" id="pieces_utilisees" name="pieces_utilisees" rows="2">{{ old('pieces_utilisees') }}</textarea>
                                @error('pieces_utilisees')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="prochaine_echeance" class="form-label">Prochaine échéance</label>
                                <input type="date" class="form-control @error('prochaine_echeance') is-invalid @enderror" id="prochaine_echeance" name="prochaine_echeance" value="{{ old('prochaine_echeance') }}">
                                @error('prochaine_echeance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes complémentaires</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-undo"></i> Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer la maintenance
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
    // Script pour la gestion dynamique du formulaire
    document.addEventListener('DOMContentLoaded', function() {
        // Mettre à jour la date minimale pour la prochaine échéance
        const dateInput = document.getElementById('date');
        const prochaineEcheanceInput = document.getElementById('prochaine_echeance');

        if (dateInput && prochaineEcheanceInput) {
            dateInput.addEventListener('change', function() {
                prochaineEcheanceInput.min = this.value;
                if (prochaineEcheanceInput.value && prochaineEcheanceInput.value < this.value) {
                    prochaineEcheanceInput.value = this.value;
                }
            });

            // Initialiser la date minimale au chargement
            prochaineEcheanceInput.min = dateInput.value;
        }
    });
</script>
@endpush
@endsection
