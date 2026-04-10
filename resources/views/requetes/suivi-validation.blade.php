@extends('layouts.app')

@section('title', 'Suivi & Validation - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-eye me-2 text-success"></i>Suivi & Validation
            </h1>
            <p class="text-muted mb-0">Consultez et suivez vos requêtes envoyées</p>
        </div>
        <div>
            <a href="/agent/requetes/create" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Nouvelle Requête
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-select" id="statut">
                                <option value="">Tous les statuts</option>
                                <option value="en_attente">En attente</option>
                                <option value="en_cours">En cours</option>
                                <option value="validee">Validée</option>
                                <option value="refusee">Refusée</option>
                                <option value="cloturee">Clôturée</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="priorite" class="form-label">Priorité</label>
                            <select class="form-select" id="priorite">
                                <option value="">Toutes les priorités</option>
                                <option value="basse">Basse</option>
                                <option value="normale">Normale</option>
                                <option value="haute">Haute</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_debut" class="form-label">Date début</label>
                            <input type="date" class="form-control" id="date_debut">
                        </div>
                        <div class="col-md-3">
                            <label for="date_fin" class="form-label">Date fin</label>
                            <input type="date" class="form-control" id="date_fin">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-primary" onclick="filtrerRequetes()">
                                <i class="fas fa-search me-2"></i>Filtrer
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="reinitialiserFiltres()">
                                <i class="fas fa-redo me-2"></i>Réinitialiser
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des requêtes -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Mes Requêtes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="requetesTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Référence</th>
                                    <th>Titre</th>
                                    <th>Service Destination</th>
                                    <th>Date</th>
                                    <th>Priorité</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($requetes) && $requetes->count() > 0)
                                    @foreach($requetes as $requete)
                                        <tr>
                                            <td><span class="badge bg-primary">REQ-{{ str_pad($requete->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                                            <td>{{ $requete->titre ?? 'Requête sans titre' }}</td>
                                            <td>{{ $requete->service_destination ?? 'Non spécifié' }}</td>
                                            <td>{{ $requete->created_at ? $requete->created_at->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                @switch($requete->priorite ?? 'normale')
                                                    @case('basse')
                                                        <span class="badge bg-info">Basse</span>
                                                        @break
                                                    @case('normale')
                                                        <span class="badge bg-secondary">Normale</span>
                                                        @break
                                                    @case('haute')
                                                        <span class="badge bg-warning">Haute</span>
                                                        @break
                                                    @case('urgente')
                                                        <span class="badge bg-danger">Urgente</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">Normale</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @switch($requete->statut ?? 'en_attente')
                                                    @case('en_attente')
                                                        <span class="badge bg-warning">En attente</span>
                                                        @break
                                                    @case('en_cours')
                                                        <span class="badge bg-info">En cours</span>
                                                        @break
                                                    @case('validee')
                                                        <span class="badge bg-success">Validée</span>
                                                        @break
                                                    @case('refusee')
                                                        <span class="badge bg-danger">Refusée</span>
                                                        @break
                                                    @case('cloturee')
                                                        <span class="badge bg-dark">Clôturée</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-warning">En attente</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="voirDetails('REQ-{{ str_pad($requete->id, 3, '0', STR_PAD_LEFT) }}')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if($requete->fichier_joint)
                                                    <button class="btn btn-sm btn-outline-success" onclick="telecharger('REQ-{{ str_pad($requete->id, 3, '0', STR_PAD_LEFT) }}')">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>Vous n'avez pas encore créé de requêtes.</p>
                                                <a href="/agent/requetes/create" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Créer votre première requête
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Pagination des requêtes">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Précédent</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">Suivant</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    .table th {
        border-top: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
function filtrerRequetes() {
    // Logique de filtrage (à implémenter)
    console.log('Filtrage des requêtes...');
    // Ici vous pouvez ajouter la logique pour filtrer les requêtes
}

function reinitialiserFiltres() {
    document.getElementById('statut').value = '';
    document.getElementById('priorite').value = '';
    document.getElementById('date_debut').value = '';
    document.getElementById('date_fin').value = '';
    console.log('Filtres réinitialisés');
}

function voirDetails(reference) {
    // Ouvrir une modal ou rediriger vers la page de détails
    alert('Voir les détails de la requête ' + reference);
    // window.location.href = '/requetes/' + reference;
}

function telecharger(reference) {
    // Télécharger le PDF ou les documents de la requête
    alert('Télécharger les documents de la requête ' + reference);
    // window.location.href = '/requetes/' + reference + '/download';
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser DataTable si disponible
    if ($.fn.DataTable) {
        $('#requetesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr.json'
            },
            pageLength: 10,
            order: [[3, 'desc']] // Trier par date décroissante
        });
    }
});
</script>
@endpush
