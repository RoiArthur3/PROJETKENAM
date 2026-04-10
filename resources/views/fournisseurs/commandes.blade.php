@extends('layouts.app')

@section('title', 'Achats Atelier | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice me-2"></i>Achats Atelier
            </h1>
            <p class="text-muted mb-0">Gestion des achats atelier (pieces, maintenance, consommables et prestations)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('fournisseurs.commandes.export-excel', request()->query()) }}" class="btn btn-outline-success">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
            <a href="{{ route('fournisseurs.commandes.export-pdf', request()->query()) }}" class="btn btn-outline-danger">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#searchEnginsModal">
                <i class="fas fa-search me-2"></i>Rechercher des engins
            </button>
            <a href="{{ route('fournisseurs.commandes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Commande
            </a>
        </div>
    </div>

    <div class="alert alert-info d-flex align-items-center" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <div>
            Cette rubrique centralise tous les achats de l'atelier: pieces de rechange, maintenance, consommables et commandes fournisseurs.
        </div>
    </div>

    <!-- Modal de recherche d'engins -->
    <div class="modal fade" id="searchEnginsModal" tabindex="-1" aria-labelledby="searchEnginsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchEnginsModalLabel">
                        <i class="fas fa-search me-2"></i>Rechercher des engins pour mission
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Recherche</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="enginSearchInput" placeholder="Immatriculation, marque, modèle...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type</label>
                            <select class="form-select" id="enginTypeFilter">
                                <option value="all">Tous</option>
                                <option value="vehicule">Véhicules</option>
                                <option value="equipement">Équipements</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div id="enginsSearchResults" class="engins-results">
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-search fa-2x mb-2 opacity-50"></i>
                                    <p>Entrez un terme de recherche pour trouver des engins disponibles</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="selectEnginsBtn" disabled>Selectionner</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('fournisseurs.commandes.index') }}" class="row g-2 align-items-end">
                @php($fournisseursList = $fournisseurs ?? collect())
                <div class="col-md-3">
                    <label class="form-label">Fournisseur</label>
                    <select name="fournisseur_id" class="form-select">
                        <option value="">Tous</option>
                        @foreach($fournisseursList as $fournisseur)
                            <option value="{{ $fournisseur->id }}" @selected((string) request('fournisseur_id') === (string) $fournisseur->id)>
                                {{ $fournisseur->raison_sociale }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="brouillon" @selected(request('statut') === 'brouillon')>Brouillon</option>
                        <option value="en_attente" @selected(request('statut') === 'en_attente')>En attente</option>
                        <option value="validee" @selected(request('statut') === 'validee')>Validee</option>
                        <option value="en_cours" @selected(request('statut') === 'en_cours')>En cours</option>
                        <option value="livree" @selected(request('statut') === 'livree')>Livree</option>
                        <option value="annulee" @selected(request('statut') === 'annulee')>Annulee</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Base</label>
                    <select name="base" class="form-select">
                        <option value="">Toutes</option>
                        <option value="mission" @selected(request('base') === 'mission')>Mission</option>
                        <option value="prestation" @selected(request('base') === 'prestation')>Prestation</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Periode</label>
                    <select name="periode" class="form-select">
                        <option value="">Toutes</option>
                        <option value="today" @selected(request('periode') === 'today')>Aujourd'hui</option>
                        <option value="week" @selected(request('periode') === 'week')>Semaine</option>
                        <option value="month" @selected(request('periode') === 'month')>Mois</option>
                        <option value="quarter" @selected(request('periode') === 'quarter')>Trimestre</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="recherche" class="form-control" value="{{ request('recherche') }}" placeholder="Reference, fournisseur, designation...">
                </div>
                <div class="col-12 d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('fournisseurs.commandes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Reinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Commandes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                            <div class="text-muted small">Montant: {{ number_format($stats['total_montant'] ?? 0, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Validées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['validees'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">En attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['en_attente'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Missions / Prestations Atelier</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['missions'] ?? 0 }} / {{ $stats['prestations'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-route fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des commandes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Commandes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Fournisseur</th>
                            <th>Base</th>
                            <th>Objet</th>
                            <th>Date Commande</th>
                            <th>Montant TTC</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($commandes) && $commandes->count() > 0)
                            @foreach($commandes as $commande)
                        <tr>
                            <td>
                                <div class="fw-bold text-primary">{{ $commande->reference ?? 'CMD-' . $commande->id }}</div>
                                <div class="text-muted small">{{ $commande->created_at?->format('d/m/Y') }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $commande->fournisseur->raison_sociale ?? 'N/A' }}</div>
                            </td>
                            <td>
                                @php
                                    $nature = $commande->nature_commande ?? 'autre';
                                @endphp
                                @if($nature === 'mission')
                                    <span class="badge bg-info">Mission</span>
                                @elseif($nature === 'prestation')
                                    <span class="badge bg-primary">Prestation</span>
                                @else
                                    <span class="badge bg-secondary">Autre</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $premiereLigne = $commande->lignes->first();
                                @endphp
                                <div class="fw-semibold">{{ $premiereLigne->designation ?? 'Commande generale' }}</div>
                                @if($premiereLigne && $premiereLigne->description)
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($premiereLigne->description, 60) }}</div>
                                @endif
                            </td>
                            <td>{{ $commande->date_commande?->format('d/m/Y') ?? '-' }}</td>
                            <td class="fw-bold">{{ number_format($commande->montant_ttc ?? 0, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <span class="badge bg-{{ $commande->statut === 'validee' ? 'success' : 'warning' }}">
                                    {{ ucfirst(str_replace('_', ' ', $commande->statut ?? 'inconnu')) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('fournisseurs.commandes.show', $commande->id) }}" class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-success" title="PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <div class="py-4">
                                    <i class="fas fa-file-invoice fa-4x text-muted mb-3 opacity-50"></i>
                                    <h5 class="text-muted">Aucune commande trouvée</h5>
                                    <p class="text-muted mb-3">Commencez par créer votre première commande</p>
                                    <a href="{{ route('fournisseurs.commandes.create') }}" class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i>Créer une commande
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Affichage de {{ $commandes->firstItem() ?? 0 }} à {{ $commandes->lastItem() ?? 0 }} sur {{ $commandes->total() }} résultats
                </div>
                {{ $commandes->links() }}
            </div>
        </div>
    </div>
</div>

<style>
.engins-results {
    max-height: 400px;
    overflow-y: auto;
}

.engin-item {
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.engin-item:hover {
    background-color: #f8f9fa;
    border-color: #007bff;
}

.engin-item.selected {
    background-color: #e7f3ff;
    border-color: #007bff;
}

.engin-item .engin-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.engin-item .engin-details {
    flex-grow: 1;
}

.engin-item .engin-actions {
    margin-left: 10px;
}

.engin-item .badge {
    font-size: 0.75rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('enginSearchInput');
    const typeFilter = document.getElementById('enginTypeFilter');
    const resultsContainer = document.getElementById('enginsSearchResults');
    const selectBtn = document.getElementById('selectEnginsBtn');
    const modal = document.getElementById('searchEnginsModal');
    
    let selectedEngins = [];
    
    // Recherche d'engins
    searchInput.addEventListener('input', debounce(function() {
        searchEngins();
    }, 300));
    
    typeFilter.addEventListener('change', function() {
        searchEngins();
    });
    
    function searchEngins() {
        const search = searchInput.value.trim();
        const type = typeFilter.value;
        
        if (search.length < 2) {
            resultsContainer.innerHTML = `
                <div class="text-center text-muted py-3">
                    <i class="fas fa-search fa-2x mb-2 opacity-50"></i>
                    <p>Entrez un terme de recherche pour trouver des engins disponibles</p>
                </div>
            `;
            selectBtn.disabled = true;
            return;
        }
        
        resultsContainer.innerHTML = `
            <div class="text-center py-3">
                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                <p>Recherche en cours...</p>
            </div>
        `;
        
        fetch(`/fournisseurs/commandes/search-engins?search=${encodeURIComponent(search)}&type=${type}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayEngins(data.engins);
                } else {
                    resultsContainer.innerHTML = `
                        <div class="text-center text-danger py-3">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p>${data.message}</p>
                        </div>
                    `;
                }
                selectBtn.disabled = true;
            })
            .catch(error => {
                console.error('Erreur:', error);
                resultsContainer.innerHTML = `
                    <div class="text-center text-danger py-3">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Erreur lors de la recherche</p>
                    </div>
                `;
                selectBtn.disabled = true;
            });
    }
    
    function displayEngins(engins) {
        if (engins.length === 0) {
            resultsContainer.innerHTML = `
                <div class="text-center text-muted py-3">
                    <i class="fas fa-search fa-2x mb-2 opacity-50"></i>
                    <p>Aucun engin trouvé pour cette recherche</p>
                </div>
            `;
            selectBtn.disabled = true;
            return;
        }
        
        let html = '';
        engins.forEach(engin => {
            const statusBadge = engin.statut === 'disponible' ? 'success' : 'warning';
            const statusText = engin.statut === 'disponible' ? 'Disponible' : 'En stock';
            
            html += `
                <div class="engin-item" data-engin-id="${engin.id}">
                    <div class="engin-info">
                        <div class="engin-details">
                            <div class="fw-bold">${engin.immatriculation || 'N/A'}</div>
                            <div class="text-muted small">${engin.marque} ${engin.modele || ''}</div>
                            <div class="text-muted small">Série: ${engin.numero_serie || 'N/A'}</div>
                        </div>
                        <div class="engin-actions">
                            <span class="badge bg-${statusBadge}">${statusText}</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        resultsContainer.innerHTML = html;
        
        // Ajouter les écouteurs de sélection
        document.querySelectorAll('.engin-item').forEach(item => {
            item.addEventListener('click', function() {
                toggleEnginSelection(this);
            });
        });
        
        selectBtn.disabled = selectedEngins.length === 0;
    }
    
    function toggleEnginSelection(element) {
        const enginId = element.dataset.enginId;
        
        element.classList.toggle('selected');
        
        if (element.classList.contains('selected')) {
            selectedEngins.push(enginId);
        } else {
            selectedEngins = selectedEngins.filter(id => id !== enginId);
        }
        
        selectBtn.disabled = selectedEngins.length === 0;
    }
    
    // Bouton de sélection
    selectBtn.addEventListener('click', function() {
        if (selectedEngins.length > 0) {
            modal.hide();
            
            // Afficher une notification
            const notification = document.createElement('div');
            notification.className = 'alert alert-success alert-dismissible fade show';
            notification.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                ${selectedEngins.length} engin(s) sélectionné(s) pour la mission
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            document.querySelector('.container-fluid').insertAdjacentHTML('afterbegin', notification.outerHTML);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    });
    
    // Réinitialiser lors de l'ouverture du modal
    modal.addEventListener('show.bs.modal', function() {
        searchInput.value = '';
        typeFilter.value = 'all';
        selectedEngins = [];
        selectBtn.disabled = true;
        resultsContainer.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fas fa-search fa-2x mb-2 opacity-50"></i>
                <p>Entrez un terme de recherche pour trouver des engins disponibles</p>
            </div>
        `;
    });
    
    // Fonction de debounce
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                timeout = null;
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});
</script>
@endsection
