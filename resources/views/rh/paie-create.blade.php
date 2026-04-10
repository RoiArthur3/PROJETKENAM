@extends('layouts.app')

@section('title', 'RH - Nouveau Bulletin de Paie | KENAM SERVICES')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col">
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-file-invoice-dollar mr-2 text-primary"></i>Nouveau Bulletin de Paie
      </h1>
      <p class="text-muted">Création manuelle d'un bulletin pour la période sélectionnée</p>
    </div>
    <div class="col-auto">
      <a href="{{ route('rh.paie.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left mr-1"></i>Retour à la paie
      </a>
    </div>
  </div>

  <form method="POST" action="{{ route('rh.paie.store') }}" class="row">
    @csrf
    <div class="col-lg-8">
      <div class="card shadow">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-user mr-2"></i>Informations Bulletin
          </h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Agent *</label>
              <select name="agent_id" class="form-select" required>
                <option value="">-- Sélectionner --</option>
                @foreach(($users ?? collect()) as $u)
                  <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Mois *</label>
              <input type="text" class="form-control" name="mois" value="{{ optional($period)->format('m') }}" placeholder="mm" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Année *</label>
              <input type="number" class="form-control" name="annee" value="{{ optional($period)->format('Y') }}" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Salaire de base (FCFA) *</label>
              <input type="number" class="form-control" name="salaire_base" step="0.01" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Heures sup (FCFA)</label>
              <input type="number" class="form-control" name="heures_sup" step="0.01">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Primes (FCFA)</label>
              <input type="number" class="form-control" name="primes" step="0.01">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Commentaires</label>
            <textarea name="commentaires" class="form-control" rows="3"></textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-cogs mr-2"></i>Actions
          </h6>
        </div>
        <div class="card-body">
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
              <i class="fas fa-save mr-1"></i>Enregistrer le bulletin
            </button>
            <a href="{{ route('rh.paie.index') }}" class="btn btn-outline-secondary">
              <i class="fas fa-times mr-1"></i>Annuler
            </a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
