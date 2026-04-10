@extends('layouts.app')

@section('title', 'Nouvelle Police d\'Assurance')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fas fa-shield-halved me-2"></i>Nouvelle police d'assurance</h5>
      <a href="{{ route('parc.assurances.index') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left me-1"></i>Retour</a>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('parc.assurances.store') }}" class="row g-3">
        @csrf
        <div class="col-md-12">
          <label class="form-label small">Véhicule *</label>
          <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
            <option value="">Sélectionner un véhicule</option>
            @foreach(($vehicles ?? []) as $v)
              <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                {{ $v->immatriculation }} @if(!empty($v->modele)) — {{ $v->modele }} @endif
              </option>
            @endforeach
          </select>
          @error('vehicle_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Assureur *</label>
          <input type="text" name="assureur" value="{{ old('assureur') }}" class="form-control @error('assureur') is-invalid @enderror" placeholder="Compagnie" required>
           @error('assureur')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Numéro de police *</label>
          <input type="text" name="numero_police" value="{{ old('numero_police') }}" class="form-control @error('numero_police') is-invalid @enderror" placeholder="Référence" required>
           @error('numero_police')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Date de début *</label>
          <input type="date" name="date_debut" value="{{ old('date_debut') }}" class="form-control @error('date_debut') is-invalid @enderror" required>
           @error('date_debut')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Date d'échéance *</label>
          <input type="date" name="date_fin" value="{{ old('date_fin') }}" class="form-control @error('date_fin') is-invalid @enderror" required>
           @error('date_fin')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Prime annuelle (FCFA)</label>
          <input type="number" name="prime_annuelle" value="{{ old('prime_annuelle') }}" class="form-control @error('prime_annuelle') is-invalid @enderror" min="0" step="0.01" placeholder="0.00">
           @error('prime_annuelle')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Statut</label>
          <select name="statut" class="form-select @error('statut') is-invalid @enderror">
            <option value="active" {{ old('statut') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="a_renouveler" {{ old('statut') === 'a_renouveler' ? 'selected' : '' }}>À renouveler</option>
            <option value="expiree" {{ old('statut') === 'expiree' ? 'selected' : '' }}>Expirée</option>
          </select>
           @error('statut')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-12">
          <label class="form-label small">Notes</label>
          <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Informations complémentaires">{{ old('notes') }}</textarea>
           @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
          <a href="{{ route('parc.assurances.index') }}" class="btn btn-light">Annuler</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
