@extends('layouts.app')
@section('title', 'Nouveau Document Légal - KENAM SERVICES')
@section('content')
<div class="container-fluid">
  <div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>Nouveau document légal</h5>
      <a href="{{ route('parc.documents-legaux.index') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left me-1"></i>Retour</a>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('parc.documents-legaux.store') }}" class="row g-3" enctype="multipart/form-data">
        @csrf
        <div class="col-md-6">
          <label class="form-label small">Véhicule *</label>
          <select name="vehicule_id" class="form-select @error('vehicule_id') is-invalid @enderror" required>
            <option value="">Sélectionnez un véhicule</option>
            @foreach(($vehicles ?? []) as $v)
              <option value="{{ $v->id }}" {{ old('vehicule_id') == $v->id ? 'selected' : '' }}>
                {{ $v->immatriculation }} @if(!empty($v->modele)) — {{ $v->modele }} @endif
              </option>
            @endforeach
          </select>
          @error('vehicule_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Type document *</label>
          <select name="type" class="form-select @error('type') is-invalid @enderror" required>
            <option>Assurance</option>
            <option>Carte grise</option>
            <option>Visite technique</option>
            <option>Vignette</option>
          </select>
          @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Date émission *</label>
          <input type="date" name="date_emission" class="form-control @error('date_emission') is-invalid @enderror" required value="{{ old('date_emission') }}">
          @error('date_emission')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Date expiration *</label>
          <input type="date" name="date_expiration" class="form-control @error('date_expiration') is-invalid @enderror" required value="{{ old('date_expiration') }}">
          @error('date_expiration')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Numéro document</label>
          <input type="text" name="numero" class="form-control @error('numero') is-invalid @enderror" value="{{ old('numero') }}">
          @error('numero')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Organisme émetteur</label>
          <input type="text" name="organisme" class="form-control @error('organisme') is-invalid @enderror" value="{{ old('organisme') }}">
          @error('organisme')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-12">
          <label class="form-label small">Fichier</label>
          <input type="file" name="fichier" class="form-control @error('fichier') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
          @error('fichier')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
          <a href="{{ route('parc.documents-legaux.index') }}" class="btn btn-light">Annuler</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
