@extends('layouts.app')

@section('title', 'Projets - Dashboard')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-project-diagram me-2"></i>Tableau de Bord des Projets
        </h1>
        <a href="{{ route('projets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau Projet
        </a>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Projets
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                En Cours
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['en_cours'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Terminés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['termines'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Budget Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['budget_total'] ?? 0, 0, ',', ' ') }} M</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et analyses -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Distribution par Statut
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $total = max(1, $stats['total'] ?? 1);
                        $brouillon_pct = $total > 0 ? round(($stats['brouillon'] ?? 0) / $total * 100, 1) : 0;
                        $valides_pct = $total > 0 ? round(($stats['valides'] ?? 0) / $total * 100, 1) : 0;
                        $en_cours_pct = $total > 0 ? round(($stats['en_cours'] ?? 0) / $total * 100, 1) : 0;
                        $termines_pct = $total > 0 ? round(($stats['termines'] ?? 0) / $total * 100, 1) : 0;
                        $clotured_pct = $total > 0 ? round(($stats['clotured'] ?? 0) / $total * 100, 1) : 0;
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="font-weight-bold">Brouillon</span>
                            <span class="badge bg-secondary">{{ $stats['brouillon'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $brouillon_pct }}%" aria-valuenow="{{ $brouillon_pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="font-weight-bold">Validés</span>
                            <span class="badge bg-primary">{{ $stats['valides'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $valides_pct }}%" aria-valuenow="{{ $valides_pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="font-weight-bold">En Cours</span>
                            <span class="badge bg-success">{{ $stats['en_cours'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $en_cours_pct }}%" aria-valuenow="{{ $en_cours_pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="font-weight-bold">Terminés</span>
                            <span class="badge bg-info">{{ $stats['termines'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $termines_pct }}%" aria-valuenow="{{ $termines_pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="font-weight-bold">Clôturés</span>
                            <span class="badge bg-dark">{{ $stats['clotured'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-dark" role="progressbar" style="width: {{ $clotured_pct }}%" aria-valuenow="{{ $clotured_pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calculator me-2"></i>Analyse Budgétaire
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="font-weight-bold">Budget Estimé Total</span>
                        </div>
                        <div class="h5 text-primary font-weight-bold">{{ number_format($stats['budget_total'] ?? 0, 0, ',', ' ') }} FCFA</div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="font-weight-bold">Budget Réel Total</span>
                        </div>
                        <div class="h5 text-info font-weight-bold">{{ number_format($stats['budget_reel'] ?? 0, 0, ',', ' ') }} FCFA</div>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="font-weight-bold">Écart</span>
                        </div>
                        @php
                            $ecart = ($stats['budget_reel'] ?? 0) - ($stats['budget_total'] ?? 0);
                            $ecart_class = $ecart > 0 ? 'text-danger' : 'text-success';
                        @endphp
                        <div class="h5 font-weight-bold {{ $ecart_class }}">
                            {{ $ecart > 0 ? '+' : '' }}{{ number_format($ecart, 0, ',', ' ') }} FCFA
                        </div>
                    </div>

                    @if(($stats['budget_total'] ?? 0) > 0)
                        <div class="mt-3 p-3" style="background-color: #f8f9fa; border-radius: 4px;">
                            <small class="text-muted">
                                <strong>Taux d'utilisation:</strong>
                                @php
                                    $budget_total = max(1, $stats['budget_total'] ?? 1);
                                    $taux = $budget_total > 0 ? round((($stats['budget_reel'] ?? 0) / $budget_total) * 100, 1) : 0;
                                @endphp
                                <span class="badge {{ $taux > 100 ? 'bg-danger' : ($taux > 80 ? 'bg-warning' : 'bg-success') }}">
                                    {{ $taux }}%
                                </span>
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Table des projets -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Tous les Projets
            </h6>
            <a href="{{ route('projets.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i>Nouveau
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th><i class="fas fa-briefcase"></i> Projet</th>
                        <th>Client</th>
                        <th>Responsable</th>
                        <th>Statut</th>
                        <th>Avancement</th>
                        <th>Budget</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td class="font-weight-bold">
                                <a href="{{ route('projets.show', $project->id) }}" class="text-decoration-none">
                                    {{ $project->nom }}
                                </a>
                            </td>
                            <td>{{ $project->client->nom ?? '-' }}</td>
                            <td>{{ $project->responsable->nom ?? '-' }}</td>
                            <td>
                                <span class="badge {{ 
                                    $project->statut === 'brouillon' ? 'bg-secondary' :
                                    ($project->statut === 'valide' ? 'bg-primary' :
                                    ($project->statut === 'en_cours' ? 'bg-success' :
                                    ($project->statut === 'termine' ? 'bg-info' : 'bg-dark')))
                                }}">
                                    {{ ucfirst($project->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 60px; height: 20px;" class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $project->pourcentage_avancement }}%" aria-valuenow="{{ $project->pourcentage_avancement }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="badge bg-light text-dark">{{ $project->pourcentage_avancement }}%</span>
                                </div>
                            </td>
                            <td>{{ number_format($project->budget_estime ?? 0, 0, ',', ' ') }} M</td>
                            <td>
                                <a href="{{ route('projets.show', $project->id) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('projets.edit', $project->id) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Aucun projet pour le moment.
                                <a href="{{ route('projets.create') }}" class="ms-2 text-primary">Créer un nouveau projet</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
