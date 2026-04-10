@extends('layouts.app')

@section('title', 'Modifier Virement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier Virement</h1>
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
                    <form method="POST" action="{{ route('tresorerie.virements.update', $virement->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="{{ $virement->reference }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_virement" class="form-label">Date de virement</label>
                                <input type="date" class="form-control" id="date_virement" name="date_virement" value="{{ \Carbon\Carbon::parse($virement->date_virement)->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="compte_source" class="form-label">Compte source</label>
                                <select class="form-select" id="compte_source" name="compte_source" required>
                                    <option value="">Sélectionner un compte</option>
                                    <option value="Caisse Principale" {{ $virement->compte_source == 'Caisse Principale' ? 'selected' : '' }}>Caisse Principale</option>
                                    <option value="Caisse Secondaire" {{ $virement->compte_source == 'Caisse Secondaire' ? 'selected' : '' }}>Caisse Secondaire</option>
                                    <option value="BIAO" {{ $virement->compte_source == 'BIAO' ? 'selected' : '' }}>BIAO</option>
                                    <option value="ECOBANK" {{ $virement->compte_source == 'ECOBANK' ? 'selected' : '' }}>ECOBANK</option>
                                    <option value="NSIA" {{ $virement->compte_source == 'NSIA' ? 'selected' : '' }}>NSIA</option>
                                    <option value="SGBCI" {{ $virement->compte_source == 'SGBCI' ? 'selected' : '' }}>SGBCI</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="compte_destination" class="form-label">Compte destination</label>
                                <select class="form-select" id="compte_destination" name="compte_destination" required>
                                    <option value="">Sélectionner un compte</option>
                                    <option value="Caisse Principale" {{ $virement->compte_destination == 'Caisse Principale' ? 'selected' : '' }}>Caisse Principale</option>
                                    <option value="Caisse Secondaire" {{ $virement->compte_destination == 'Caisse Secondaire' ? 'selected' : '' }}>Caisse Secondaire</option>
                                    <option value="BIAO" {{ $virement->compte_destination == 'BIAO' ? 'selected' : '' }}>BIAO</option>
                                    <option value="ECOBANK" {{ $virement->compte_destination == 'ECOBANK' ? 'selected' : '' }}>ECOBANK</option>
                                    <option value="NSIA" {{ $virement->compte_destination == 'NSIA' ? 'selected' : '' }}>NSIA</option>
                                    <option value="SGBCI" {{ $virement->compte_destination == 'SGBCI' ? 'selected' : '' }}>SGBCI</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">Montant (FCFA)</label>
                                <input type="number" class="form-control" id="montant" name="montant" value="{{ $virement->montant }}" required min="0" step="100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="motif" class="form-label">Motif</label>
                                <select class="form-select" id="motif" name="motif" required>
                                    <option value="Approvisionnement" {{ $virement->motif == 'Approvisionnement' ? 'selected' : '' }}>Approvisionnement</option>
                                    <option value="Transfert" {{ $virement->motif == 'Transfert' ? 'selected' : '' }}>Transfert</option>
                                    <option value="Réglement" {{ $virement->motif == 'Réglement' ? 'selected' : '' }}>Réglement</option>
                                    <option value="Placement" {{ $virement->motif == 'Placement' ? 'selected' : '' }}>Placement</option>
                                    <option value="Autre" {{ $virement->motif == 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type_virement" class="form-label">Type de virement</label>
                                <select class="form-select" id="type_virement" name="type_virement" required>
                                    <option value="Interne" {{ $virement->type_virement == 'Interne' ? 'selected' : '' }}>Interne</option>
                                    <option value="Externe" {{ $virement->type_virement == 'Externe' ? 'selected' : '' }}>Externe</option>
                                    <option value="International" {{ $virement->type_virement == 'International' ? 'selected' : '' }}>International</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="en attente" {{ $virement->statut == 'en attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="validé" {{ $virement->statut == 'validé' ? 'selected' : '' }}>Validé</option>
                                    <option value="exécuté" {{ $virement->statut == 'exécuté' ? 'selected' : '' }}>Exécuté</option>
                                    <option value="annulé" {{ $virement->statut == 'annulé' ? 'selected' : '' }}>Annulé</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="beneficiaire" class="form-label">Bénéficiaire</label>
                                <input type="text" class="form-control" id="beneficiaire" name="beneficiaire" value="{{ $virement->beneficiaire ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reference_bancaire" class="form-label">Référence bancaire</label>
                                <input type="text" class="form-control" id="reference_bancaire" name="reference_bancaire" value="{{ $virement->reference_bancaire ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="frais" class="form-label">Frais (FCFA)</label>
                                <input type="number" class="form-control" id="frais" name="frais" value="{{ $virement->frais ?? 0 }}" min="0" step="100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="taux_change" class="form-label">Taux de change</label>
                                <input type="number" class="form-control" id="taux_change" name="taux_change" value="{{ $virement->taux_change ?? 1 }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" name="responsable" value="{{ $virement->responsable }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_execution" class="form-label">Date d'exécution</label>
                                <input type="date" class="form-control" id="date_execution" name="date_execution" value="{{ $virement->date_execution ? \Carbon\Carbon::parse($virement->date_execution)->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ $virement->notes ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
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
