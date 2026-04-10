@extends('layouts.app')

@section('title', 'Nouveau Paiement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nouveau Paiement</h1>
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
                    <form method="POST" action="{{ route('tresorerie.paiements.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" required placeholder="PAT-2024-XXX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_paiement" class="form-label">Date de paiement</label>
                                <input type="date" class="form-control" id="date_paiement" name="date_paiement" required value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="beneficiaire" class="form-label">Bénéficiaire</label>
                                <input type="text" class="form-control" id="beneficiaire" name="beneficiaire" required placeholder="Nom du bénéficiaire">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">Montant (FCFA)</label>
                                <input type="number" class="form-control" id="montant" name="montant" required placeholder="0" min="0" step="100">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">Type de paiement</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Choisir un type</option>
                                    <option value="Fournisseur">Fournisseur</option>
                                    <option value="Salaire">Salaire</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="mode_paiement" class="form-label">Mode de paiement</label>
                                <select class="form-select" id="mode_paiement" name="mode_paiement" required>
                                    <option value="">Choisir un mode</option>
                                    <option value="Virement">Virement</option>
                                    <option value="Espèces">Espèces</option>
                                    <option value="Chèque">Chèque</option>
                                    <option value="Mobile Money">Mobile Money</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="">Choisir un statut</option>
                                    <option value="validé">Validé</option>
                                    <option value="en attente">En attente</option>
                                    <option value="annulé">Annulé</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="compte_source" class="form-label">Compte source</label>
                                <select class="form-select" id="compte_source" name="compte_source" required>
                                    <option value="">Choisir un compte</option>
                                    <option value="Caisse Principale">Caisse Principale</option>
                                    <option value="Caisse Secondaire">Caisse Secondaire</option>
                                    <option value="ECOBANK">ECOBANK</option>
                                    <option value="SGBCI">SGBCI</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reference_document" class="form-label">Référence document</label>
                                <input type="text" class="form-control" id="reference_document" name="reference_document" placeholder="Numéro de facture ou document">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description détaillée du paiement"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Créer le paiement
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
