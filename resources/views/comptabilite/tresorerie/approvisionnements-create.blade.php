@extends('layouts.app')

@section('title', 'Nouvel Approvisionnement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nouvel Approvisionnement de Caisse</h1>
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
                    <form method="POST" action="{{ route('tresorerie.approvisionnements.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" required placeholder="APP-2024-XXX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_approvisionnement" class="form-label">Date d'approvisionnement</label>
                                <input type="date" class="form-control" id="date_approvisionnement" name="date_approvisionnement" required value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="libelle" class="form-label">Libellé</label>
                                <input type="text" class="form-control" id="libelle" name="libelle" required placeholder="Description de l'approvisionnement">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="montant" class="form-label">Montant (FCFA)</label>
                                <input type="number" class="form-control" id="montant" name="montant" required placeholder="0" min="0" step="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="caisse_id" class="form-label">Caisse</label>
                                <select class="form-select" id="caisse_id" name="caisse_id" required>
                                    <option value="">Choisir une caisse</option>
                                    <option value="1">Caisse Principale</option>
                                    <option value="2"> <option>
                                    <option value="3">Caisse Mobile</option>
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
                            <div class="col-md-4 mb-3">
                                <label for="source" class="form-label">Source</label>
                                <select class="form-select" id="source" name="source" required>
                                    <option value="">Choisir une source</option>
                                    <option value="Banque ECOBANK">Banque ECOBANK</option>
                                    <option value="Banque SGBCI">Banque SGBCI</option>
                                    <option value="Banque BIAO">Banque BIAO</option>
                                    <option value="Banque NSIA BANQUE">Banque NSIA BANQUE</option>
                                    <option value="Espèces">Espèces</option>
                                    <option value="Autres">Autres</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="reference_source" class="form-label">Référence Source</label>
                                <input type="text" class="form-control" id="reference_source" name="reference_source" placeholder="Numéro de virement ou ticket">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" name="responsable" required placeholder="Nom du responsable">
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
                                    <i class="fas fa-save me-2"></i>Enregistrer l'approvisionnement
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
