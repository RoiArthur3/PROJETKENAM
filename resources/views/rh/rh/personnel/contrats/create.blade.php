@extends('layouts.app')

@section('title', 'Nouveau Contrat de Travail | KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="display-6 fw-bold text-success mb-1">
                        <i class="fas fa-file-contract me-3"></i>Génération de Contrat
                    </h2>
                    <p class="text-muted fs-5">Conforme à la Convention Collective Interprofessionnelle de Côte d'Ivoire</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('personnel.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-2"></i>Liste Personnel
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-success py-3 text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 h4"><i class="fas fa-pen-nib me-2"></i>Nouveau Contrat de Travail</h5>
                    <span class="badge bg-white text-success fw-bold">Version 2024.1</span>
                </div>
                
                <div class="card-body p-4 p-xl-5">
                    <form action="{{ route('personnel.contrats.store') }}" method="POST" id="contratForm">
                        @csrf

                        <!-- Étape 1: Identification de l'Employé -->
                        <div class="row mb-5">
                            <div class="col-12 mb-4">
                                <h5 class="fw-bold text-success border-bottom pb-2">
                                    <i class="fas fa-user-tie me-2"></i>1. Identification du Salarié
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Sélectionner l'employé *</label>
                                <select name="personnel_id" class="form-select @error('personnel_id') is-invalid @enderror" id="personnel_id" required>
                                    <option value="">-- Choisir un employé --</option>
                                    @foreach($personnels as $p)
                                        <option value="{{ $p->id }}" {{ (isset($personnel) && $personnel->id == $p->id) ? 'selected' : '' }}>
                                            {{ $p->matricule }} - {{ $p->nom }} {{ $p->prenoms }} ({{ $p->poste }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('personnel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Numéro de Contrat (Référence) *</label>
                                <input type="text" name="numero_contrat" class="form-control @error('numero_contrat') is-invalid @enderror" 
                                       placeholder="Ex: KEN-RH-2024-042" value="{{ old('numero_contrat', 'KEN-RH-' . date('Y') . '-' . rand(100, 999)) }}" required>
                                @error('numero_contrat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <!-- Étape 2: Nature et Objet -->
                        <div class="row mb-5">
                            <div class="col-12 mb-4">
                                <h5 class="fw-bold text-success border-bottom pb-2">
                                    <i class="fas fa-briefcase me-2"></i>2. Nature et Objet du Contrat
                                </h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Type de Contrat *</label>
                                <select name="type_contrat" id="type_contrat" class="form-select" required>
                                    <option value="CDI">CDI (Indéterminé)</option>
                                    <option value="CDD">CDD (Déterminé)</option>
                                    <option value="STAGE">Stage de Qualification</option>
                                    <option value="INTERIM">Intérim / Temporaire</option>
                                    <option value="CONSULTANT">Prestation de Service</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Date de Début (Effet) *</label>
                                <input type="date" name="date_debut" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-4 mb-3" id="date_fin_group" style="display:none;">
                                <label class="form-label fw-bold">Date de Fin (Terme)</label>
                                <input type="date" name="date_fin" class="form-control">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Période d'Essai (Jours)</label>
                                <input type="number" name="duree_essai_jours" class="form-control" value="30" min="0">
                                <small class="text-muted">Généralement 1 à 6 mois selon la catégorie.</small>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Poste de Travail *</label>
                                <input type="text" name="poste" class="form-control" placeholder="Intitulé exact du poste" value="{{ old('poste', $personnel->poste ?? '') }}" required>
                            </div>
                        </div>

                        <!-- Étape 3: Rémunération - Ivorian Specific -->
                        <div class="row mb-5">
                            <div class="col-12 mb-4">
                                <h5 class="fw-bold text-success border-bottom pb-2">
                                    <i class="fas fa-money-bill-wave me-2"></i>3. Conditions Financières (Barème Côte d'Ivoire)
                                </h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Salaire de Base Brut *</label>
                                <div class="input-group">
                                    <input type="number" name="salaire_base" class="form-control form-control-lg text-primary fw-bold" 
                                           value="{{ old('salaire_base', $personnel->salaire_base ?? '') }}" required>
                                    <span class="input-group-text bg-light fw-bold">FCFA (XOF)</span>
                                </div>
                                <small class="text-success" id="smi_label">SMIG Minimum: 75 000 FCFA</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Fréquence Paiement</label>
                                <select name="frequence_paiement" class="form-select">
                                    <option value="MENSUEL">Mensuel (Fin de mois)</option>
                                    <option value="QUINZOMADAIRE">Quinzaine</option>
                                    <option value="JOURNALIER">Journalier</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold">Devise</label>
                                <input type="text" name="devise" class="form-control bg-light" value="XOF" readonly>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="form-label fw-bold">Indemnités & Primes Conventionnelles</label>
                                <textarea name="avantages" class="form-control" rows="3" 
                                          placeholder="Ex: Indemnité de Transport (30.000), Indemnité de Logement (15.000), Prime de Panier...">{{ old('avantages') }}</textarea>
                                <div class="form-text">Listez les éléments qui s'ajoutent au salaire de base selon la convention.</div>
                            </div>
                        </div>

                        <!-- Étape 4: Conditions de Travail -->
                        <div class="row mb-5">
                            <div class="col-12 mb-4">
                                <h5 class="fw-bold text-success border-bottom pb-2">
                                    <i class="fas fa-map-marker-alt me-2"></i>4. Conditions de Travail
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Lieu d'Exécution *</label>
                                <input type="text" name="lieu_travail" class="form-control" value="Abidjan, Côte-d'Ivoire" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Statut du Contrat *</label>
                                <select name="statut" class="form-select border-primary" required>
                                    <option value="PROJET">Brouillon / Projet</option>
                                    <option value="SIGNE">Signé (En attente début)</option>
                                    <option value="ACTIF">Actif / En cours</option>
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Description des Missions (Brief)</label>
                                <textarea name="description_taches" class="form-control" rows="4" 
                                          placeholder="Résumé des tâches principales qui seront annexées à la fiche de poste.">{{ old('description_taches') }}</textarea>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="alert alert-warning mb-4 shadow-sm border-0 d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x me-3 opacity-50"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">Validation Légale</h6>
                                <p class="mb-0 small text-dark-50">Ce formulaire génère les données du contrat. Assurez-vous de le faire valider par un conseil juridique avant signature définitive.</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                            <button type="reset" class="btn btn-light btn-lg text-secondary px-5">
                                <i class="fas fa-times me-2"></i>Annuler
                            </button>
                            <button type="submit" class="btn btn-success btn-lg px-5 shadow-lg">
                                <i class="fas fa-save me-2"></i>Générer le Dossier Contrat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type_contrat');
        const finGroup = document.getElementById('date_fin_group');
        
        typeSelect.addEventListener('change', function() {
            if (['CDD', 'STAGE', 'INTERIM'].includes(this.value)) {
                finGroup.style.display = 'block';
                finGroup.querySelector('input').required = true;
            } else {
                finGroup.style.display = 'none';
                finGroup.querySelector('input').required = false;
            }
        });

        // Trigger change on load if needed
        typeSelect.dispatchEvent(new Event('change'));
    });
</script>
@endpush

<style>
    .card-header {
        background: linear-gradient(135deg, #198754 0%, #20c997 100%);
    }
    .form-control:focus, .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.15);
    }
</style>
@endsection
