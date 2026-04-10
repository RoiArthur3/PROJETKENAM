@extends('layouts.app')

@section('title', 'Modifier Paiement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier Paiement</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.paiements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.paiements.update', $paiement->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="{{ $paiement->reference }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_paiement" class="form-label">Date de paiement</label>
                                <input type="date" class="form-control" id="date_paiement" name="date_paiement" value="{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="beneficiaire" class="form-label">Bénéficiaire</label>
                                <input type="text" class="form-control" id="beneficiaire" name="beneficiaire" value="{{ $paiement->beneficiaire }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">Montant (FCFA)</label>
                                <input type="number" class="form-control" id="montant" name="montant" value="{{ $paiement->montant }}" required min="0" step="100">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">Type de paiement</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="Fournisseur" {{ $paiement->type == 'Fournisseur' ? 'selected' : '' }}>Fournisseur</option>
                                    <option value="Salaire" {{ $paiement->type == 'Salaire' ? 'selected' : '' }}>Salaire</option>
                                    <option value="Autre" {{ $paiement->type == 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="mode_paiement" class="form-label">Mode de paiement</label>
                                <select class="form-select" id="mode_paiement" name="mode_paiement" required>
                                    <option value="Virement" {{ $paiement->mode_paiement == 'Virement' ? 'selected' : '' }}>Virement</option>
                                    <option value="Espèces" {{ $paiement->mode_paiement == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                                    <option value="Chèque" {{ $paiement->mode_paiement == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                    <option value="Mobile Money" {{ $paiement->mode_paiement == 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="validé" {{ $paiement->statut == 'validé' ? 'selected' : '' }}>Validé</option>
                                    <option value="en attente" {{ $paiement->statut == 'en attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="annulé" {{ $paiement->statut == 'annulé' ? 'selected' : '' }}>Annulé</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="compte_source" class="form-label">Compte source</label>
                                <select class="form-select" id="compte_source" name="compte_source" required>
                                    <option value="Caisse Principale" {{ $paiement->compte_source == 'Caisse Principale' ? 'selected' : '' }}>Caisse Principale</option>
                                    <option value="Caisse Secondaire" {{ $paiement->compte_source == 'Caisse Secondaire' ? 'selected' : '' }}>Caisse Secondaire</option>
                                    <option value="ECOBANK" {{ $paiement->compte_source == 'ECOBANK' ? 'selected' : '' }}>ECOBANK</option>
                                    <option value="SGBCI" {{ $paiement->compte_source == 'SGBCI' ? 'selected' : '' }}>SGBCI</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reference_document" class="form-label">Référence document</label>
                                <input type="text" class="form-control" id="reference_document" name="reference_document" value="{{ $paiement->reference_document ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ $paiement->description ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
                                </button>
                                <a href="{{ route('tresorerie.paiements') }}" class="btn btn-outline-secondary">
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
