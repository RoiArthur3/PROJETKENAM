@extends('layouts.app')

@section('title', 'Rapport Financier | KENAM SERVICES')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line me-2 text-success"></i>Rapport Financier
            </h1>
            <p class="text-muted mb-0">Analyse financière du projet et facturation</p>
        </div>
    </div>

    <!-- Informations du projet -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-folder me-2"></i>{{ $projet->titre }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Client:</strong> {{ $projet->client ? $projet->client->raison_sociale : 'N/A' }}</p>
                    <p><strong>Responsable:</strong> {{ $projet->user ? $projet->user->name : 'N/A' }}</p>
                    <p><strong>Date début:</strong> {{ $projet->date_debut ? \Carbon\Carbon::parse($projet->date_debut)->format('d/m/Y') : 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Date fin:</strong> {{ $projet->date_fin ? \Carbon\Carbon::parse($projet->date_fin)->format('d/m/Y') : 'N/A' }}</p>
                    <p><strong>Coût estimatif:</strong> {{ number_format($projet->cout_estimatif ?? 0, 0, ' ', ' ') }} FCFA</p>
                    <p><strong>Montant à facturer:</strong> {{ number_format($projet->montant_facturer ?? 0, 0, ' ', ' ') }} FCFA</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et résumé - DESIGN REVISÉ -->
    <div class="row mb-4">
        <!-- Colonne de gauche : Filtres -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filtres de recherche
                    </h5>
                </div>
                <div class="card-body">
                    <form id="filtresForm" class="row g-3">
                        <div class="col-md-6">
                            <label for="date_debut" class="form-label">Date début</label>
                            <input type="date" class="form-control" id="date_debut" name="date_debut">
                        </div>
                        <div class="col-md-6">
                            <label for="date_fin" class="form-label">Date fin</label>
                            <input type="date" class="form-control" id="date_fin" name="date_fin">
                        </div>
                        <div class="col-md-6">
                            <label for="engin_id" class="form-label">Engin</label>
                            <select class="form-select" id="engin_id" name="engin_id">
                                <option value="">Tous les engins</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="type_pointage" class="form-label">Type de pointage</label>
                            <select class="form-select" id="type_pointage" name="type_pointage">
                                <option value="">Tous les types</option>
                                <option value="heure">Par heure</option>
                                <option value="jour">Par jour</option>
                                <option value="voyage">Par voyage</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Appliquer les filtres
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetFiltres()">
                                    <i class="fas fa-redo me-1"></i>Réinitialiser
                                </button>
                                <button type="button" class="btn btn-success" onclick="genererFacture()">
                                    <i class="fas fa-file-invoice me-1"></i>Générer la facture
                                </button>
                                <a href="{{ route('projets.show', $projet->id) }}" class="btn btn-outline-dark ms-auto">
                                    <i class="fas fa-arrow-left me-1"></i>Retour au projet
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Colonne de droite : Résumé du pointage -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-chart-pie me-2"></i>
                        Résumé du pointage
                        <span class="badge bg-light text-success ms-auto" id="summary_count">0</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                            <div>
                                <i class="fas fa-clock text-primary me-2"></i>
                                <span class="text-muted">Total heures/jours</span>
                            </div>
                            <strong class="text-primary fs-6" id="summary_unites">0</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                            <div>
                                <i class="fas fa-money-bill-wave text-danger me-2"></i>
                                <span class="text-muted">Coût fournisseur</span>
                            </div>
                            <strong class="text-danger fs-6" id="summary_fournisseur">0 FCFA</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                            <div>
                                <i class="fas fa-hand-holding-usd text-info me-2"></i>
                                <span class="text-muted">Montant client</span>
                            </div>
                            <strong class="text-info fs-6" id="summary_client">0 FCFA</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2 bg-light">
                            <div>
                                <i class="fas fa-chart-line text-success me-2"></i>
                                <strong class="text-success">Marge bénéficiaire</strong>
                            </div>
                            <strong class="text-success fs-5" id="summary_marge">0 FCFA</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Tableau des pointages -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Détail des pointages
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="pointagesTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Engin</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Prix unitaire fournisseur</th>
                            <th>Prix unitaire client</th>
                            <th>Coût total fournisseur</th>
                            <th>Montant total client</th>
                            <th>Marge</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pointagesBody">
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                                <p class="text-muted">Chargement des données...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
let projetId = {{ $projet->id }};
let pointagesData = [];

// Charger les données initiales
document.addEventListener('DOMContentLoaded', function() {
    chargerDonnees();
    chargerEngins();
});

// Charger les engins du projet
function chargerEngins() {
    fetch(`/projets/api/${projetId}/engins`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const enginSelect = document.getElementById('engin_id');
                data.engins.forEach(engin => {
                    const option = document.createElement('option');
                    option.value = engin.id;
                    option.textContent = `${engin.immatriculation} - ${engin.marque} ${engin.modele}`;
                    enginSelect.appendChild(option);
                });
            }
        });
}

