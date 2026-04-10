@extends('layouts.app')

@section('title', 'Nouvelle Dépense - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nouvelle Dépense</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.depenses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.depenses.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" required placeholder="DET-2024-XXX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_depense" class="form-label">Date de dépense</label>
                                <input type="date" class="form-control" id="date_depense" name="date_depense" required value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="libelle" class="form-label">Libellé</label>
                                <input type="text" class="form-control" id="libelle" name="libelle" required placeholder="Description de la dépense">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="montant" class="form-label">Montant (FCFA)</label>
                                <input type="number" class="form-control" id="montant" name="montant" required placeholder="0" min="0" step="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="caisse" class="form-label">Caisse</label>
                                <select class="form-select" id="caisse" name="caisse" required>
                                    <option value="">Choisir une caisse</option>
                                    <option value="Caisse Principale">Caisse Principale</option>
                                    <option value="Caisse Secondaire">Caisse Secondaire</option>
                                    <option value="Caisse Mobile">Caisse Mobile</option>
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
                                <label for="motif" class="form-label">Motif</label>
                                <select class="form-select" id="motif" name="motif" required>
                                    <option value="">Choisir un motif</option>
                                    <option value="Fournitures">Fournitures</option>
                                    <option value="Carburant">Carburant</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Communication">Communication</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Autres">Autres</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="beneficiaire" class="form-label">Bénéficiaire</label>
                                <input type="text" class="form-control" id="beneficiaire" name="beneficiaire" placeholder="Nom du bénéficiaire">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="justification" class="form-label">Justification</label>
                                <input type="text" class="form-control" id="justification" name="justification" placeholder="Numéro de facture ou ticket">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" name="responsable" placeholder="Nom du responsable">
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
                                    <i class="fas fa-save me-2"></i>Enregistrer la dépense
                                </button>
                                <a href="{{ route('tresorerie.depenses.index') }}" class="btn btn-outline-secondary">
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
