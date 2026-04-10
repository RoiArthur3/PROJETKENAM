@extends('layouts.app')

@section('title', 'Modifier le Contrat - ' . $personnel->nom . ' ' . $personnel->prenoms)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifier le Contrat : {{ $personnel->nom }} {{ $personnel->prenoms }}</h1>
        <a href="{{ route('rh.personnel.show', $personnel->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <form action="{{ route('personnel.update', $personnel->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-warning text-dark">
                <h6 class="m-0 font-weight-bold">Édition des conditions contractuelles</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Type de Contrat</label>
                        <select name="type_contrat" class="form-select" required>
                            <option value="CDI" {{ $personnel->type_contrat == 'CDI' ? 'selected' : '' }}>CDI</option>
                            <option value="CDD" {{ $personnel->type_contrat == 'CDD' ? 'selected' : '' }}>CDD</option>
                            <option value="STAGE" {{ $personnel->type_contrat == 'STAGE' ? 'selected' : '' }}>Stage</option>
                            <option value="INTERIM" {{ $personnel->type_contrat == 'INTERIM' ? 'selected' : '' }}>Intérim</option>
                            <option value="CONSULTANT" {{ $personnel->type_contrat == 'CONSULTANT' ? 'selected' : '' }}>Consultant</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date d'Embauche</label>
                        <input type="date" name="date_embauche" class="form-control" value="{{ $personnel->date_embauche }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date Fin (si CDD)</label>
                        <input type="date" name="date_fin_contrat" class="form-control" value="{{ $personnel->date_fin_contrat }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Catégorie</label>
                        <select name="categorie" class="form-select" required>
                            <option value="A" {{ $personnel->categorie == 'A' ? 'selected' : '' }}>Catégorie A</option>
                            <option value="B" {{ $personnel->categorie == 'B' ? 'selected' : '' }}>Catégorie B</option>
                            <option value="C" {{ $personnel->categorie == 'C' ? 'selected' : '' }}>Catégorie C</option>
                            <option value="D" {{ $personnel->categorie == 'D' ? 'selected' : '' }}>Catégorie D</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Salaire de Base</label>
                        <input type="number" name="salaire_base" class="form-control" value="{{ $personnel->salaire_base }}" required id="salaireBase">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Salaire Horaire</label>
                        <input type="number" name="salaire_horaire" class="form-control" value="{{ $personnel->salaire_horaire ?? number_format($personnel->salaire_base / 173.33, 2, '.', '') }}" required step="0.01" id="salaireHoraire">
                        <small class="text-muted">Calculé automatiquement ou saisissez manuellement</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Devise</label>
                        <select name="devise" class="form-select" required>
                            <option value="XOF" {{ $personnel->devise == 'XOF' ? 'selected' : '' }}>XOF (FCFA)</option>
                            <option value="EUR" {{ $personnel->devise == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                            <option value="USD" {{ $personnel->devise == 'USD' ? 'selected' : '' }}>USD ($)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Fréquence de Paiement</label>
                        <select name="frequence_paiement" class="form-select" required>
                            <option value="MENSUEL" {{ $personnel->frequence_paiement == 'MENSUEL' ? 'selected' : '' }}>Mensuel</option>
                            <option value="HEBDOMADAIRE" {{ $personnel->frequence_paiement == 'HEBDOMADAIRE' ? 'selected' : '' }}>Hebdomadaire</option>
                            <option value="QUINZOMADAIRE" {{ $personnel->frequence_paiement == 'QUINZOMADAIRE' ? 'selected' : '' }}>Quinzomadaire</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Période d'essai (jours)</label>
                        <input type="number" name="duree_essai_jours" class="form-control" value="{{ $personnel->duree_essai_jours }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Service</label>
                        <input type="text" name="service" class="form-control" value="{{ $personnel->service }}" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3 bg-info text-white">
                <h6 class="m-0 font-weight-bold">Informations CNPS & Fiscalité</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Numéro CNPS</label>
                        <input type="text" name="numero_cnps" class="form-control" value="{{ $personnel->numero_cnps }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Date Affiliation CNPS</label>
                        <input type="date" name="date_affiliation_cnps" class="form-control" value="{{ $personnel->date_affiliation_cnps }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Catégorie CNPS</label>
                        <select name="categorie_cnps" class="form-select">
                            <option value="">Sélectionner...</option>
                            <option value="A" {{ $personnel->categorie_cnps == 'A' ? 'selected' : '' }}>Catégorie A</option>
                            <option value="B" {{ $personnel->categorie_cnps == 'B' ? 'selected' : '' }}>Catégorie B</option>
                            <option value="C" {{ $personnel->categorie_cnps == 'C' ? 'selected' : '' }}>Catégorie C</option>
                            <option value="D" {{ $personnel->categorie_cnps == 'D' ? 'selected' : '' }}>Catégorie D</option>
                            <option value="E" {{ $personnel->categorie_cnps == 'E' ? 'selected' : '' }}>Catégorie E</option>
                            <option value="F" {{ $personnel->categorie_cnps == 'F' ? 'selected' : '' }}>Catégorie F</option>
                            <option value="G" {{ $personnel->categorie_cnps == 'G' ? 'selected' : '' }}>Catégorie G</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Numéro Contribuable</label>
                        <input type="text" name="numero_contribuable" class="form-control" value="{{ $personnel->numero_contribuable }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Nombre de Parts Fiscales</label>
                        <input type="number" name="nb_parts_fiscales" class="form-control" value="{{ $personnel->nb_parts_fiscales }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Situation Fiscale</label>
                        <select name="situation_fiscale" class="form-select" required>
                            <option value="IMPOSABLE" {{ $personnel->situation_fiscale == 'IMPOSABLE' ? 'selected' : '' }}>Imposable</option>
                            <option value="NON_IMPOSABLE" {{ $personnel->situation_fiscale == 'NON_IMPOSABLE' ? 'selected' : '' }}>Non Imposable</option>
                            <option value="EXONERE" {{ $personnel->situation_fiscale == 'EXONERE' ? 'selected' : '' }}>Exonéré</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mt-4">
            <a href="{{ route('rh.personnel.show', $personnel->id) }}" class="btn btn-secondary me-2">
                <i class="fas fa-times me-1"></i>Annuler
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcul automatique du salaire horaire
    const salaireBaseInput = document.getElementById('salaireBase');
    const salaireHoraireInput = document.getElementById('salaireHoraire');
    const frequencePaiementSelect = document.querySelector('select[name="frequence_paiement"]');

    function calculerSalaireHoraire() {
        const salaireBase = parseFloat(salaireBaseInput.value) || 0;
        const frequence = frequencePaiementSelect.value;
        let heuresParMois = 173.33; // Base: 40h/semaine

        // Ajuster selon la fréquence de paiement
        if (frequence === 'HEBDOMADAIRE') {
            heuresParMois = 40; // 40 heures par semaine
        } else if (frequence === 'QUINZOMADAIRE') {
            heuresParMois = 80; // 2 semaines
        }

        const salaireHoraire = salaireBase / heuresParMois;
        salaireHoraireInput.value = salaireHoraire.toFixed(2);
    }

    if (salaireBaseInput && salaireHoraireInput) {
        salaireBaseInput.addEventListener('input', calculerSalaireHoraire);
        frequencePaiementSelect.addEventListener('change', calculerSalaireHoraire);

        // Permettre aussi la saisie manuelle du salaire horaire
        salaireHoraireInput.addEventListener('input', function() {
            // Si l'utilisateur saisit manuellement, ne plus recalculer automatiquement
            salaireBaseInput.removeEventListener('input', calculerSalaireHoraire);
        });
    }
});
</script>
@endpush