// Charger les données du rapport
function chargerDonnees() {
    const formData = new FormData(document.getElementById('filtresForm'));
    const params = new URLSearchParams(formData);

    fetch(`/projets/api/${projetId}/rapport-financier/data?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                pointagesData = data.pointages;
                afficherPointages(data.pointages);
                mettreAJourTotaux(data.totaux);
            } else {
                console.error('Erreur:', data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
}

// Afficher les pointages dans le tableau
function afficherPointages(pointages) {
    const tbody = document.getElementById('pointagesBody');

    if (pointages.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-4">
                    <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                    <p class="text-muted">Aucun pointage trouvé pour les critères sélectionnés.</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = pointages.map(pointage => `
        <tr>
            <td>${formatDate(pointage.date_pointage)}</td>
            <td>${pointage.vehicule ? pointage.vehicule.immatriculation : 'N/A'}</td>
            <td><span class="badge bg-info">${pointage.unit_type || 'N/A'}</span></td>
            <td>${pointage.quantity || 0}</td>
            <td>${formatPrice(pointage.supplier_unit_cost || 0)}</td>
            <td>${formatPrice(pointage.client_unit_price || 0)}</td>
            <td>${formatPrice(pointage.total_supplier_cost || 0)}</td>
            <td>${formatPrice(pointage.total_client_amount || 0)}</td>
            <td><span class="badge ${pointage.marge >= 0 ? 'bg-success' : 'bg-danger'}">${formatPrice(pointage.marge || 0)}</span></td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="voirPointage(${pointage.id})">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Mettre à jour les totaux
function mettreAJourTotaux(totaux) {
    // Mettre à jour le résumé en haut à droite
    document.getElementById('summary_unites').textContent = totaux.total_unites || 0;
    document.getElementById('summary_fournisseur').textContent = formatPrice(totaux.total_fournisseur || 0);
    document.getElementById('summary_client').textContent = formatPrice(totaux.total_client || 0);
    document.getElementById('summary_marge').textContent = formatPrice(totaux.total_marge || 0);

    // Mettre à jour le compteur de pointages
    const countElement = document.getElementById('summary_count');
    if (countElement) {
        countElement.textContent = totaux.nombre_pointages || 0;
    }

    // Mettre à jour les anciens IDs s'ils existent encore
    const oldUnites = document.getElementById('total_unites');
    const oldFournisseur = document.getElementById('total_fournisseur');
    const oldClient = document.getElementById('total_client');
    const oldMarge = document.getElementById('total_marge');

    if (oldUnites) oldUnites.textContent = totaux.total_unites || 0;
    if (oldFournisseur) oldFournisseur.textContent = formatPrice(totaux.total_fournisseur || 0);
    if (oldClient) oldClient.textContent = formatPrice(totaux.total_client || 0);
    if (oldMarge) oldMarge.textContent = formatPrice(totaux.total_marge || 0);
}

// Formater la date
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

// Formater le prix
function formatPrice(value) {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value) + ' FCFA';
}

// Réinitialiser les filtres
function resetFiltres() {
    document.getElementById('filtresForm').reset();
    chargerDonnees();
}

// Générer la facture
function genererFacture() {
    const formData = new FormData(document.getElementById('filtresForm'));
    const params = new URLSearchParams(formData);

    window.open(`/projets/${projetId}/rapport-financier/facture?${params}`, '_blank');
}

// Voir un pointage
function voirPointage(pointageId) {
    window.open(`/materiel/cost-control/engin/pointages/${pointageId}`, '_blank');
}

// Soumission du formulaire de filtres
document.getElementById('filtresForm').addEventListener('submit', function(e) {
    e.preventDefault();
    chargerDonnees();
});
</script>
@endsection
