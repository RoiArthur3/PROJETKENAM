@extends('layouts.app')

@section('title', 'Modifier Compte Bancaire - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier Compte Bancaire</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.comptes-bancaires') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.comptes-bancaires.update', $compte->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="{{ $compte->reference }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom du compte</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="{{ $compte->nom }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="banque" class="form-label">Banque</label>
                                <select class="form-select" id="banque" name="banque" required>
                                    <option value="ECOBANK" {{ $compte->banque == 'ECOBANK' ? 'selected' : '' }}>ECOBANK</option>
                                    <option value="SGBCI" {{ $compte->banque == 'SGBCI' ? 'selected' : '' }}>SGBCI</option>
                                    <option value="BIAO" {{ $compte->banque == 'BIAO' ? 'selected' : '' }}>BIAO</option>
                                    <option value="NSIA BANQUE" {{ $compte->banque == 'NSIA BANQUE' ? 'selected' : '' }}>NSIA BANQUE</option>
                                    <option value="UBA" {{ $compte->banque == 'UBA' ? 'selected' : '' }}>UBA</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="numero_compte" class="form-label">Numéro de compte</label>
                                <input type="text" class="form-control" id="numero_compte" name="numero_compte" value="{{ $compte->numero_compte }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="solde" class="form-label">Solde (FCFA)</label>
                                <input type="number" class="form-control" id="solde" name="solde" value="{{ $compte->solde }}" required min="0" step="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="devise" class="form-label">Devise</label>
                                <select class="form-select" id="devise" name="devise" required>
                                    <option value="XOF" {{ $compte->devise == 'XOF' ? 'selected' : '' }}>XOF (FCFA)</option>
                                    <option value="EUR" {{ $compte->devise == 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                                    <option value="USD" {{ $compte->devise == 'USD' ? 'selected' : '' }}>USD (Dollar)</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="actif" {{ $compte->statut == 'actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactif" {{ $compte->statut == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" name="responsable" value="{{ $compte->responsable }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_ouverture" class="form-label">Date d'ouverture</label>
                                <input type="date" class="form-control" id="date_ouverture" name="date_ouverture" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ $compte->notes ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
                                </button>
                                <a href="{{ route('tresorerie.comptes-bancaires') }}" class="btn btn-outline-secondary">
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
