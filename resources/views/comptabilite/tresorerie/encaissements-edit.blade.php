@extends('layouts.app')

@section('title', 'Modifier Encaissement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier Encaissement</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.encaissements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.encaissements.update', $encaissement->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="{{ $encaissement->reference }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_encaissement" class="form-label">Date d'encaissement</label>
                                <input type="date" class="form-control" id="date_encaissement" name="date_encaissement" value="{{ \Carbon\Carbon::parse($encaissement->date_encaissement)->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="libelle" class="form-label">Libellé</label>
                                <input type="text" class="form-control" id="libelle" name="libelle" value="{{ $encaissement->libelle }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">Montant (FCFA)</label>
                                <input type="number" class="form-control" id="montant" name="montant" value="{{ $encaissement->montant }}" required min="0" step="100">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="caisse" class="form-label">Caisse</label>
                                <select class="form-select" id="caisse" name="caisse" required>
                                    <option value="Caisse Principale" {{ $encaissement->caisse == 'Caisse Principale' ? 'selected' : '' }}>Caisse Principale</option>
                                    <option value="Caisse Secondaire" {{ $encaissement->caisse == 'Caisse Secondaire' ? 'selected' : '' }}>Caisse Secondaire</option>
                                    <option value="Caisse Mobile" {{ $encaissement->caisse == 'Caisse Mobile' ? 'selected' : '' }}>Caisse Mobile</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="type_encaissement" class="form-label">Type d'encaissement</label>
                                <select class="form-select" id="type_encaissement" name="type_encaissement" required>
                                    <option value="Vente" {{ $encaissement->type_encaissement == 'Vente' ? 'selected' : '' }}>Vente</option>
                                    <option value="Service" {{ $encaissement->type_encaissement == 'Service' ? 'selected' : '' }}>Service</option>
                                    <option value="Pret" {{ $encaissement->type_encaissement == 'Pret' ? 'selected' : '' }}>Prêt</option>
                                    <option value="Remboursement" {{ $encaissement->type_encaissement == 'Remboursement' ? 'selected' : '' }}>Remboursement</option>
                                    <option value="Autre" {{ $encaissement->type_encaissement == 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client" class="form-label">Client</label>
                                <input type="text" class="form-control" id="client" name="client" value="{{ $encaissement->client }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="validé" {{ $encaissement->statut == 'validé' ? 'selected' : '' }}>Validé</option>
                                    <option value="en attente" {{ $encaissement->statut == 'en attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="annulé" {{ $encaissement->statut == 'annulé' ? 'selected' : '' }}>Annulé</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mode_paiement" class="form-label">Mode de paiement</label>
                                <select class="form-select" id="mode_paiement" name="mode_paiement" required>
                                    <option value="Espèces" {{ $encaissement->mode_paiement == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                                    <option value="Carte bancaire" {{ $encaissement->mode_paiement == 'Carte bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                                    <option value="Virement" {{ $encaissement->mode_paiement == 'Virement' ? 'selected' : '' }}>Virement</option>
                                    <option value="Chèque" {{ $encaissement->mode_paiement == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                    <option value="Mobile Money" {{ $encaissement->mode_paiement == 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reference_paiement" class="form-label">Référence paiement</label>
                                <input type="text" class="form-control" id="reference_paiement" name="reference_paiement" value="{{ $encaissement->reference_paiement ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" name="responsable" value="{{ $encaissement->responsable }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_validation" class="form-label">Date de validation</label>
                                <input type="date" class="form-control" id="date_validation" name="date_validation" value="{{ $encaissement->date_validation ? \Carbon\Carbon::parse($encaissement->date_validation)->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ $encaissement->notes ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
                                </button>
                                <a href="{{ route('tresorerie.encaissements') }}" class="btn btn-outline-secondary">
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
