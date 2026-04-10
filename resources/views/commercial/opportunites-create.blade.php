@extends('layouts.app')

@section('title', 'Nouvelle Opportunité - KENAM SERVICES')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-bullseye me-2 text-primary"></i>Nouvelle Opportunité
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
          <h6 class="m-0 fw-bold text-primary">
            <i class="fas fa-info-circle me-2"></i>Informations
          </h6>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="titre" class="form-label fw-bold">Nom de l'opportunité *</label>
              <input type="text" id="titre" name="titre" class="form-control" placeholder="Ex: Transport SOLIBRA" required>
            </div>
            <div class="col-md-6">
              <label for="client_id" class="form-label fw-bold">Client/Prospect *</label>
              <select id="client_id" name="client_id" class="form-select" required>
                <option value="">Sélectionner un client</option>
                @foreach($clients as $client)
                  <option value="{{ $client->id }}">{{ $client->nom }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label for="montant" class="form-label fw-bold">Valeur estimée (FCFA) *</label>
              <input type="number" id="montant" name="montant" class="form-control" min="0" step="1" required>
            </div>
            <div class="col-md-4">
              <label for="statut" class="form-label fw-bold">Statut *</label>
              <select id="statut" name="statut" class="form-select" required>
                <option value="prospection">Prospection</option>
                <option value="qualification">Qualification</option>
                <option value="proposition">Proposition</option>
                <option value="negociation">Négociation</option>
                <option value="gagne">Gagné</option>
                <option value="perdu">Perdu</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="probabilite" class="form-label fw-bold">Probabilité (%)</label>
              <select id="probabilite" name="probabilite" class="form-select">
                <option value="10">10% - Prospection</option>
                <option value="25">25% - Qualification</option>
                <option value="50">50% - Proposition</option>
                <option value="75">75% - Négociation</option>
                <option value="90">90% - Verbal</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="date_echeance" class="form-label fw-bold">Date de clôture prévue</label>
              <input type="date" id="date_echeance" name="date_echeance" class="form-control" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-bold">Description</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Détails de l'opportunité..."></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
          <h6 class="m-0 fw-bold text-primary">
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
