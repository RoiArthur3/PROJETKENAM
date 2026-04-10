@extends('layouts.app')

@section('title', 'Liste des Projets - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Liste des Projets</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('projets.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Projets
            </a>
            <a href="{{ route('projets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Projet
            </a>
        </div>
    </div>

    <!-- KPIs Projets -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Projets</h6>
                            <h3 class="mb-0">{{ $projects->count() ?? 0 }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-briefcase"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">En Cours</h6>
                            <h3 class="mb-0">{{ $projects->where('statut', 'en_cours')->count() ?? 0 }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-spinner"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Budget Total</h6>
                            <h3 class="mb-0">{{ number_format($projects->sum('budget_estime') ?? 0, 0, ',', ' ') }} M</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Avancement Moyen</h6>
                            <h3 class="mb-0">
                                @if($projects->count() > 0)
                                    {{ number_format($projects->avg('pourcentage_avancement') ?? 0, 1, ',', ' ') }}%
                                @else
                                    0%
                                @endif
                            </h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Projets -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nom du Projet</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Budget</th>
                            <th>Avancement</th>
                            <th>Responsable</th>
                            <th>Statut</th>
                            <th>Dates</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $project->nom }}</div>
                                <div class="text-muted small">ID: {{ $project->id }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $project->client->nom ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $project->type)) }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ number_format($project->budget_estime ?? 0, 0, ',', ' ') }} M</div>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px; width: 100px;">
                                    <div class="progress-bar bg-{{ $project->pourcentage_avancement >= 75 ? 'success' : ($project->pourcentage_avancement >= 50 ? 'warning' : 'danger') }}"
                                         style="width: {{ $project->pourcentage_avancement }}%">
                                        {{ $project->pourcentage_avancement }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $project->responsable->nom ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $project->statut === 'brouillon' ? 'secondary' :
                                    ($project->statut === 'valide' ? 'primary' :
                                    ($project->statut === 'en_cours' ? 'success' :
                                    ($project->statut === 'termine' ? 'info' : 'dark')))
                                }}">
                                    {{ ucfirst($project->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-muted small">
                                    <div>Début: {{ $project->date_debut ? $project->date_debut->format('d/m/y') : '-' }}</div>
                                    <div>Fin: {{ $project->date_fin_prevue ? $project->date_fin_prevue->format('d/m/y') : '-' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('projets.show', $project->id) }}" class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('projets.edit', $project->id) }}" class="btn btn-outline-secondary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('projets.destroy', $project->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun projet trouvé</p>
                                <a href="{{ route('projets.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Créer le premier projet
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques Projets par Statut -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Distribution par Statut</h5>
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-secondary">Brouillon</h6>
                                    <p class="mb-0 text-secondary" style="font-size: 24px; font-weight: bold;">{{ $projects->where('statut', 'brouillon')->count() }}</p>
                                    <small class="text-muted">Projets en préparation</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary">Validés</h6>
                                    <p class="mb-0 text-primary" style="font-size: 24px; font-weight: bold;">{{ $projects->where('statut', 'valide')->count() }}</p>
                                    <small class="text-muted">Projets approuvés</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-success">En Cours</h6>
                                    <p class="mb-0 text-success" style="font-size: 24px; font-weight: bold;">{{ $projects->where('statut', 'en_cours')->count() }}</p>
                                    <small class="text-muted">Projets en exécution</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-info">Terminés</h6>
                                    <p class="mb-0 text-info" style="font-size: 24px; font-weight: bold;">{{ $projects->where('statut', 'termine')->count() }}</p>
                                    <small class="text-muted">Projets achevés</small>
                                </div>
                            </div>
                        </div>
                    </div>
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

.content-wrapper {
    padding: 20px;
}
</style>

@endsection
