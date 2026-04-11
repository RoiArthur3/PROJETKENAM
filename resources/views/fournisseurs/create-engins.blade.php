@extends('layouts.app')

@section('title', 'Ajouter un Fournisseur d\'Engins - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Ajouter un Fournisseur d'Engins</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('fournisseurs.engins.kenam') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour aux Engins Kenam
            </a>
            <a href="{{ route('fournisseurs.dashboard') }}" class="btn btn-outline-info">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Fournisseurs
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('fournisseurs.store') }}" method="POST" id="fournisseurEnginsForm">
                        @csrf

                        <!-- Informations principales -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-building me-2"></i>
                                            Type et Spécialité
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="type_fournisseur" class="form-label">Type de fournisseur</label>
                                            <select class="form-select" id="type_fournisseur" name="type_fournisseur" required>
                                                <option value="">Sélectionner le type</option>
                                                <option value="engins_interne">Interne - Kenam</option>
                                                <option value="engins_externe">Externe - Prestataire</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
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
                                        <div class="mb-3">
                                            <label for="categorie_id" class="form-label">Catégorie</label>
                                            <select class="form-select" id="categorie_id" name="categorie_id">
                                                <option value="">Sélectionner une catégorie</option>
                                                @forelse(($categories ?? []) as $categorie)
                                                    <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                                        {{ $categorie->nom }}
                                                    </option>
                                                @empty
                                                    <option value="1">Fournisseur d'engins</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Informations Juridiques
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="raison_sociale" class="form-label">Raison Sociale</label>
                                            <input type="text" class="form-control" id="raison_sociale" name="raison_sociale"
                                                   placeholder="Ex: TRANSPORT SA" value="{{ old('raison_sociale') }}" required>
                                            @error('raison_sociale')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="forme_juridique" class="form-label">Forme Juridique</label>
                                            <select class="form-select" id="forme_juridique" name="forme_juridique">
                                                <option value="">Sélectionner</option>
                                                <option value="SA">Société Anonyme (SA)</option>
                                                <option value="SARL">Société à Responsabilité Limitée (SARL)</option>
                                                <option value="SNC">Société en Nom Collectif (SNC)</option>
                                                <option value="SCS">Société en Commandite Simple (SCS)</option>
                                                <option value="SCA">Société en Commandite par Actions (SCA)</option>
                                                <option value="GIE">Groupement d'Intérêt Économique (GIE)</option>
                                                <option value="Personne_physique">Personne Physique</option>
                                                <option value="Autre">Autre</option>
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="cc" class="form-label">N° Centre de Formalités</label>
                                                <input type="text" class="form-control" id="cc" name="cc"
                                                       placeholder="CI-ABJ-2023-001234" value="{{ old('cc') }}">
                                                <small class="text-muted">Numéro d'enregistrement au Centre de Formalités</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="ncc" class="form-label">N° Contribuable</label>
                                                <input type="text" class="form-control" id="ncc" name="ncc"
                                                       placeholder="1234567890123" value="{{ old('ncc') }}">
                                                <small class="text-muted">Numéro d'Identification Fiscale</small>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="rccm" class="form-label">N° RCCM</label>
                                                <input type="text" class="form-control" id="rccm" name="rccm"
                                                       placeholder="ABJ-2023-B-12345" value="{{ old('rccm') }}">
                                                <small class="text-muted">Registre du Commerce et du Crédit Mobilier</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="id_national" class="form-label">N° Identification Nationale</label>
                                                <input type="text" class="form-control" id="id_national" name="id_national"
                                                       placeholder="CI0001234567890" value="{{ old('id_national') }}">
                                                <small class="text-muted">Optionnel pour les sociétés</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Coordonnées -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-phone me-2"></i>
                                            Coordonnées et Adresse
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                       placeholder="contact@fournisseur.com" value="{{ old('email') }}">
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
                                                       placeholder="123 Rue de la République" value="{{ old('adresse') }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="code_postal" class="form-label">Code Postal</label>
                                                <input type="text" class="form-control" id="code_postal" name="code_postal"
                                                       placeholder="12345" value="{{ old('code_postal') }}">
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
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Engins associés -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-truck me-2"></i>
                                            Engins Associés
                                        </h6>
                                        <button type="button" class="btn btn-sm btn-success" onclick="addEnginRow()">
                                            <i class="fas fa-plus"></i> Ajouter un engin
                                        </button>
                                    </div>
                                    <div class="card-body">
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
                                        </div>
                                    </div>
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
                                            <i class="fas fa-sticky-note me-2"></i>
                                            Informations Supplémentaires
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="4"
                                                      placeholder="Informations supplémentaires sur le fournisseur...">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('fournisseurs.engins.kenam') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="spinner"></span>
                                <i class="fas fa-save me-2"></i>
                                Créer le Fournisseur d'Engins
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.card-header h6 {
    color: #495057;
    font-weight: 600;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

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
document.getElementById('fournisseurEnginsForm').addEventListener('submit', function(e) {
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

    // Animation de soumission
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0.7';
        }, index * 100);
    });
});

// Auto-formatage des champs
document.getElementById('siret').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '');
    if (value.length > 0) {
        value = value.match(/.{1,3}/g).join(' ');
    }
    e.target.value = value;
});

document.getElementById('telephone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/[^\d+]/g, '');
    e.target.value = value;
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
@endsection
