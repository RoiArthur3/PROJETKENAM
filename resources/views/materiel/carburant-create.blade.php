@extends('layouts.app')

@section('title', 'Nouveau Plein - KENAM SERVICES')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nouveau Plein de Carburant</h5>
                    <a href="{{ route('materiel.carburant.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('materiel.carburant.store') }}">
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
                                <label for="chauffeur_id" class="form-label">Chauffeur *</label>
                                <select class="form-select @error('chauffeur_id') is-invalid @enderror" id="chauffeur_id" name="chauffeur_id" required>
                                    <option value="">Sélectionner un chauffeur</option>
                                    @foreach($chauffeurs as $chauffeur)
                                        <option value="{{ $chauffeur->id }}" {{ old('chauffeur_id') == $chauffeur->id ? 'selected' : '' }}>
                                            {{ $chauffeur->nom }} ({{ $chauffeur->contact }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('chauffeur_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="date" class="form-label">Date du plein *</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="type_carburant" class="form-label">Type de carburant *</label>
                                <select class="form-select @error('type_carburant') is-invalid @enderror" id="type_carburant" name="type_carburant" required>
                                    <option value="">Sélectionner un type</option>
                                    @foreach($typesCarburant as $key => $value)
                                        <option value="{{ $key }}" {{ old('type_carburant') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type_carburant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="station" class="form-label">Station-service *</label>
                                <select class="form-select @error('station') is-invalid @enderror" id="station" name="station" required>
                                    <option value="">Sélectionner une station</option>
                                    @foreach($stations as $key => $value)
                                        <option value="{{ $key }}" {{ old('station') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('station')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="kilometrage_avant" class="form-label">Kilométrage avant *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('kilometrage_avant') is-invalid @enderror" id="kilometrage_avant" name="kilometrage_avant" value="{{ old('kilometrage_avant') }}" required min="0">
                                    <span class="input-group-text">km</span>
                                    @error('kilometrage_avant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="kilometrage_apres" class="form-label">Kilométrage après *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('kilometrage_apres') is-invalid @enderror" id="kilometrage_apres" name="kilometrage_apres" value="{{ old('kilometrage_apres') }}" required min="0">
                                    <span class="input-group-text">km</span>
                                    @error('kilometrage_apres')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="quantite" class="form-label">Quantité (litres) *</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control @error('quantite') is-invalid @enderror" id="quantite" name="quantite" value="{{ old('quantite') }}" required min="0.1">
                                    <span class="input-group-text">L</span>
                                    @error('quantite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="prix_unitaire" class="form-label">Prix unitaire *</label>
                                <div class="input-group">
                                    <input type="number" step="1" class="form-control @error('prix_unitaire') is-invalid @enderror" id="prix_unitaire" name="prix_unitaire" value="{{ old('prix_unitaire', 800) }}" required min="0">
                                    <span class="input-group-text">FCFA/L</span>
                                    @error('prix_unitaire')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="montant_total" class="form-label">Montant total</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="montant_total" readonly>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="consommation" class="form-label">Consommation</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="consommation" readonly>
                                    <span class="input-group-text">L/100km</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="bon_de_commande" class="form-label">N° Bon de commande</label>
                                <input type="text" class="form-control @error('bon_de_commande') is-invalid @enderror" id="bon_de_commande" name="bon_de_commande" value="{{ old('bon_de_commande') }}">
                                @error('bon_de_commande')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="facture" class="form-label">N° Facture</label>
                                <input type="text" class="form-control @error('facture') is-invalid @enderror" id="facture" name="facture" value="{{ old('facture') }}">
                                @error('facture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Commentaire</label>
                            <textarea class="form-control @error('commentaire') is-invalid @enderror" id="commentaire" name="commentaire" rows="2">{{ old('commentaire') }}</textarea>
                            @error('commentaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-undo"></i> Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer le plein
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
    // Calcul automatique du montant total et de la consommation
    document.addEventListener('DOMContentLoaded', function() {
        const quantiteInput = document.getElementById('quantite');
        const prixUnitaireInput = document.getElementById('prix_unitaire');
        const montantTotalInput = document.getElementById('montant_total');
        const kilometrageAvantInput = document.getElementById('kilometrage_avant');
        const kilometrageApresInput = document.getElementById('kilometrage_apres');
        const consommationInput = document.getElementById('consommation');

        function calculerMontantTotal() {
            const quantite = parseFloat(quantiteInput.value) || 0;
            const prixUnitaire = parseFloat(prixUnitaireInput.value) || 0;
            const montantTotal = quantite * prixUnitaire;
            montantTotalInput.value = montantTotal.toFixed(0);
        }

        function calculerConsommation() {
            const quantite = parseFloat(quantiteInput.value) || 0;
            const kmAvant = parseFloat(kilometrageAvantInput.value) || 0;
            const kmApres = parseFloat(kilometrageApresInput.value) || 0;
            
            if (kmApres > kmAvant && kmAvant >= 0) {
                const distance = kmApres - kmAvant;
                const consommation = (quantite / distance) * 100;
                consommationInput.value = consommation.toFixed(2);
            } else {
                consommationInput.value = '';
            }
        }

        // Écouteurs d'événements
        quantiteInput.addEventListener('input', function() {
            calculerMontantTotal();
            calculerConsommation();
        });

        prixUnitaireInput.addEventListener('input', calculerMontantTotal);
        kilometrageAvantInput.addEventListener('input', calculerConsommation);
        kilometrageApresInput.addEventListener('input', calculerConsommation);

        // Calcul initial
        calculerMontantTotal();
        calculerConsommation();
    });
</script>
@endpush
@endsection
