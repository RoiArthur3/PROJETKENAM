@extends('layouts.app')

@section('title', (($acompteTerminology['singular'] ?? 'Acompte')) . ' - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nouveau {{ $acompteTerminology['singular'] ?? 'Acompte' }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.avances') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.avances.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" required placeholder="AVA-2024-XXX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_avance" class="form-label">Date de l'acompte</label>
                                <input type="date" class="form-control" id="date_avance" name="date_avance" required value="{{ now()->format('Y-m-d') }}">
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
                            <div class="col-md-6 mb-3">
                                <label for="motif" class="form-label">Motif</label>
                                <select class="form-select" id="motif" name="motif" required>
                                    <option value="">Choisir un motif</option>
                                    <option value="Mission">Mission</option>
                                    <option value="Frais terrain">Frais terrain</option>
                                    <option value="Urgence">Urgence</option>
                                    <option value="Déplacement">Déplacement</option>
                                    <option value="Autres">Autres</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="caisse" class="form-label">Caisse</label>
                                <select class="form-select" id="caisse" name="caisse" required>
                                    <option value="">Choisir une caisse</option>
                                    <option value="Caisse Principale">Caisse Principale</option>
                                    <option value="Caisse Secondaire">Caisse Secondaire</option>
                                    <option value="Caisse Mobile">Caisse Mobile</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="">Choisir un statut</option>
                                    <option value="validé">Validé</option>
                                    <option value="en attente">En attente</option>
                                    <option value="annulé">Annulé</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_remboursement" class="form-label">Date de remboursement prévue</label>
                                <input type="date" class="form-control" id="date_remboursement" name="date_remboursement" placeholder="Date de remboursement">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description detaillee de l'acompte"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Creer l'acompte
                                </button>
                                <a href="{{ route('tresorerie.avances') }}" class="btn btn-outline-secondary">
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
