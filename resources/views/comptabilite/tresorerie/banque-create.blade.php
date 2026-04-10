@extends('layouts.app')

@section('title', 'Nouvelle Opération Bancaire - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nouvelle Opération Bancaire</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.banque') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.banque.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" required placeholder="BAN-2024-XXX">
                            </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_operation" class="form-label">Date d'opération</label>
                                <input type="date" class="form-control" id="date_operation" name="date_operation" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="libelle" class="form-label">Libellé</label>
                                <input type="text" class="form-control" id="libelle" name="libelle" required placeholder="Description de l'opération">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="montant" class="form-label">Montant (FCFA)</label>
                                <input type="number" class="form-control" id="montant" name="montant" required placeholder="0" min="0" step="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">Type d'opération</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Choisir un type</option>
                                    <option value="débit">Débit</option>
                                    <option value="crédit">Crédit</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
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
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
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
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Notes additionnelles"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Enregistrer l'opération
                                </button>
                                <a href="{{ route('tresorerie.banque') }}" class="btn btn-outline-secondary">
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
