@extends('layouts.app')
@section('title', 'Nouvel Entretien - KENAM SERVICES')
@section('content')
<div class="container-fluid">
  <div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Nouvel entretien</h5>
      <a href="{{ route('parc.entretiens') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left me-1"></i>Retour</a>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('parc.entretiens.store') }}" class="row g-3">
        @csrf

        <div class="col-md-6">
          <label class="form-label small">Véhicule <span class="text-danger">*</span></label>
          <select name="vehicule_id" class="form-select @error('vehicule_id') is-invalid @enderror" required>
            <option value="">Sélectionnez un véhicule</option>
            @foreach(($vehicules ?? []) as $v)
              <option value="{{ $v->id }}" {{ old('vehicule_id') == $v->id ? 'selected' : '' }}>
                {{ $v->immatriculation }}
              </option>
            @endforeach
          </select>
          @error('vehicule_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label small">Date <span class="text-danger">*</span></label>
          <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
          @error('date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label small">Type <span class="text-danger">*</span></label>
          <select name="type" class="form-select @error('type') is-invalid @enderror" required>
            @php($types = ['Vidange','Révision','Contrôle technique','Changement pneus','Autre'])
            @foreach($types as $t)
              <option value="{{ $t }}" {{ old('type', 'Vidange') === $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
          </select>
          @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label small">Kilométrage</label>
          <input type="number" name="kilometrage" class="form-control @error('kilometrage') is-invalid @enderror" min="0" value="{{ old('kilometrage') }}">
          @error('kilometrage')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label small">Prestataire</label>
          <input type="text" name="prestataire" class="form-control @error('prestataire') is-invalid @enderror" value="{{ old('prestataire') }}">
          @error('prestataire')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label small">Coût (FCFA)</label>
          <input type="number" name="cout" class="form-control @error('cout') is-invalid @enderror" min="0" step="0.01" value="{{ old('cout') }}">
          @error('cout')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12">
          <label class="form-label small">Description</label>
          <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
          <a href="{{ route('parc.entretiens') }}" class="btn btn-light">Annuler</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
