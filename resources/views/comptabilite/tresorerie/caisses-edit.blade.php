@extends('layouts.app')

@section('title', 'Modifier Caisse - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier Caisse</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.caisses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.caisses.update', $caisse->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="{{ $caisse->reference }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom de la caisse</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="{{ $caisse->nom }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2" required>{{ $caisse->description }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="solde_initial" class="form-label">Solde Initial (FCFA)</label>
                                <input type="number" class="form-control" id="solde_initial" name="solde_initial" value="{{ $caisse->solde_initial }}" required min="0" step="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="solde_actuel" class="form-label">Solde Actuel (FCFA)</label>
                                <input type="number" class="form-control" id="solde_actuel" name="solde_actuel" value="{{ $caisse->solde_actuel }}" required min="0" step="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="devise" class="form-label">Devise</label>
                                <select class="form-select" id="devise" name="devise" required>
                                    <option value="XOF" {{ $caisse->devise == 'XOF' ? 'selected' : '' }}>XOF (FCFA)</option>
                                    <option value="EUR" {{ $caisse->devise == 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                                    <option value="USD" {{ $caisse->devise == 'USD' ? 'selected' : '' }}>USD (Dollar)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="actif" {{ $caisse->statut == 'actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactif" {{ $caisse->statut == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" name="responsable" value="{{ $caisse->responsable }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ $caisse->notes ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
                                </button>
                                <a href="{{ route('tresorerie.caisses.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
