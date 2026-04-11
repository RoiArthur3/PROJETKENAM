@extends('layouts.app')

@section('title', 'Créer Fournisseur Kenam - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Créer un Fournisseur Kenam</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('fournisseurs.engins.list') }}" class="btn btn-outline-info">
                <i class="fas fa-truck me-2"></i>Fournisseurs Externes
            </a>
            <a href="{{ route('fournisseurs.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Informations du Fournisseur Kenam
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('fournisseurs.store') }}" method="POST" id="fournisseurKenamForm">
                        @csrf

                        <!-- Type de fournisseur -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="type_fournisseur" class="form-label">Type de fournisseur</label>
                                <select class="form-select" id="type_fournisseur" name="type_fournisseur" required>
                                    <option value="">Sélectionner le type</option>
                                    <option value="kenam_interne" selected>Interne - Kenam Services</option>
                                </select>
                                <div class="form-text">Ce fournisseur fait partie de l'entreprise Kenam Services</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="specialite" class="form-label">Spécialité</label>
                                <select class="form-select" id="specialite" name="specialite">
                                    <option value="">Sélectionner la spécialité</option>
                                    <option value="transport">Transport</option>
                                    <option value="engins_chantier">Engins de chantier</option>
                                    <option value="vehicules_legers">Véhicules légers</option>
                                    <option value="camions">Camions</option>
                                    <option value="divers">Divers</option>
                                </select>
                            </div>
                        </div>

                        <!-- Informations Principales -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Informations du Fournisseur
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="raison_sociale" class="form-label">Raison Sociale</label>
                                <input type="text" class="form-control" id="raison_sociale" name="raison_sociale"
                                       placeholder="Ex: Kenam Services - Division Transport" value="{{ old('raison_sociale') }}" required>
                                @error('raison_sociale')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="service_interne" class="form-label">Service Interne</label>
                                <input type="text" class="form-control" id="service_interne" name="service_interne"
                                       placeholder="Ex: Division Transport" value="{{ old('service_interne') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" name="responsable"
                                       placeholder="Nom du responsable" value="{{ old('responsable') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telephone_interne" class="form-label">Téléphone Interne</label>
                                <input type="tel" class="form-control" id="telephone_interne" name="telephone_interne"
                                       placeholder="Extension ou numéro interne" value="{{ old('telephone_interne') }}">
                            </div>
                        </div>

                        <!-- Coordonnées -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-phone me-2"></i>
                                    Coordonnées
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="contact@kenam-ci.com" value="{{ old('email') }}">
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone"
                                       placeholder="+225 00 00 00 00" value="{{ old('telephone') }}">
                                @error('telephone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="adresse" name="adresse"
                                       placeholder="Siège social Kenam" value="{{ old('adresse') }}">
                            </div>
                        </div>

                        <!-- Engins associés -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-truck me-2"></i>
                                    Engins Associés
                                </h6>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="enginsTable">
                                        <thead>
                                            <tr>
                                                <th>Immatriculation</th>
                                                <th>Marque/Modèle</th>
                                                <th>Type</th>
                                                <th>Tarif Journalier (FCFA)</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="enginRow1">
                                                <td>
                                                    <input type="text" class="form-control" name="engins[0][immatriculation]" placeholder="ABC-123">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="engins[0][marque_modele]" placeholder="Volvo FH16">
                                                </td>
                                                <td>
                                                    <select class="form-select" name="engins[0][type]">
                                                        <option value="">Type</option>
                                                        <option value="camion">Camion</option>
                                                        <option value="engin">Engin</option>
                                                        <option value="vehicule_leger">Véhicule léger</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" name="engins[0][tarif_journalier]" placeholder="50000" step="1000">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeEnginRow(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-sm btn-success mt-2" onclick="addEnginRow()">
                                        <i class="fas fa-plus"></i> Ajouter un engin
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Conditions financières -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-euro-sign me-2"></i>
                                            Conditions Financières
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="mode_reglement" class="form-label">Mode de Règlement</label>
                                            <select class="form-select" id="mode_reglement" name="mode_reglement">
                                                <option value="">Sélectionner</option>
                                                <option value="interne">Transfert interne</option>
                                                <option value="virement">Virement bancaire</option>
                                                <option value="espece">Espèces</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="condition_reglement" class="form-label">Conditions de Règlement</label>
                                            <select class="form-select" id="condition_reglement" name="condition_reglement">
                                                <option value="">Sélectionner</option>
                                                <option value="immediat">Immédiat</option>
                                                <option value="30jours">30 jours</option>
                                                <option value="comptant">Comptant</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-sticky-note me-2"></i>
                                            Informations Supplémentaires
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="4"
                                                      placeholder="Informations supplémentaires sur le fournisseur interne...">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('fournisseurs.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="spinner"></span>
                                <i class="fas fa-save me-2"></i>
                                Créer le Fournisseur Kenam
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let enginRowCount = 1;

function addEnginRow() {
    enginRowCount++;
    const tbody = document.querySelector('#enginsTable tbody');
    const newRow = document.createElement('tr');
    newRow.id = 'enginRow' + enginRowCount;
    newRow.innerHTML = `
        <td>
            <input type="text" class="form-control" name="engins[${enginRowCount-1}][immatriculation]" placeholder="ABC-123">
        </td>
        <td>
            <input type="text" class="form-control" name="engins[${enginRowCount-1}][marque_modele]" placeholder="Volvo FH16">
        </td>
        <td>
            <select class="form-select" name="engins[${enginRowCount-1}][type]">
                <option value="">Type</option>
                <option value="camion">Camion</option>
                <option value="engin">Engin</option>
                <option value="vehicule_leger">Véhicule léger</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control" name="engins[${enginRowCount-1}][tarif_journalier]" placeholder="50000" step="1000">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeEnginRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);

    // Animation d'ajout
    newRow.style.opacity = '0';
    newRow.style.transform = 'translateX(-20px)';
    setTimeout(() => {
        newRow.style.transition = 'all 0.3s ease';
        newRow.style.opacity = '1';
        newRow.style.transform = 'translateX(0)';
    }, 10);
}

function removeEnginRow(button) {
    const row = button.closest('tr');
    if (document.querySelectorAll('#enginsTable tbody tr').length > 1) {
        // Animation de suppression
        row.style.transition = 'all 0.3s ease';
        row.style.opacity = '0';
        row.style.transform = 'translateX(20px)';
        setTimeout(() => {
            row.remove();
        }, 300);
    } else {
        alert('Au moins un engin doit être spécifié');
    }
}

// Validation du formulaire
document.getElementById('fournisseurKenamForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');

    // Validation des champs requis
    const raisonSociale = document.getElementById('raison_sociale');
    if (!raisonSociale.value.trim()) {
        e.preventDefault();
        raisonSociale.focus();
        alert('Veuillez renseigner la raison sociale du fournisseur');
        return;
    }

    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Création en cours...';
});

// Animation des cartes au chargement
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

<style>
.form-control:focus, .form-select:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 0.2rem rgba(22, 163, 74, 0.25);
}

.card {
    border: 1px solid rgba(0, 0, 0, 0.125);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>
@endsection
