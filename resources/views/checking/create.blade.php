@extends('layouts.app')

@section('title', 'Nouvelle Vérification')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Nouvelle vérification</h1>
    <a href="{{ route('fleet.checking.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Retour</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route('fleet.checking.store') }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">Titre *</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Type</label>
            <input type="text" name="type" class="form-control" placeholder="vehicule, equipement..." value="vehicule">
          </div>
          <div class="col-12">
            <label class="form-label fw-bold">Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Checklist</label>
            <select name="checklist_id" class="form-select">
              <option value="">—</option>
              @foreach($checklists as $cl)
                <option value="{{ $cl->id }}">{{ $cl->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Inspecteur</label>
            <select name="inspector_id" class="form-select">
              <option value="">—</option>
              @foreach($inspectors as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Planifiée le</label>
            <input type="datetime-local" name="scheduled_at" class="form-control">
          </div>

          <div class="col-md-6">
            <label class="form-label fw-bold">Véhicule (immatriculation)</label>
            <input list="vehiculesList" name="vehicle_input" class="form-control" placeholder="Ex: CI-AB-1234-XX">
            <datalist id="vehiculesList">
              @foreach($vehicules as $v)
                <option value="{{ $v->immatriculation }}">{{ $v->marque }} {{ $v->modele }}</option>
              @endforeach
            </datalist>
          </div>
        </div>

        <div class="mt-4 d-flex gap-2">
          <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Enregistrer</button>
          <a href="{{ route('fleet.checking.index') }}" class="btn btn-outline-secondary">Annuler</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
