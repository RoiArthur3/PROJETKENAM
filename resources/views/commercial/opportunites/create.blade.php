@extends('layouts.app')

@section('title', 'Nouvelle Opportunité - KENAM SERVICES')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-bullseye me-2 text-success"></i>Nouvelle Opportunité
      </h1>
      <p class="text-muted mb-0">Créer une opportunité qui alimentera le dashboard commercial</p>
    </div>
    <a href="{{ route('commercial.opportunites.index') }}" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-2"></i>Retour aux opportunités
    </a>
  </div>

  <form method="POST" action="{{ route('commercial.opportunites.store') }}" class="row">
    @csrf
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
          <h6 class="m-0 fw-bold text-success">
            <i class="fas fa-info-circle me-2"></i>Informations
          </h6>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="titre" class="form-label fw-bold">Nom de l'opportunité <span class="text-danger">*</span></label>
              <input type="text" id="titre" name="titre" class="form-control @error('titre') is-invalid @enderror" value="{{ old('titre') }}" placeholder="Ex: Transport SOLIBRA" required>
              @error('titre')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="client_id" class="form-label fw-bold">Client/Prospect <span class="text-danger">*</span></label>
              <select id="client_id" name="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                <option value="">Sélectionner un client</option>
                @foreach($clients ?? [] as $client)
                  <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->nom }}</option>
                @endforeach
              </select>
              @error('client_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label for="montant" class="form-label fw-bold">Valeur estimée (FCFA) <span class="text-danger">*</span></label>
              <input type="number" id="montant" name="montant" class="form-control @error('montant') is-invalid @enderror" value="{{ old('montant') }}" min="0" step="1" required>
              @error('montant')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label for="statut" class="form-label fw-bold">Statut <span class="text-danger">*</span></label>
              <select id="statut" name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                <option value="prospection" {{ old('statut') == 'prospection' ? 'selected' : '' }}>Prospection</option>
                <option value="qualification" {{ old('statut') == 'qualification' ? 'selected' : '' }}>Qualification</option>
                <option value="proposition" {{ old('statut') == 'proposition' ? 'selected' : '' }}>Proposition</option>
                <option value="negociation" {{ old('statut') == 'negociation' ? 'selected' : '' }}>Négociation</option>
                <option value="gagne" {{ old('statut') == 'gagne' ? 'selected' : '' }}>Gagné</option>
                <option value="perdu" {{ old('statut') == 'perdu' ? 'selected' : '' }}>Perdu</option>
              </select>
              @error('statut')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label for="probabilite" class="form-label fw-bold">Probabilité (%)</label>
              <select id="probabilite" name="probabilite" class="form-select @error('probabilite') is-invalid @enderror">
                <option value="10" {{ old('probabilite') == '10' ? 'selected' : '' }}>10% - Prospection</option>
                <option value="25" {{ old('probabilite') == '25' ? 'selected' : '' }}>25% - Qualification</option>
                <option value="50" {{ old('probabilite') == '50' ? 'selected' : '' }}>50% - Proposition</option>
                <option value="75" {{ old('probabilite') == '75' ? 'selected' : '' }}>75% - Négociation</option>
                <option value="90" {{ old('probabilite') == '90' ? 'selected' : '' }}>90% - Verbal</option>
              </select>
              @error('probabilite')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label for="date_echeance" class="form-label fw-bold">Date de clôture prévue</label>
              <input type="date" id="date_echeance" name="date_echeance" class="form-control @error('date_echeance') is-invalid @enderror" value="{{ old('date_echeance') }}" required>
              @error('date_echeance')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12">
              <label class="form-label fw-bold">Description</label>
              <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Détails de l'opportunité...">{{ old('description') }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
          <h6 class="m-0 fw-bold text-success">
            <i class="fas fa-cogs me-2"></i>Actions
          </h6>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save me-2"></i>Enregistrer l'opportunité
            </button>
            <a href="{{ route('commercial.opportunites.index') }}" class="btn btn-outline-secondary">
              <i class="fas fa-times me-2"></i>Annuler
            </a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
