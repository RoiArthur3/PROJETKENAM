@extends('layouts.app')

@section('title', 'Nouvel Entrepôt')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fas fa-warehouse me-2"></i>Nouvel entrepôt</h5>
      <a href="{{ route('stock.warehouses.index') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left me-1"></i>Retour</a>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('stock.warehouses.store') }}" class="row g-3">
        @csrf
        <div class="col-md-6">
          <label class="form-label small">Nom de l'entrepôt <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Localisation</label>
          <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}">
          @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Capacité (unités)</label>
          <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity') }}" min="0">
          @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
          <label class="form-label small">Description</label>
          <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
          @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
          <a href="{{ route('stock.warehouses.index') }}" class="btn btn-light">Annuler</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
