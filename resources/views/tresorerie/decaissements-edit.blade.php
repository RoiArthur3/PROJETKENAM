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
                            <div class="col-12 mb-3">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Référence:</strong> {{ $decaissement->reference }}
                                    <small class="text-muted">(générée automatiquement, non modifiable)</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="operation_id" class="form-label">Opération liée (optionnel)</label>
                                <select class="form-select" id="operation_id" name="operation_id">
                                    <option value="">-- Aucune opération liée --</option>
                                    @foreach(($operations ?? collect()) as $op)
                                        <option value="{{ $op->id }}" {{ (int) $decaissement->operation_id === (int) $op->id ? 'selected' : '' }}>
                                            [{{ $op->numero_ordre ?? '#OP-'.$op->id }}] {{ $op->titre }} - {{ number_format($op->montant ?? 0, 0, ',', ' ') }} FCFA
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Permet de rattacher ce décaissement à une opération validée.</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_decaissement" class="form-label">Date de décaissement</label>
                                <input type="date" class="form-control" id="date_decaissement" name="date_decaissement" value="{{ \Carbon\Carbon::parse($decaissement->date_depense)->format('Y-m-d') }}" required>
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
                                <label for="caisse_id" class="form-label">Caisse</label>
                                <select class="form-select" id="caisse_id" name="caisse_id" required>
                                    <option value="">Choisir une caisse</option>
                                    @foreach(($caisses ?? collect()) as $caisse)
                                        <option value="{{ $caisse->id }}" {{ (int) $decaissement->caisse_id === (int) $caisse->id ? 'selected' : '' }}>
                                            {{ $caisse->nom }} (Solde: {{ number_format($caisse->solde_actuel, 0, ',', ' ') }} FCFA)
                                        </option>
                                    @endforeach
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
