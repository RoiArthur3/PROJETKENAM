@extends('layouts.app')

@section('title', 'Planifications - Contrôle & Audit')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-calendar-alt text-primary me-2"></i>Planifications d'Audit
            </h1>
            <p class="text-muted mb-0">Gestion des planifications des contrôles et audits</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('controle-audit.planifs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle planification
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total planifications
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $planifications->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                En cours
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $planifications->where('statut', 'en_cours')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Programmées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $planifications->where('statut', 'programmee')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Terminées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $planifications->where('statut', 'terminee')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table des planifications -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des planifications
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Titre</th>
                            <th>Type d'audit</th>
                            <th>Période</th>
                            <th>Responsable</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($planifications as $planification)
                            <tr>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">#{{ str_pad($planification->id, 4, '0', STR_PAD_LEFT) }}</code>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $planification->titre }}</div>
                                    <small class="text-muted">{{ $planification->description }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $planification->type_audit }}</span>
                                </td>
                                <td>
                                    {{ $planification->date_debut ? $planification->date_debut->format('d/m/Y') : 'N/A' }} -
                                    {{ $planification->date_fin ? $planification->date_fin->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td>
                                    @if($planification->responsable)
                                        {{ $planification->responsable->name }}
                                    @else
                                        <span class="text-muted">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $planification->statut === 'terminee' ? 'success' : ($planification->statut === 'en_cours' ? 'info' : ($planification->statut === 'programmee' ? 'warning' : 'secondary')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $planification->statut)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('controle-audit.planifs.show', $planification) }}" class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('controle-audit.planifs.edit', $planification) }}" class="btn btn-outline-secondary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                                    <div class="text-muted">Aucune planification trouvée</div>
                                    <p class="text-muted small">Commencez par créer votre première planification d'audit.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $planifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
