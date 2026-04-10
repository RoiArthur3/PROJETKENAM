@extends('layouts.app')

@section('title', 'Affectation de Ressources - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-cog me-2 text-primary"></i>Affectation de Ressources
            </h1>
            <p class="text-muted mb-0">Gestion des véhicules et du personnel par projet</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#affectationModal">
            <i class="fas fa-plus me-2"></i>Nouvelle Affectation
        </button>
    </div>

    <!-- Sélection Projet -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Projet</label>
                    <select class="form-select" id="projetSelect" onchange="chargerAffectations()">
                        <option value="">-- Tous les projets --</option>
                        <option value="PRJ-2024-015">PRJ-2024-015 - NESTLE CI (Abidjan → Korhogo)</option>
                        <option value="PRJ-2024-018">PRJ-2024-018 - PETROCI (Abidjan → San-Pédro)</option>
                        <option value="PRJ-2024-020">PRJ-2024-020 - BOLLORE CI (Abidjan → Bouaké)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ressources Disponibles -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-truck me-2"></i>Véhicules Disponibles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Immatriculation</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>CI-AB-1234-XX</strong></td>
                                    <td>Camion 20T</td>
                                    <td><span class="badge bg-success">Disponible</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="affecterVehicule('CI-AB-1234-XX')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>CI-AB-5678-XX</strong></td>
                                    <td>Camion 20T</td>
                                    <td><span class="badge bg-success">Disponible</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="affecterVehicule('CI-AB-5678-XX')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>CI-AB-9012-XX</strong></td>
                                    <td>Camion Frigo</td>
                                    <td><span class="badge bg-success">Disponible</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="affecterVehicule('CI-AB-9012-XX')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>CI-AB-3456-XX</strong></td>
                                    <td>Semi-remorque</td>
                                    <td><span class="badge bg-warning">En maintenance</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('parc.vehicules') }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                        <i class="fas fa-arrow-right me-2"></i>Voir tout le parc
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-users me-2"></i>Personnel Disponible
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Fonction</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Koné Mamadou</strong></td>
                                    <td>Chauffeur PL</td>
                                    <td><span class="badge bg-success">Disponible</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="affecterPersonnel('Koné Mamadou')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Diallo Ibrahim</strong></td>
                                    <td>Chauffeur PL</td>
                                    <td><span class="badge bg-success">Disponible</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="affecterPersonnel('Diallo Ibrahim')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Touré Fatou</strong></td>
                                    <td>Logisticien</td>
                                    <td><span class="badge bg-success">Disponible</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="affecterPersonnel('Touré Fatou')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Kouassi Jean</strong></td>
                                    <td>Chauffeur PL</td>
                                    <td><span class="badge bg-warning">En congé</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @if(Route::has('rh.agents.index'))
                        <a href="{{ route('rh.agents.index') }}" class="btn btn-sm btn-outline-info w-100 mt-2">
                            <i class="fas fa-arrow-right me-2"></i>Voir tous les agents
                        </a>
                    @else
                        <a href="{{ route('rh.dashboard') }}" class="btn btn-sm btn-outline-info w-100 mt-2">
                            <i class="fas fa-arrow-right me-2"></i>Voir tous les agents
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Affectations Actuelles -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-list me-2"></i>Affectations Actuelles
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="affectationsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Projet</th>
                            <th>Véhicule</th>
                            <th>Chauffeur</th>
                            <th>Personnel Logistique</th>
                            <th>Date Début</th>
                            <th>Date Fin</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>PRJ-2024-015</strong><br>
                                <small class="text-muted">NESTLE CI</small>
                            </td>
                            <td>
                                <a href="{{ route('parc.vehicules') }}">CI-AB-1234-XX</a><br>
                                <small class="text-muted">Camion 20T</small>
                            </td>
                            <td>
                                @if(Route::has('rh.agents.index'))
                                    <a href="{{ route('rh.agents.index') }}">Koné Mamadou</a><br>
                                @else
                                    <a href="{{ route('rh.dashboard') }}">Koné Mamadou</a><br>
                                @endif
                                <small class="text-muted">+225 07 12 34 56 78</small>
                            </td>
                            <td>
                                @if(Route::has('rh.agents.index'))
                                    <a href="{{ route('rh.agents.index') }}">Touré Fatou</a><br>
                                @else
                                    <a href="{{ route('rh.dashboard') }}">Touré Fatou</a><br>
                                @endif
                                <small class="text-muted">Logisticien</small>
                            </td>
                            <td>01/11/2024</td>
                            <td>15/11/2024</td>
                            <td><span class="badge bg-primary">En cours</span></td>
                            <td>
                                <button class="btn btn-sm btn-warning" title="Modifier"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" title="Retirer"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>PRJ-2024-018</strong><br>
                                <small class="text-muted">PETROCI</small>
                            </td>
                            <td>
                                <a href="{{ route('parc.vehicules') }}">CI-AB-5678-XX</a><br>
                                <small class="text-muted">Camion 20T</small>
                            </td>
                            <td>
                                <a href="{{ route('rh.agents.index') }}">Diallo Ibrahim</a><br>
                                <small class="text-muted">+225 05 98 76 54 32</small>
                            </td>
                            <td>-</td>
                            <td>05/11/2024</td>
                            <td>20/11/2024</td>
                            <td><span class="badge bg-primary">En cours</span></td>
                            <td>
                                <button class="btn btn-sm btn-warning" title="Modifier"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" title="Retirer"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouvelle Affectation -->
<div class="modal fade" id="affectationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nouvelle Affectation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Projet *</label>
                        <select class="form-select">
                            <option>PRJ-2024-015 - NESTLE CI (Abidjan → Korhogo)</option>
                            <option>PRJ-2024-018 - PETROCI (Abidjan → San-Pédro)</option>
                            <option>PRJ-2024-020 - BOLLORE CI (Abidjan → Bouaké)</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Véhicule *</label>
                            <select class="form-select">
                                <option>CI-AB-1234-XX - Camion 20T</option>
                                <option>CI-AB-5678-XX - Camion 20T</option>
                                <option>CI-AB-9012-XX - Camion Frigo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Chauffeur *</label>
                            <select class="form-select">
                                <option>Koné Mamadou</option>
                                <option>Diallo Ibrahim</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Personnel Logistique</label>
                            <select class="form-select">
                                <option>-- Aucun --</option>
                                <option>Touré Fatou</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date Début</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Instructions</label>
                            <textarea class="form-control" rows="3" placeholder="Instructions spécifiques pour cette affectation..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Affecter
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    setTimeout(function() {
        if ($.fn.DataTable.isDataTable('#affectationsTable')) {
            $('#affectationsTable').DataTable().destroy();
        }
        $('#affectationsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
            },
            columnDefs: [
                { targets: '_all', defaultContent: '-' }
            ]
        });
    }, 100);
});

function chargerAffectations() {
    // Logique de filtrage
}

function affecterVehicule(immat) {
    alert('Affecter véhicule: ' + immat);
}

function affecterPersonnel(nom) {
    alert('Affecter personnel: ' + nom);
}
</script>
@endsection
