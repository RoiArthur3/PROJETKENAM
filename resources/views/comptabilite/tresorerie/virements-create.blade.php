@extends('layouts.app')

@section('title', 'Nouveau Virement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nouveau Virement</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.virements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.virements.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="VIR-{{ date('Y-m') }}-001" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_virement" class="form-label">Date de virement</label>
                                <input type="date" class="form-control" id="date_virement" name="date_virement" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="compte_source" class="form-label">Compte source</label>
                                <select class="form-select" id="compte_source" name="compte_source" required>
                                    <option value="">Sélectionner un compte</option>
                                    <option value="Caisse Principale">Caisse Principale</option>
                                    <option value="Caisse Secondaire">Caisse Secondaire</option>
                                    <option value="BIAO">BIAO</option>
                                    <option value="ECOBANK">ECOBANK</option>
                                    <option value="NSIA">NSIA</option>
                                    <option value="SGBCI">SGBCI</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="compte_destination" class="form-label">Compte destination</label>
                                <select class="form-select" id="compte_destination" name="compte_destination" required>
                                    <option value="">Sélectionner un compte</option>
                                    <option value="Caisse Principale">Caisse Principale</option>
                                    <option value="Caisse Secondaire">Caisse Secondaire</option>
                                    <option value="BIAO">BIAO</option>
                                    <option value="ECOBANK">ECOBANK</option>
                                    <option value="NSIA">NSIA</option>
                                    <option value="SGBCI">SGBCI</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">Montant (FCFA)</label>
                                <input type="number" class="form-control" id="montant" name="montant" required min="0" step="100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="motif" class="form-label">Motif</label>
                                <select class="form-select" id="motif" name="motif" required>
                                    <option value="Approvisionnement">Approvisionnement</option>
                                    <option value="Transfert">Transfert</option>
                                    <option value="Réglement">Réglement</option>
                                    <option value="Placement">Placement</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type_virement" class="form-label">Type de virement</label>
                                <select class="form-select" id="type_virement" name="type_virement" required>
                                    <option value="Interne">Interne</option>
                                    <option value="Externe">Externe</option>
                                    <option value="International">International</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="en attente">En attente</option>
                                    <option value="validé">Validé</option>
                                    <option value="exécuté">Exécuté</option>
                                    <option value="annulé">Annulé</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="beneficiaire" class="form-label">Bénéficiaire</label>
                                <input type="text" class="form-control" id="beneficiaire" name="beneficiaire">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reference_bancaire" class="form-label">Référence bancaire</label>
                                <input type="text" class="form-control" id="reference_bancaire" name="reference_bancaire">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="frais" class="form-label">Frais (FCFA)</label>
                                <input type="number" class="form-control" id="frais" name="frais" value="0" min="0" step="100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="taux_change" class="form-label">Taux de change</label>
                                <input type="number" class="form-control" id="taux_change" name="taux_change" value="1" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" name="responsable" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_execution" class="form-label">Date d'exécution</label>
                                <input type="date" class="form-control" id="date_execution" name="date_execution">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Créer le virement
                                </button>
                                <a href="{{ route('tresorerie.virements') }}" class="btn btn-outline-secondary">
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
    // Validation pour éviter de sélectionner le même compte source et destination
    const compteSource = document.getElementById('compte_source');
    const compteDestination = document.getElementById('compte_destination');
    
    compteSource.addEventListener('change', function() {
        if (compteDestination.value === this.value) {
            compteDestination.value = '';
        }
    });
    
    compteDestination.addEventListener('change', function() {
        if (compteSource.value === this.value) {
            compteSource.value = '';
        }
    });
});
</script>

@endsection
