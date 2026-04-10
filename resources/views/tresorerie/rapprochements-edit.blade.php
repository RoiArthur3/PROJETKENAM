@extends('layouts.app')

@section('title', 'Modifier Rapprochement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier Rapprochement Bancaire</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.rapprochements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.rapprochements.update', $rapprochement->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="{{ $rapprochement->reference }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_rapprochement" class="form-label">Date de rapprochement</label>
                                <input type="date" class="form-control" id="date_rapprochement" name="date_rapprochement" value="{{ \Carbon\Carbon::parse($rapprochement->date_rapprochement)->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="compte_bancaire" class="form-label">Compte bancaire</label>
                                <select class="form-select" id="compte_bancaire" name="compte_bancaire" required>
                                    <option value="BIAO" {{ $rapprochement->compte_bancaire == 'BIAO' ? 'selected' : '' }}>BIAO</option>
                                    <option value="ECOBANK" {{ $rapprochement->compte_bancaire == 'ECOBANK' ? 'selected' : '' }}>ECOBANK</option>
                                    <option value="NSIA" {{ $rapprochement->compte_bancaire == 'NSIA' ? 'selected' : '' }}>NSIA</option>
                                    <option value="SGBCI" {{ $rapprochement->compte_bancaire == 'SGBCI' ? 'selected' : '' }}>SGBCI</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="periode" class="form-label">Période</label>
                                <input type="month" class="form-control" id="periode" name="periode" value="{{ $rapprochement->periode }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="solde_initial" class="form-label">Solde initial (FCFA)</label>
                                <input type="number" class="form-control" id="solde_initial" name="solde_initial" value="{{ $rapprochement->solde_initial }}" required min="0" step="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="solde_final" class="form-label">Solde final (FCFA)</label>
                                <input type="number" class="form-control" id="solde_final" name="solde_final" value="{{ $rapprochement->solde_final }}" required min="0" step="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ecart" class="form-label">Écart (FCFA)</label>
                                <input type="number" class="form-control" id="ecart" name="ecart" value="{{ $rapprochement->ecart }}" required step="100">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="total_debits" class="form-label">Total débits (FCFA)</label>
                                <input type="number" class="form-control" id="total_debits" name="total_debits" value="{{ $rapprochement->total_debits }}" required min="0" step="100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="total_credits" class="form-label">Total crédits (FCFA)</label>
                                <input type="number" class="form-control" id="total_credits" name="total_credits" value="{{ $rapprochement->total_credits }}" required min="0" step="100">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="validé" {{ $rapprochement->statut == 'validé' ? 'selected' : '' }}>Validé</option>
                                    <option value="en attente" {{ $rapprochement->statut == 'en attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="en cours" {{ $rapprochement->statut == 'en cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="rejeté" {{ $rapprochement->statut == 'rejeté' ? 'selected' : '' }}>Rejeté</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" name="responsable" value="{{ $rapprochement->responsable }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_validation" class="form-label">Date de validation</label>
                                <input type="date" class="form-control" id="date_validation" name="date_validation" value="{{ $rapprochement->date_validation ? \Carbon\Carbon::parse($rapprochement->date_validation)->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validateur" class="form-label">Validateur</label>
                                <input type="text" class="form-control" id="validateur" name="validateur" value="{{ $rapprochement->validateur ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="observations" class="form-label">Observations</label>
                                <textarea class="form-control" id="observations" name="observations" rows="3">{{ $rapprochement->observations ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
                                </button>
                                <a href="{{ route('tresorerie.rapprochements') }}" class="btn btn-outline-secondary">
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
