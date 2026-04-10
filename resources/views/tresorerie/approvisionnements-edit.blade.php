@extends('layouts.app')

@section('title', 'Modifier Approvisionnement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier Approvisionnement</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.approvisionnements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.approvisionnements.update', $approvisionnement->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="{{ $approvisionnement->reference }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_approvisionnement" class="form-label">Date d'approvisionnement</label>
                                <input type="date" class="form-control" id="date_approvisionnement" name="date_approvisionnement" value="{{ \Carbon\Carbon::parse($approvisionnement->date_approvisionnement)->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="libelle" class="form-label">Libellé</label>
                                <input type="text" class="form-control" id="libelle" name="libelle" value="{{ $approvisionnement->libelle }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">Montant (FCFA)</label>
                                <input type="number" class="form-control" id="montant" name="montant" value="{{ $approvisionnement->montant }}" required min="0" step="100">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="caisse" class="form-label">Caisse</label>
                                <select class="form-select" id="caisse" name="caisse" required>
                                    <option value="Caisse Principale" {{ $approvisionnement->caisse == 'Caisse Principale' ? 'selected' : '' }}>Caisse Principale</option>
                                    <option value="Caisse Secondaire" {{ $approvisionnement->caisse == 'Caisse Secondaire' ? 'selected' : '' }}>Caisse Secondaire</option>
                                    <option value="Caisse Mobile" {{ $approvisionnement->caisse == 'Caisse Mobile' ? 'selected' : '' }}>Caisse Mobile</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="validé" {{ $approvisionnement->statut == 'validé' ? 'selected' : '' }}>Validé</option>
                                    <option value="en attente" {{ $approvisionnement->statut == 'en attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="annulé" {{ $approvisionnement->statut == 'annulé' ? 'selected' : '' }}>Annulé</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="source" class="form-label">Source</label>
                                <select class="form-select" id="source" name="source" required>
                                    <option value="Banque" {{ $approvisionnement->source == 'Banque' ? 'selected' : '' }}>Banque</option>
                                    <option value="Autre caisse" {{ $approvisionnement->source == 'Autre caisse' ? 'selected' : '' }}>Autre caisse</option>
                                    <option value="Client" {{ $approvisionnement->source == 'Client' ? 'selected' : '' }}>Client</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reference_source" class="form-label">Référence source</label>
                                <input type="text" class="form-control" id="reference_source" name="reference_source" value="{{ $approvisionnement->reference_source ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" name="responsable" value="{{ $approvisionnement->responsable }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_validation" class="form-label">Date de validation</label>
                                <input type="date" class="form-control" id="date_validation" name="date_validation" value="{{ $approvisionnement->date_validation ? \Carbon\Carbon::parse($approvisionnement->date_validation)->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ $approvisionnement->notes ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
                                </button>
                                <a href="{{ route('tresorerie.approvisionnements') }}" class="btn btn-outline-secondary">
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
