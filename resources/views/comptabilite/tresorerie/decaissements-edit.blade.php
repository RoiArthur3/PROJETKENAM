@extends('layouts.app')

@section('title', 'Modifier Décaissement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier Décaissement</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.decaissements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.decaissements.update', $decaissement->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="{{ $decaissement->reference }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_decaissement" class="form-label">Date de décaissement</label>
                                <input type="date" class="form-control" id="date_decaissement" name="date_decaissement" value="{{ \Carbon\Carbon::parse($decaissement->date_decaissement)->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="libelle" class="form-label">Libellé</label>
                                <input type="text" class="form-control" id="libelle" name="libelle" value="{{ $decaissement->libelle }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">Montant (FCFA)</label>
                                <input type="number" class="form-control" id="montant" name="montant" value="{{ $decaissement->montant }}" required min="0" step="100">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="caisse" class="form-label">Caisse</label>
                                <select class="form-select" id="caisse" name="caisse" required>
                                    <option value="Caisse Principale" {{ $decaissement->caisse == 'Caisse Principale' ? 'selected' : '' }}>Caisse Principale</option>
                                    <option value="Caisse Secondaire" {{ $decaissement->caisse == 'Caisse Secondaire' ? 'selected' : '' }}>Caisse Secondaire</option>
                                    <option value="Caisse Mobile" {{ $decaissement->caisse == 'Caisse Mobile' ? 'selected' : '' }}>Caisse Mobile</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="type_decaissement" class="form-label">Type de décaissement</label>
                                <select class="form-select" id="type_decaissement" name="type_decaissement" required>
                                    <option value="Dépense" {{ $decaissement->type_decaissement == 'Dépense' ? 'selected' : '' }}>Dépense</option>
                                    <option value="Virement" {{ $decaissement->type_decaissement == 'Virement' ? 'selected' : '' }}>Virement</option>
                                    <option value="Retrait" {{ $decaissement->type_decaissement == 'Retrait' ? 'selected' : '' }}>Retrait</option>
                                    <option value="Remboursement" {{ $decaissement->type_decaissement == 'Remboursement' ? 'selected' : '' }}>Remboursement</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="beneficiaire" class="form-label">Bénéficiaire</label>
                                <input type="text" class="form-control" id="beneficiaire" name="beneficiaire" value="{{ $decaissement->beneficiaire }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="validé" {{ $decaissement->statut == 'validé' ? 'selected' : '' }}>Validé</option>
                                    <option value="en attente" {{ $decaissement->statut == 'en attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="annulé" {{ $decaissement->statut == 'annulé' ? 'selected' : '' }}>Annulé</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mode_paiement" class="form-label">Mode de paiement</label>
                                <select class="form-select" id="mode_paiement" name="mode_paiement" required>
                                    <option value="Espèces" {{ $decaissement->mode_paiement == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                                    <option value="Carte bancaire" {{ $decaissement->mode_paiement == 'Carte bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                                    <option value="Virement" {{ $decaissement->mode_paiement == 'Virement' ? 'selected' : '' }}>Virement</option>
                                    <option value="Chèque" {{ $decaissement->mode_paiement == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reference_paiement" class="form-label">Référence paiement</label>
                                <input type="text" class="form-control" id="reference_paiement" name="reference_paiement" value="{{ $decaissement->reference_paiement ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" name="responsable" value="{{ $decaissement->responsable }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_validation" class="form-label">Date de validation</label>
                                <input type="date" class="form-control" id="date_validation" name="date_validation" value="{{ $decaissement->date_validation ? \Carbon\Carbon::parse($decaissement->date_validation)->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ $decaissement->notes ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
                                </button>
                                <a href="{{ route('tresorerie.decaissements') }}" class="btn btn-outline-secondary">
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
