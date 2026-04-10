@extends('layouts.app')

@section('title', 'Requêtes - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs me-2 text-primary"></i>Requêtes
            </h1>
            <p class="text-muted mb-0">Gestion des requêtes et demandes</p>
        </div>
        <div>
            <a href="{{ route('requetes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Requête
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('operations.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select name="statut" id="statut" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente">En attente</option>
                            <option value="en_cours">En cours</option>
                            <option value="termine">Terminé</option>
                            <option value="annule">Annulé</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="priorite" class="form-label">Priorité</label>
                        <select name="priorite" id="priorite" class="form-select">
                            <option value="">Toutes les priorités</option>
                            <option value="basse">Basse</option>
                            <option value="normale">Normale</option>
                            <option value="haute">Haute</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="service" class="form-label">Service</label>
                        <select name="service" id="service" class="form-select">
                            <option value="">Tous les services</option>
                            <option value="rh">Ressources Humaines</option>
                            <option value="comptabilite">Comptabilité</option>
                            <option value="informatique">Informatique</option>
                            <option value="logistique">Logistique</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search me-2"></i>Filtrer
                        </button>
                        <a href="{{ route('operations.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des requêtes -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Requêtes
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Titre</th>
                            <th>Demandeur</th>
                            <th>Service</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Exemple de données - à remplacer par vos vraies données -->
                        <tr>
                            <td><span class="badge bg-primary">REQ-2024-001</span></td>
                            <td>
                                <div>
                                    <strong>Demande de matériel informatique</strong>
                                    <br><small class="text-muted">Besoin d'un nouvel ordinateur portable</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        JD
                                    </div>
                                    <div>
                                        <div class="fw-bold">Jean Dupont</div>
                                        <small class="text-muted">jean.dupont@kenam.com</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-info">Informatique</span></td>
                            <td><span class="badge bg-warning">Haute</span></td>
                            <td><span class="badge bg-secondary">En attente</span></td>
                            <td>29/12/2024</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" title="Traiter">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" title="Annuler">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-primary">REQ-2024-002</span></td>
                            <td>
                                <div>
                                    <strong>Demande de congés</strong>
                                    <br><small class="text-muted">Congés annuels du 15 au 30 janvier</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        MS
                                    </div>
                                    <div>
                                        <div class="fw-bold">Marie Sophie</div>
                                        <small class="text-muted">marie.sophie@kenam.com</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-info">RH</span></td>
                            <td><span class="badge bg-success">Normale</span></td>
                            <td><span class="badge bg-primary">En cours</span></td>
                            <td>28/12/2024</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" title="Approuver">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" title="Reporter">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
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

<style>
.avatar-sm {
    font-size: 12px;
    font-weight: bold;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.badge {
    font-size: 0.75em;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
</style>
@endsection
