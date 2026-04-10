@extends('layouts.authenticated')
@section('header-title', 'Parc Automobile')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0"><i class="fas fa-car me-2 text-primary"></i>Gestion du Parc Automobile</h1>
            <a href="{{ route('fleet.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un Véhicule
            </a>
        </div>
    </div>

    <!-- Onglets -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs" id="parcTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="vehicules-tab" data-bs-toggle="tab" data-bs-target="#vehicules" type="button" role="tab" aria-controls="vehicules" aria-selected="true">
                        <i class="fas fa-car me-2"></i>Véhicules
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="recherche-tab" data-bs-toggle="tab" data-bs-target="#recherche" type="button" role="tab" aria-controls="recherche" aria-selected="false">
                        <i class="fas fa-search me-2"></i>Recherche d'Engins
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Contenu des onglets -->
    <div class="tab-content" id="parcTabContent">
        <!-- Onglet Véhicules -->
        <div class="tab-pane fade show active" id="vehicules" role="tabpanel" aria-labelledby="vehicules-tab">
            <!-- Filtres et Recherche -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <select class="form-select" id="typeFilter">
                        <option value="">Tous les Types</option>
                        <option value="voiture">Voiture</option>
                        <option value="camion">Camion</option>
                        <option value="moto">Moto</option>
                        <option value="utilitaire">Utilitaire</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="disponibiliteFilter">
                        <option value="">Tous les Statuts</option>
                        <option value="1">Disponible</option>
                        <option value="0">Indisponible</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="serviceFilter">
                        <option value="">Tous les Services</option>
                        <option value="Logistique">Logistique</option>
                        <option value="Entretien">Entretien</option>
                        <option value="Comptabilité">Comptabilité</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-success" onclick="filterVehicles()">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </div>

            <!-- Tableau des Véhicules -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Liste des Véhicules</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Immatriculation</th>
                                            <th>Marque/Modèle</th>
                                            <th>Type</th>
                                            <th>État</th>
                                            <th>Disponibilité</th>
                                            <th>Kilométrage</th>
                                            <th>Service Assigné</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="vehicleTable">
                                        <tr>
                                            <td>AB-123-CD</td>
                                            <td>Toyota Camry</td>
                                            <td>Voiture</td>
                                            <td><span class="badge bg-success">Bon</span></td>
                                            <td><span class="badge bg-success">Disponible</span></td>
                                            <td>45 000 km</td>
                                            <td>Logistique</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('parc.vehicules.show', 'AB-123-CD') }}" class="btn btn-sm btn-info">Voir</a>
                                                    <a href="{{ route('parc.vehicules.edit', 'AB-123-CD') }}" class="btn btn-sm btn-warning">Modifier</a>
                                                    <button class="btn btn-sm btn-danger" onclick="supprimerVehicule('AB-123-CD')">Supprimer</button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>XY-456-ZW</td>
                                            <td>Renault Truck</td>
                                            <td>Camion</td>
                                            <td><span class="badge bg-warning">Moyen</span></td>
                                            <td><span class="badge bg-danger">Indisponible</span></td>
                                            <td>120 000 km</td>
                                            <td>Entretien</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('parc.vehicules.show', 'XY-456-ZW') }}" class="btn btn-sm btn-info">Voir</a>
                                                    <a href="{{ route('parc.vehicules.edit', 'XY-456-ZW') }}" class="btn btn-sm btn-warning">Modifier</a>
                                                    <button class="btn btn-sm btn-danger" onclick="supprimerVehicule('XY-456-ZW')">Supprimer</button>
                                                </div>
                                            </td>
                                        </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Recherche d'Engins -->
        <div class="tab-pane fade" id="recherche" role="tabpanel" aria-labelledby="recherche-tab">
            <!-- Filtres de recherche -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">Recherche</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="enginSearchInput" placeholder="Immatriculation, marque, modèle, numéro de série...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type d'engin</label>
                    <select class="form-select" id="enginTypeFilter">
                        <option value="all">Tous</option>
                        <option value="vehicule">Véhicules</option>
                        <option value="equipement">Équipements</option>
                        <option value="camion">Camions</option>
                        <option value="moto">Motos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select class="form-select" id="enginStatusFilter">
                        <option value="">Tous</option>
                        <option value="disponible">Disponible</option>
                        <option value="en_stock">En stock</option>
                        <option value="en_mission">En mission</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100" onclick="searchEngins()">
                        <i class="fas fa-search me-2"></i>Recherchercher
                    </button>
                </div>
            </div>

            <!-- Résultats de recherche -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-search me-2"></i>Résultats de Recherche
                                <span class="badge bg-secondary ms-2" id="resultCount">0</span>
                            </h6>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-success" onclick="exportResults()">
                                    <i class="fas fa-file-excel me-1"></i>Exporter
                                </button>
                                <button class="btn btn-outline-info" onclick="printResults()">
                                    <i class="fas fa-print me-1"></i>Imprimer
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="enginsSearchResults">
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-search fa-4x mb-3 opacity-50"></i>
                                    <h5 class="text-muted">Rechercher des engins</h5>
                                    <p class="text-muted">Utilisez les filtres ci-dessus pour trouver des engins disponibles</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sélection multiple -->
            <div class="row mt-3" id="selectionPanel" style="display: none;">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="m-0">
                                <i class="fas fa-check-square me-2"></i>
                                <span id="selectedCount">0</span> engin(s) sélectionné(s)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2">
                                <button class="btn btn-success" onclick="assignToMission()">
                                    <i class="fas fa-tasks me-2"></i>Assigner à une mission
                                </button>
                                <button class="btn btn-info" onclick="createMaintenance()">
                                    <i class="fas fa-wrench me-2"></i>Planifier maintenance
                                </button>
                                <button class="btn btn-warning" onclick="exportSelection()">
                                    <i class="fas fa-download me-2"></i>Exporter la sélection
                                </button>
                                <button class="btn btn-outline-secondary" onclick="clearSelection()">
                                    <i class="fas fa-times me-2"></i>Vider la sélection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé du Parc -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Véhicules</h5>
                    <h2>25</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Disponibles</h5>
                    <h2>20</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">En Maintenance</h5>
                    <h2>3</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Kilométrage Moyen</h5>
                    <h2>85 000 km</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales pour la recherche d'engins
