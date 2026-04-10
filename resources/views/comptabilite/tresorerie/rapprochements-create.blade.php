@extends('layouts.app')

@section('title', 'Nouveau Rapprochement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nouveau Rapprochement Bancaire</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.rapprochements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.rapprochements.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" required placeholder="RAP-2024-XXX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_rapprochement" class="form-label">Date de rapprochement</label>
                                <input type="date" class="form-control" id="date_rapprochement" name="date_rapprochement" required value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="compte_bancaire" class="form-label">Compte bancaire</label>
                                <select class="form-select" id="compte_bancaire" name="compte_bancaire" required>
                                    <option value="">Choisir un compte</option>
                                    <option value="ECOBANK">ECOBANK</option>
                                    <option value="SGBCI">SGBCI</option>
                                    <option value="BIAO">BIAO</option>
                                    <option value="NSIA BANQUE">NSIA BANQUE</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="periode" class="form-label">Période</label>
                                <input type="text" class="form-control" id="periode" name="periode" required placeholder="Ex: Janvier 2024">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="solde_bancaire" class="form-label">Solde bancaire (FCFA)</label>
                                <input type="number" class="form-control" id="solde_bancaire" name="solde_bancaire" required placeholder="0" min="0" step="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="solde_comptable" class="form-label">Solde comptable (FCFA)</label>
                                <input type="number" class="form-control" id="solde_comptable" name="solde_comptable" required placeholder="0" min="0" step="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ecart" class="form-label">Écart (FCFA)</label>
                                <input type="number" class="form-control" id="ecart" name="ecart" placeholder="Calculé automatiquement" readonly>
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
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" name="responsable" required placeholder="Nom du responsable">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="observations" class="form-label">Observations</label>
                                <textarea class="form-control" id="observations" name="observations" rows="3" placeholder="Observations sur le rapprochement"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Créer le rapprochement
                                </button>
                                <a href="{{ route('tresorerie.rapprochements') }}" class="btn btn-outline-secondary">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const soldeBancaire = document.getElementById('solde_bancaire');
    const soldeComptable = document.getElementById('solde_comptable');
    const ecart = document.getElementById('ecart');

    function calculerEcart() {
        const bancaire = parseFloat(soldeBancaire.value) || 0;
        const comptable = parseFloat(soldeComptable.value) || 0;
        const ecartValue = bancaire - comptable;
        ecart.value = ecartValue;

        // Mettre à jour le style en fonction de l'écart
        if (ecartValue > 0) {
            ecart.classList.add('text-danger');
            ecart.classList.remove('text-success', 'text-warning');
        } else if (ecartValue < 0) {
            ecart.classList.add('text-warning');
            ecart.classList.remove('text-success', 'text-danger');
        } else {
            ecart.classList.add('text-success');
            ecart.classList.remove('text-danger', 'text-warning');
        }
    }

    soldeBancaire.addEventListener('input', calculerEcart);
    soldeComptable.addEventListener('input', calculerEcart);
});
</script>

@endsection
