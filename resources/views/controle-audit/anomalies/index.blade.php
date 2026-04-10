@extends('layouts.app')

@section('title', 'Anomalies - Contrôle & Audit')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-exclamation-triangle text-primary me-2"></i>Anomalies d'Audit
            </h1>
            <p class="text-muted mb-0">Gestion des anomalies et non-conformités détectées</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('controle-audit.anomalies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle anomalie
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
                                Total anomalies
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $anomalies->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Critiques
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $anomalies->where('severite', 'critique')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
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
                                En cours
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $anomalies->where('statut', 'en_cours')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
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
                                Résolues
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $anomalies->where('statut', 'resolue')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table des anomalies -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des anomalies
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Description</th>
                            <th>Type d'anomalie</th>
                            <th>Sévérité</th>
                            <th>Date découverte</th>
                            <th>Responsable</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anomalies as $anomalie)
                            <tr>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">#{{ str_pad($anomalie->id, 4, '0', STR_PAD_LEFT) }}</code>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $anomalie->description }}</div>
                                    @if($anomalie->cause)
                                        <small class="text-muted">Cause: {{ $anomalie->cause }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $anomalie->type_anomalie }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $anomalie->severite === 'critique' ? 'danger' : ($anomalie->severite === 'majeure' ? 'warning' : 'info') }}">
                                        {{ ucfirst($anomalie->severite) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $anomalie->date_decouverte ? $anomalie->date_decouverte->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td>
                                    @if($anomalie->responsable)
                                        {{ $anomalie->responsable->name }}
                                    @else
                                        <span class="text-muted">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $anomalie->statut === 'resolue' ? 'success' : ($anomalie->statut === 'en_cours' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $anomalie->statut)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('controle-audit.anomalies.show', $anomalie) }}" class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('controle-audit.anomalies.edit', $anomalie) }}" class="btn btn-outline-secondary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                                    <div class="text-muted">Aucune anomalie trouvée</div>
                                    <p class="text-muted small">Commencez par créer votre première anomalie d'audit.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $anomalies->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
