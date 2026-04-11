@extends('layouts.app')

@section('title', 'Nouveau dossier de financement')

@section('content')
<div class="content-wrapper">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-hand-holding-usd me-2"></i>Nouveau dossier de financement
        </h1>
        <a href="{{ route('juridique.financements.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    <!-- Formulaire -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('juridique.financements.store') }}">
                @csrf

                <div class="row g-3">
                    <!-- Référence -->
                    <div class="col-md-4">
                        <label for="reference" class="form-label">Référence</label>
                        <input type="text" id="reference" name="reference" class="form-control"
                               value="{{ old('reference') }}" placeholder="Ex: FIN-2026-001">
                        @error('reference')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Titre -->
                    <div class="col-md-8">
                        <label for="titre" class="form-label">Intitulé <span class="text-danger">*</span></label>
                        <input type="text" id="titre" name="titre" class="form-control"
                               value="{{ old('titre') }}" required placeholder="Intitulé du financement">
                        @error('titre')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Type de financement -->
                    <div class="col-md-4">
                        <label for="type_financement" class="form-label">Type de financement</label>
                        <select id="type_financement" name="type_financement" class="form-select">
                            <option value="">Sélectionner un type</option>
                            <option value="bancaire" {{ old('type_financement') == 'bancaire' ? 'selected' : '' }}>Bancaire</option>
                            <option value="propre" {{ old('type_financement') == 'propre' ? 'selected' : '' }}>Propre</option>
                            <option value="leasing" {{ old('type_financement') == 'leasing' ? 'selected' : '' }}>Leasing</option>
                            <option value="credit_bail" {{ old('type_financement') == 'credit_bail' ? 'selected' : '' }}>Crédit-bail</option>
                            <option value="subvention" {{ old('type_financement') == 'subvention' ? 'selected' : '' }}>Subvention</option>
                        </select>
                        @error('type_financement')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Organisme prêteur -->
                    <div class="col-md-8">
                        <label for="organisme_preteur" class="form-label">Organisme prêteur</label>
                        <input type="text" id="organisme_preteur" name="organisme_preteur" class="form-control"
                               value="{{ old('organisme_preteur') }}" placeholder="Nom de la banque ou organisme">
                        @error('organisme_preteur')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Contrat lié -->
                    <div class="col-12">
                        <label for="contrat_id" class="form-label">Contrat lié</label>
                        <select id="contrat_id" name="contrat_id" class="form-select">
                            <option value="">Aucun contrat</option>
                            @foreach($contrats as $contrat)
                                <option value="{{ $contrat->id }}" {{ old('contrat_id') == $contrat->id ? 'selected' : '' }}>
                                    {{ $contrat->titre }} ({{ $contrat->reference }})
                                </option>
                            @endforeach
                        </select>
                        @error('contrat_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"
                                  placeholder="Description du projet de financement...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Montant emprunté -->
                    <div class="col-md-3">
                        <label for="montant_emprunte" class="form-label">Montant emprunté</label>
                        <input type="number" id="montant_emprunte" name="montant_emprunte" class="form-control"
                               step="0.01" value="{{ old('montant_emprunte') }}" placeholder="0.00">
                        @error('montant_emprunte')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Taux d'intérêt -->
                    <div class="col-md-3">
                        <label for="taux_interet" class="form-label">Taux d'intérêt (%)</label>
                        <input type="number" id="taux_interet" name="taux_interet" class="form-control"
                               step="0.01" value="{{ old('taux_interet') }}" placeholder="0.00">
                        @error('taux_interet')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Durée -->
                    <div class="col-md-3">
                        <label for="duree_mois" class="form-label">Durée (mois)</label>
                        <input type="number" id="duree_mois" name="duree_mois" class="form-control"
                               value="{{ old('duree_mois') }}" placeholder="12">
                        @error('duree_mois')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mensualité -->
                    <div class="col-md-3">
                        <label for="mensualite" class="form-label">Mensualité</label>
                        <input type="number" id="mensualite" name="mensualite" class="form-control"
                               step="0.01" value="{{ old('mensualite') }}" placeholder="0.00">
                        <small class="text-muted">Calculée automatiquement</small>
                        @error('mensualite')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dates -->
                    <div class="col-md-4">
                        <label for="date_debut" class="form-label">Date de début</label>
                        <input type="date" id="date_debut" name="date_debut" class="form-control"
                               value="{{ old('date_debut') }}">
                        @error('date_debut')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="date_fin" class="form-label">Date de fin</label>
                        <input type="date" id="date_fin" name="date_fin" class="form-control"
                               value="{{ old('date_fin') }}">
                        @error('date_fin')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="devise" class="form-label">Devise</label>
                        <select id="devise" name="devise" class="form-select">
                            <option value="XOF" {{ old('devise', 'XOF') == 'XOF' ? 'selected' : '' }}>XOF (FCFA)</option>
                            <option value="EUR" {{ old('devise') == 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                            <option value="USD" {{ old('devise') == 'USD' ? 'selected' : '' }}>USD (Dollar)</option>
                        </select>
                        @error('devise')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div class="col-md-4">
                        <label for="statut" class="form-label">Statut</label>
                        <select id="statut" name="statut" class="form-select">
                            <option value="en_cours" {{ old('statut', 'en_cours') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="en_preparation" {{ old('statut') == 'en_preparation' ? 'selected' : '' }}>En préparation</option>
                            <option value="termine" {{ old('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                            <option value="suspendu" {{ old('statut') == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                            <option value="annule" {{ old('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                        </select>
                        @error('statut')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3"
                                  placeholder="Notes ou observations sur ce financement...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('juridique.financements.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer le dossier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-body {
    padding: 2rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    padding: 0.5rem 1.5rem;
}

.text-danger {
    font-size: 0.875rem;
}

.text-muted {
    font-size: 0.75rem;
}
</style>

<script>
// Auto-génération de référence
document.addEventListener('DOMContentLoaded', function() {
    const referenceInput = document.getElementById('reference');
    const titreInput = document.getElementById('titre');

    // Si la référence est vide, en générer une automatiquement
    if (!referenceInput.value) {
        const today = new Date();
        const year = today.getFullYear();
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        referenceInput.value = `FIN-${year}-${random}`;
    }

    // Calcul automatique de la mensualité
    const montantEmprunte = document.getElementById('montant_emprunte');
    const tauxInteret = document.getElementById('taux_interet');
    const dureeMois = document.getElementById('duree_mois');
    const mensualite = document.getElementById('mensualite');

    function calculerMensualite() {
        const montant = parseFloat(montantEmprunte.value) || 0;
        const taux = parseFloat(tauxInteret.value) || 0;
        const duree = parseInt(dureeMois.value) || 0;

        if (montant > 0 && taux > 0 && duree > 0) {
            // Formule simple de calcul de mensualité
            const tauxMensuel = taux / 100 / 12;
            const mensualiteCalc = (montant * tauxMensuel * Math.pow(1 + tauxMensuel, duree)) / (Math.pow(1 + tauxMensuel, duree) - 1);
            mensualite.value = mensualiteCalc.toFixed(2);
        }
    }

    montantEmprunte.addEventListener('input', calculerMensualite);
    tauxInteret.addEventListener('input', calculerMensualite);
    dureeMois.addEventListener('input', calculerMensualite);

    // Validation de la date de fin
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');

    dateFin.addEventListener('change', function() {
        if (dateDebut.value && this.value) {
            if (new Date(this.value) < new Date(dateDebut.value)) {
                alert('La date de fin ne peut pas être antérieure à la date de début.');
                this.value = '';
            }
        }
    });

    // Animation des champs
    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach((input, index) => {
        input.style.opacity = '0';
        input.style.transform = 'translateY(10px)';
        setTimeout(() => {
            input.style.transition = 'all 0.3s ease';
            input.style.opacity = '1';
            input.style.transform = 'translateY(0)';
        }, index * 50);
    });
});
</script>
@endsection
