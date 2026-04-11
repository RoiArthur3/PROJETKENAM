@extends('layouts.app')

@section('title', 'Modifier le Véhicule | KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-edit me-2 text-warning"></i>Modifier le Véhicule
            </h1>
            <p class="text-muted mb-0">{{ $vehicule->immatriculation }} - {{ $vehicule->marque }} {{ $vehicule->modele }}</p>
        </div>
        <div>
            <a href="{{ route('materiel.vehicules.show', $vehicule) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-1"></i>Voir les détails
            </a>
            <a href="{{ route('materiel.vehicules') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2"></i>Modifier les informations du véhicule
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('materiel.vehicules.update', $vehicule) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Colonne de gauche -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="immatriculation" class="form-label">Immatriculation *</label>
                            <input type="text" class="form-control @error('immatriculation') is-invalid @enderror" 
                                   id="immatriculation" name="immatriculation" value="{{ old('immatriculation', $vehicule->immatriculation) }}" required>
                            @error('immatriculation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="marque" class="form-label">Marque *</label>
                            <input type="text" class="form-control @error('marque') is-invalid @enderror" 
                                   id="marque" name="marque" value="{{ old('marque', $vehicule->marque) }}" required>
                            @error('marque')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="modele" class="form-label">Modèle *</label>
                            <input type="text" class="form-control @error('modele') is-invalid @enderror" 
                                   id="modele" name="modele" value="{{ old('modele', $vehicule->modele) }}" required>
                            @error('modele')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                                <option value="">-- Choisir un type --</option>
                                <option value="camion" {{ old('type', $vehicule->type) == 'camion' ? 'selected' : '' }}>Camion</option>
                                <option value="voiture" {{ old('type', $vehicule->type) == 'voiture' ? 'selected' : '' }}>Voiture</option>
                                <option value="engin" {{ old('type', $vehicule->type) == 'engin' ? 'selected' : '' }}>Engin</option>
                                <option value="moto" {{ old('type', $vehicule->type) == 'moto' ? 'selected' : '' }}>Moto</option>
                                <option value="autre" {{ old('type', $vehicule->type) == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="couleur" class="form-label">Couleur</label>
                            <input type="text" class="form-control @error('couleur') is-invalid @enderror" 
                                   id="couleur" name="couleur" value="{{ old('couleur', $vehicule->couleur) }}">
                            @error('couleur')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="annee" class="form-label">Année</label>
                            <input type="number" class="form-control @error('annee') is-invalid @enderror" 
                                   id="annee" name="annee" value="{{ old('annee', $vehicule->annee) }}" 
                                   min="1900" max="{{ date('Y') + 1 }}">
                            @error('annee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Colonne de droite -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="statut" class="form-label">Statut *</label>
                            <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                <option value="">-- Choisir un statut --</option>
                                <option value="actif" {{ old('statut', $vehicule->statut) == 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="maintenance" {{ old('statut', $vehicule->statut) == 'maintenance' ? 'selected' : '' }}>En maintenance</option>
                                <option value="hors_service" {{ old('statut', $vehicule->statut) == 'hors_service' ? 'selected' : '' }}>Hors service</option>
                                <option value="vendu" {{ old('statut', $vehicule->statut) == 'vendu' ? 'selected' : '' }}>Vendu</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kilometrage" class="form-label">Kilométrage</label>
                            <input type="number" class="form-control @error('kilometrage') is-invalid @enderror" 
                                   id="kilometrage" name="kilometrage" value="{{ old('kilometrage', $vehicule->kilometrage) }}" 
                                   min="0" step="1">
                            @error('kilometrage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="prix_achat" class="form-label">Prix d'achat (FCFA)</label>
                            <input type="number" class="form-control @error('prix_achat') is-invalid @enderror" 
                                   id="prix_achat" name="prix_achat" value="{{ old('prix_achat', $vehicule->prix_achat) }}" 
                                   min="0" step="1">
                            @error('prix_achat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="prix_location" class="form-label">Prix location/jour (FCFA)</label>
                            <input type="number" class="form-control @error('prix_location') is-invalid @enderror" 
                                   id="prix_location" name="prix_location" value="{{ old('prix_location', $vehicule->prix_location) }}" 
                                   min="0" step="1">
                            @error('prix_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="date_achat" class="form-label">Date d'achat</label>
                            <input type="date" class="form-control @error('date_achat') is-invalid @enderror" 
                                   id="date_achat" name="date_achat" value="{{ old('date_achat', $vehicule->date_achat) }}">
                            @error('date_achat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="carte_grise" class="form-label">Carte grise</label>
                            <input type="text" class="form-control @error('carte_grise') is-invalid @enderror" 
                                   id="carte_grise" name="carte_grise" value="{{ old('carte_grise', $vehicule->carte_grise) }}">
                            @error('carte_grise')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description et observations -->
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $vehicule->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="observations" class="form-label">Observations</label>
                            <textarea class="form-control @error('observations') is-invalid @enderror" 
                                      id="observations" name="observations" rows="3">{{ old('observations', $vehicule->observations) }}</textarea>
                            @error('observations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('materiel.vehicules') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
