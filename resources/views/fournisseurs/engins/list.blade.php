@extends('layouts.app')

@section('title', 'Créer Fournisseur Externe - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Créer un Fournisseur Externe</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('fournisseurs.engins.kenam') }}" class="btn btn-outline-primary">
                <i class="fas fa-building me-2"></i>Fournisseurs Kenam
            </a>
            <a href="{{ route('fournisseurs.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-truck me-2"></i>
                        Informations du Fournisseur Externe
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('fournisseurs.store') }}" method="POST" id="fournisseurExterneForm">
                        @csrf

                        <!-- Type de fournisseur -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="type_fournisseur" class="form-label">Type de fournisseur</label>
                                <select class="form-select" id="type_fournisseur" name="type_fournisseur" required>
                                    <option value="">Sélectionner le type</option>
                                    <option value="engins_externe" selected>Externe - Prestataire</option>
                                </select>
                                <div class="form-text">Ce fournisseur est un prestataire externe de services d'engins</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="specialite" class="form-label">Spécialité</label>
                                <select class="form-select" id="specialite" name="specialite">
                                    <option value="">Sélectionner la spécialité</option>
                                    <option value="transport">Transport</option>
                                    <option value="engins_chantier">Engins de chantier</option>
                                    <option value="vehicules_legers">Véhicules légers</option>
                                    <option value="camions">Camions</option>
                                    <option value="location_engins">Location d'engins</option>
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
                                       placeholder="Ex: Transport Express CI" value="{{ old('raison_sociale') }}" required>
                                @error('raison_sociale')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="forme_juridique" class="form-label">Forme Juridique</label>
                                <select class="form-select" id="forme_juridique" name="forme_juridique">
                                    <option value="">Sélectionner</option>
                                    <option value="SAS">SAS</option>
                                    <option value="SARL">SARL</option>
                                    <option value="EURL">EURL</option>
                                    <option value="Auto-entrepreneur">Auto-entrepreneur</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="siret" class="form-label">SIRET</label>
                                <input type="text" class="form-control" id="siret" name="siret"
                                       placeholder="123 456 789 00012" value="{{ old('siret') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tva_intracom" class="form-label">N° TVA Intracom</label>
                                <input type="text" class="form-control" id="tva_intracom" name="tva_intracom"
                                       placeholder="FR12345678901" value="{{ old('tva_intracom') }}">
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
                                       placeholder="contact@transport-ci.com" value="{{ old('email') }}">
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
                            <div class="col-md-12 mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="adresse" name="adresse"
                                       placeholder="123 Rue du Commerce, Abidjan" value="{{ old('adresse') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="code_postal" class="form-label">Code Postal</label>
                                <input type="text" class="form-control" id="code_postal" name="code_postal"
                                       placeholder="01 BP 1234" value="{{ old('code_postal') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ville" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="ville" name="ville"
                                       placeholder="Abidjan" value="{{ old('ville') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="pays" class="form-label">Pays</label>
                                <input type="text" class="form-control" id="pays" name="pays"
                                       placeholder="Côte d'Ivoire" value="{{ old('pays', 'Côte d\'Ivoire') }}">
                            </div>
                        </div>

                        <!-- Contact principal -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-user-tie me-2"></i>
                                    Contact Principal
                                </h6>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contact_nom" class="form-label">Nom du contact</label>
                                <input type="text" class="form-control" id="contact_nom" name="contact_nom"
                                       placeholder="Nom du contact principal" value="{{ old('contact_nom') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contact_fonction" class="form-label">Fonction</label>
                                <input type="text" class="form-control" id="contact_fonction" name="contact_fonction"
                                       placeholder="Directeur commercial" value="{{ old('contact_fonction') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contact_telephone" class="form-label">Téléphone du contact</label>
                                <input type="tel" class="form-control" id="contact_telephone" name="contact_telephone"
                                       placeholder="+225 00 00 00 00" value="{{ old('contact_telephone') }}">
                            </div>
                        </div>

                        <!-- Engins proposés -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-truck me-2"></i>
                                    Engins Proposés
                                </h6>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="enginsTable">
                                        <thead>
                                            <tr>
                                                <th>Type d'engin</th>
                                                <th>Quantité disponible</th>
                                                <th>Tarif Journalier (FCFA)</th>
                                                <th>Conditions</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="enginRow1">
                                                <td>
                                                    <select class="form-select" name="engins[0][type]">
                                                        <option value="">Type d'engin</option>
                                                        <option value="camion">Camion</option>
                                                        <option value="engin">Engin de chantier</option>
                                                        <option value="vehicule_leger">Véhicule léger</option>
                                                        <option value="location">Location</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" name="engins[0][quantite]" placeholder="5" min="1">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" name="engins[0][tarif_journalier]" placeholder="75000" step="1000">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="engins[0][conditions]" placeholder="Avec chauffeur">
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
                                        <i class="fas fa-plus"></i> Ajouter un type d'engin
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
                                                <option value="virement">Virement bancaire</option>
                                                <option value="cheque">Chèque</option>
                                                <option value="espece">Espèces</option>
                                                <option value="mobile">Mobile Money</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="condition_reglement" class="form-label">Conditions de Règlement</label>
                                            <select class="form-select" id="condition_reglement" name="condition_reglement">
                                                <option value="">Sélectionner</option>
                                                <option value="30jours">30 jours</option>
                                                <option value="60jours">60 jours</option>
                                                <option value="comptant">Comptant</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="delai_livraison" class="form-label">Délai de Livraison</label>
                                            <input type="text" class="form-control" id="delai_livraison" name="delai_livraison"
                                                   placeholder="24h" value="{{ old('delai_livraison') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-file-contract me-2"></i>
                                            Contrat et Documents
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="contrat_type" class="form-label">Type de contrat</label>
                                            <select class="form-select" id="contrat_type" name="contrat_type">
                                                <option value="">Sélectionner</option>
                                                <option value="ponctuel">Ponctuel</option>
                                                <option value="mensuel">Mensuel</option>
                                                <option value="annuel">Annuel</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="duree_contrat" class="form-label">Durée du contrat</label>
                                            <input type="text" class="form-control" id="duree_contrat" name="duree_contrat"
                                                   placeholder="12 mois" value="{{ old('duree_contrat') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="assurance" class="form-label">Assurance</label>
                                            <select class="form-select" id="assurance" name="assurance">
                                                <option value="">Sélectionner</option>
                                                <option value="incluse">Incluse</option>
                                                <option value="fournisseur">Fournisseur</option>
                                                <option value="kenam">Kenam</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations supplémentaires -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-sticky-note me-2"></i>
                                            Informations Supplémentaires
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="references" class="form-label">Références clients</label>
                                            <textarea class="form-control" id="references" name="references" rows="2"
                                                      placeholder="Anciens clients ou projets réalisés">{{ old('references') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                                      placeholder="Informations supplémentaires sur le fournisseur externe...">{{ old('notes') }}</textarea>
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
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="spinner"></span>
                                <i class="fas fa-save me-2"></i>
                                Créer le Fournisseur Externe
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
            <select class="form-select" name="engins[${enginRowCount-1}][type]">
                <option value="">Type d'engin</option>
                <option value="camion">Camion</option>
                <option value="engin">Engin de chantier</option>
                <option value="vehicule_leger">Véhicule léger</option>
                <option value="location">Location</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control" name="engins[${enginRowCount-1}][quantite]" placeholder="5" min="1">
        </td>
        <td>
            <input type="number" class="form-control" name="engins[${enginRowCount-1}][tarif_journalier]" placeholder="75000" step="1000">
        </td>
        <td>
            <input type="text" class="form-control" name="engins[${enginRowCount-1}][conditions]" placeholder="Avec chauffeur">
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
        alert('Au moins un type d\'engin doit être spécifié');
    }
}

// Validation du formulaire
document.getElementById('fournisseurExterneForm').addEventListener('submit', function(e) {
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