let selectedEngins = [];
let searchResults = [];

function filterVehicles() {
    const type = document.getElementById('typeFilter').value;
    const disponibilite = document.getElementById('disponibiliteFilter').value;
    // Logique pour filtrer les véhicules
    console.log('Filtrage:', type, disponibilite);
}

function searchEngins() {
    const search = document.getElementById('enginSearchInput').value.trim();
    const type = document.getElementById('enginTypeFilter').value;
    const status = document.getElementById('enginStatusFilter').value;

    if (search.length < 2) {
        showEmptyResults();
        return;
    }

    // Afficher l'indicateur de chargement
    const resultsContainer = document.getElementById('enginsSearchResults');
    resultsContainer.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <p class="mt-2">Recherche en cours...</p>
        </div>
    `;

    // Simuler une recherche (remplacer par un appel AJAX réel)
    setTimeout(() => {
        // Simuler des résultats de recherche
        searchResults = [
            {
                id: 1,
                immatriculation: 'AB-123-CD',
                marque: 'Toyota',
                modele: 'Camry',
                type: 'vehicule',
                statut: 'disponible',
                kilometrage: 45000,
                annee: 2020,
                couleur: 'Noir'
            },
            {
                id: 2,
                immatriculation: 'XY-456-ZW',
                marque: 'Renault',
                modele: 'Truck',
                type: 'camion',
                statut: 'en_mission',
                kilometrage: 120000,
                annee: 2018,
                couleur: 'Bleu'
            },
            {
                id: 3,
                immatriculation: 'EF-789-GH',
                marque: 'Honda',
                modele: 'CBR',
                type: 'moto',
                statut: 'disponible',
                kilometrage: 25000,
                annee: 2022,
                couleur: 'Rouge'
            }
        ];

        displaySearchResults();
    }, 1000);
}

function displaySearchResults() {
    const resultsContainer = document.getElementById('enginsSearchResults');
    const resultCount = document.getElementById('resultCount');

    resultCount.textContent = searchResults.length;

    if (searchResults.length === 0) {
        showNoResults();
        return;
    }

    let html = '<div class="row">';

    searchResults.forEach(engin => {
        const statusBadge = getStatusBadge(engin.statut);
        const typeIcon = getTypeIcon(engin.type);

        html += `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 engin-card ${selectedEngins.includes(engin.id) ? 'selected' : ''}" data-engin-id="${engin.id}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="engin-info">
                                <h6 class="card-title mb-1">${engin.immatriculation}</h6>
                                <p class="text-muted mb-1">${engin.marque} ${engin.modele}</p>
                                <div class="engin-meta">
                                    <span class="badge bg-light text-dark me-1">${typeIcon} ${engin.type}</span>
                                    <span class="badge ${statusBadge.class}">${statusBadge.text}</span>
                                </div>
                            </div>
                            <div class="engin-checkbox">
                                <input type="checkbox" class="form-check-input" value="${engin.id}" onchange="toggleEnginSelection(${engin.id})">
                            </div>
                        </div>
                        <div class="engin-details">
                            <div class="row text-sm">
                                <div class="col-6">
                                    <strong>Kilométrage:</strong> ${engin.kilometrage.toLocaleString()} km
                                </div>
                                <div class="col-6">
                                    <strong>Année:</strong> ${engin.annee}
                                </div>
                                <div class="col-6">
                                    <strong>Couleur:</strong>
                                    <span class="badge bg-secondary">${engin.couleur}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Actions:</strong>
                                    <button class="btn btn-sm btn-outline-primary ms-1" onclick="viewEnginDetails(${engin.id})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    resultsContainer.innerHTML = html;
}

function showEmptyResults() {
    const resultsContainer = document.getElementById('enginsSearchResults');
    const resultCount = document.getElementById('resultCount');

    resultCount.textContent = '0';
    resultsContainer.innerHTML = `
        <div class="text-center text-muted py-5">
            <i class="fas fa-search fa-4x mb-3 opacity-50"></i>
            <h5 class="text-muted">Rechercher des engins</h5>
            <p class="text-muted">Entrez au moins 2 caractères pour lancer la recherche</p>
        </div>
    `;
}

