@extends('layouts.app')

@section('title', 'Pointages d\'Engins | KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs me-2 text-info"></i>Pointages d'Engins
            </h1>
            <p class="text-muted mb-0">Gestion des pointages et suivi des coûts des engins</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pointageEnginModal">
                <i class="fas fa-plus me-2"></i>Nouveau Pointage
            </button>
        </div>
    </div>

    <!-- Filtres et statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Pointages du jour</h6>
                            <h3 class="mb-0">{{ $todayPointages ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Heures totales</h6>
                            <h3 class="mb-0">{{ number_format($totalHours ?? 0, 1) }}h</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Coût total</h6>
                            <h3 class="mb-0">{{ number_format($totalCost ?? 0, 0) }} FCFA</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Marge totale</h6>
                            <h3 class="mb-0">{{ number_format($totalMargin ?? 0, 0) }} FCFA</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtres
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('materiel.cost-control.engin.pointages.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Projet</label>
                        <select name="projet_id" class="form-select">
                            <option value="">Tous les projets</option>
                            @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ request('projet_id') == $projet->id ? 'selected' : '' }}>
                                    {{ $projet->titre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Engin</label>
                        <select name="vehicle_id" class="form-select">
                            <option value="">Tous les engins</option>
                            @foreach($vehicles ?? [] as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->immatriculation }} - {{ $vehicle->marque }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date début</label>
                        <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date fin</label>
                        <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('materiel.cost-control.engin.pointages.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des pointages -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>Liste des pointages
            </h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-success" onclick="exportExcel()">
                    <i class="fas fa-file-excel me-1"></i>Excel
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="printList()">
                    <i class="fas fa-print me-1"></i>Imprimer
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="pointagesTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Projet</th>
                            <th>Engin</th>
                            <th>Fournisseur</th>
                            <th>Horaire</th>
                            <th>Durée</th>
                            <th>Coût</th>
                            <th>Prix client</th>
                            <th>Marge</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pointages ?? [] as $pointage)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($pointage->date_pointage)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $pointage->projet->titre ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $pointage->vehicle->immatriculation ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $pointage->vehicle->marque ?? '' }} {{ $pointage->vehicle->modele ?? '' }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($pointage->fournisseur_id == 'kenam')
                                        <span class="badge bg-primary">Kenam</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $pointage->fournisseur->nom ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        {{ $pointage->heure_debut ?? '--:--' }} - {{ $pointage->heure_fin ?? '--:--' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ number_format($pointage->quantity ?? 0, 1) }}h</span>
                                </td>
                                <td>
                                    <span class="text-danger fw-bold">{{ number_format($pointage->supplier_unit_cost * $pointage->quantity ?? 0, 0) }} FCFA</span>
                                </td>
                                <td>
                                    <span class="text-success fw-bold">{{ number_format($pointage->client_unit_price * $pointage->quantity ?? 0, 0) }} FCFA</span>
                                </td>
                                <td>
                                    <span class="text-info fw-bold">
                                        {{ number_format(($pointage->client_unit_price - $pointage->supplier_unit_cost) * $pointage->quantity ?? 0, 0) }} FCFA
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $pointage->statut == 'validé' ? 'success' : 'warning' }}">
                                        {{ $pointage->statut ?? 'En cours' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" onclick="viewPointage({{ $pointage->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-warning" onclick="editPointage({{ $pointage->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="deletePointage({{ $pointage->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>Aucun pointage trouvé</p>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pointageEnginModal">
                                            <i class="fas fa-plus me-2"></i>Créer le premier pointage
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if(isset($pointages) && $pointages->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $pointages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Inclure le modal de pointage -->
@include('materiel.cost-control.engin-pointage-modal')

<style>
.badge {
    font-size: 0.75rem;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    border-top: none;
    background-color: #f8f9fa;
}

.table td {
    font-size: 0.875rem;
    vertical-align: middle;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
</style>

<script>
// Fonctions pour les actions
function viewPointage(id) {
    // Ouvrir le modal en mode consultation
    window.location.href = `/materiel/cost-control/engin/pointages/${id}`;
}

function editPointage(id) {
    // Ouvrir le modal en mode édition
    window.location.href = `/materiel/cost-control/engin/pointages/${id}/edit`;
}

function deletePointage(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce pointage ?')) {
        // Soumettre la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/materiel/cost-control/engin/pointages/${id}`;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function exportExcel() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = `${window.location.pathname}?${params.toString()}`;
}

function printList() {
    window.print();
}

// Initialisation du DataTable si disponible
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#pointagesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr.json'
            },
            pageLength: 25,
            order: [[0, 'desc']]
        });
    }
});

// Rafraîchir la liste après la soumission du modal
document.getElementById('pointageModalForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Afficher le chargement
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';
    
    // Soumettre le formulaire
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('pointageEnginModal'));
            modal.hide();
            
            // Afficher le message de succès
            showAlert('success', data.message || 'Pointage enregistré avec succès');
            
            // Recharger la page après un court délai
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('danger', data.message || 'Erreur lors de l\'enregistrement');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur lors de l\'enregistrement');
    })
    .finally(() => {
        // Restaurer le bouton
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endsection
