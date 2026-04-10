@extends('layouts.app')

@section('title', 'Nouveau Compte Bancaire - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nouveau Compte Bancaire</h1>
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
                    <form method="POST" action="{{ route('tresorerie.comptes-bancaires.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" required placeholder="CB-2024-XXX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom du compte</label>
                                <input type="text" class="form-control" id="nom" name="nom" required placeholder="Ex: Compte ECOBANK">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="banque" class="form-label">Banque</label>
                                <select class="form-select" id="banque" name="banque" required>
                                    <option value="">Choisir une banque</option>
                                    <option value="ECOBANK">ECOBANK</option>
                                    <option value="SGBCI">SGBCI</option>
                                    <option value="BIAO">BIAO</option>
                                    <option value="NSIA BANQUE">NSIA BANQUE</option>
                                    <option value="UBA">UBA</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="numero_compte" class="form-label">Numéro de compte</label>
                                <input type="text" class="form-control" id="numero_compte" name="numero_compte" required placeholder="Numéro de compte bancaire">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="solde" class="form-label">Solde (FCFA)</label>
                                <input type="number" class="form-control" id="solde" name="solde" required placeholder="0" min="0" step="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="devise" class="form-label">Devise</label>
                                <select class="form-select" id="devise" name="devise" required>
                                    <option value="XOF">XOF (FCFA)</option>
                                    <option value="EUR">EUR (Euro)</option>
                                    <option value="USD">USD (Dollar)</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="actif">Actif</option>
                                    <option value="inactif">Inactif</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" name="responsable" required placeholder="Nom du responsable">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_ouverture" class="form-label">Date d'ouverture</label>
                                <input type="date" class="form-control" id="date_ouverture" name="date_ouverture" required value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Notes additionnelles"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Créer le compte
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