function showNoResults() {
    const resultsContainer = document.getElementById('enginsSearchResults');
    const resultCount = document.getElementById('resultCount');

    resultCount.textContent = '0';
    resultsContainer.innerHTML = `
        <div class="text-center text-muted py-5">
            <i class="fas fa-search fa-4x mb-3 opacity-50"></i>
            <h5 class="text-muted">Aucun résultat trouvé</h5>
            <p class="text-muted">Essayez avec d'autres critères de recherche</p>
        </div>
    `;
}

function getStatusBadge(statut) {
    const badges = {
        'disponible': { class: 'bg-success', text: 'Disponible' },
        'en_stock': { class: 'bg-info', text: 'En stock' },
        'en_mission': { class: 'bg-warning', text: 'En mission' },
        'maintenance': { class: 'bg-danger', text: 'Maintenance' }
    };
    return badges[statut] || badges['disponible'];
}

function getTypeIcon(type) {
    const icons = {
        'vehicule': 'fa-car',
        'camion': 'fa-truck',
        'moto': 'fa-motorcycle',
        'equipement': 'fa-tools'
    };
    return icons[type] || 'fa-cog';
}

function toggleEnginSelection(enginId) {
    const index = selectedEngins.indexOf(enginId);
    const enginCard = document.querySelector(`[data-engin-id="${enginId}"]`);
    const checkbox = document.querySelector(`input[value="${enginId}"]`);

    if (index > -1) {
        selectedEngins.splice(index, 1);
        enginCard.classList.remove('selected');
        checkbox.checked = false;
    } else {
        selectedEngins.push(enginId);
        enginCard.classList.add('selected');
        checkbox.checked = true;
    }

    updateSelectionPanel();
}

function updateSelectionPanel() {
    const selectionPanel = document.getElementById('selectionPanel');
    const selectedCount = document.getElementById('selectedCount');

    if (selectedEngins.length > 0) {
        selectionPanel.style.display = 'block';
        selectedCount.textContent = selectedEngins.length;
    } else {
        selectionPanel.style.display = 'none';
    }
}

function assignToMission() {
    if (selectedEngins.length === 0) {
        alert('Veuillez sélectionner au moins un engin');
        return;
    }

    // Logique pour assigner les engins à une mission
    console.log('Assigner à mission:', selectedEngins);
    alert(`${selectedEngins.length} engin(s) assigné(s) à une mission`);
}

function createMaintenance() {
    if (selectedEngins.length === 0) {
        alert('Veuillez sélectionner au moins un engin');
        return;
    }

    // Logique pour créer une maintenance
    console.log('Créer maintenance pour:', selectedEngins);
    alert(`Maintenance planifiée pour ${selectedEngins.length} engin(s)`);
}

function exportSelection() {
    if (selectedEngins.length === 0) {
        alert('Veuillez sélectionner au moins un engin');
        return;
    }

    // Logique pour exporter la sélection
    console.log('Exporter la sélection:', selectedEngins);
    alert(`Export de ${selectedEngins.length} engin(s)`);
}

function clearSelection() {
    selectedEngins = [];
    document.querySelectorAll('.engin-card.selected').forEach(card => {
        card.classList.remove('selected');
    });
    document.querySelectorAll('.engin-checkbox input:checked').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectionPanel();
}

function viewEnginDetails(enginId) {
    // Logique pour voir les détails d'un engin
    console.log('Voir détails de l\'engin:', enginId);
    alert(`Détails de l'engin #${enginId}`);
}

function exportResults() {
    if (searchResults.length === 0) {
        alert('Aucun résultat à exporter');
        return;
    }

    // Logique pour exporter les résultats
    console.log('Exporter les résultats:', searchResults);
    alert(`Export de ${searchResults.length} résultat(s)`);
}

function printResults() {
    if (searchResults.length === 0) {
        alert('Aucun résultat à imprimer');
        return;
    }

    // Logique pour imprimer les résultats
    console.log('Imprimer les résultats:', searchResults);
    window.print();
}
</script>

<style>
.engin-card {
    transition: all 0.2s ease;
    cursor: pointer;
    border: 2px solid transparent;
}

.engin-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.engin-card.selected {
    border-color: #007bff;
    background-color: #e7f3ff;
}

.engin-checkbox {
    position: absolute;
    top: 10px;
    right: 10px;
}

.engin-info {
    flex: 1;
}

.engin-meta {
    display: flex;
    gap: 0.25rem;
}

.engin-details {
    border-top: 1px solid #dee2e6;
    padding-top: 0.5rem;
    margin-top: 0.5rem;
}

.spinner-border {
    width: 2rem;
    height: 2rem;
    border: 0.25em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-border 0.75s linear infinite;
}

@keyframes spinner-border {
    0% {
        border-top-color: currentColor;
    }
    100% {
        border-top-color: transparent;
    }
}
</style>
@endsection
