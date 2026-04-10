@extends('layouts.app')

@section('title', 'Reporting par Service - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Reporting par Service</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('reporting.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="{{ route('reporting.financier') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line me-2"></i>Financier
            </a>
            <a href="{{ route('reporting.operations') }}" class="btn btn-outline-primary">
                <i class="fas fa-cogs me-2"></i>Opérations
            </a>
            <a href="{{ route('reporting.services') }}" class="btn btn-warning active">
                <i class="fas fa-building me-2"></i>Services
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('reporting.services') }}" method="GET" class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Période</label>
                    <select name="period" class="form-select">
                        <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="month" {{ request('period') === 'month' || !request('period') ? 'selected' : '' }}>Ce mois</option>
                        <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Cette année</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Recherche Service</label>
                    <input type="text" name="search" class="form-control" placeholder="Nom ou code du service..." value="{{ request('search', '') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Actualiser les données
                    </button>
                    <a href="{{ route('reporting.services') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-redo me-2"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(empty($service_stats) || count($service_stats) === 0)
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Aucune donnée disponible</strong> - Il n'y a pas d'opérations pour les services selon les critères sélectionnés.
        </div>
    @else
        <!-- KPI Cards -->
        <div class="row">
            @foreach($service_stats as $stat)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        {{ $stat->nom }} <span class="badge bg-secondary">{{ $stat->code }}</span>
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $stat->count }} Opération{{ $stat->count !== 1 ? 's' : '' }}
                                    </div>
                                    <div class="mt-2 small">
                                        <div class="text-muted">Total: <span class="fw-bold text-dark">{{ number_format($stat->total_amount, 0, ',', ' ') }} FCFA</span></div>
                                        <div class="text-muted">Délai moyen: <span class="fw-bold text-dark">{{ $stat->avg_validation_time }} h</span></div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="progress progress-sm mr-2" style="width: 50px; height: 30px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ max(0, min(100, $stat->success_rate)) }}%" 
                                             aria-valuenow="{{ $stat->success_rate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="text-xs text-muted mt-1 text-center">{{ round($stat->success_rate, 1) }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Tableau détaillé -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table me-2"></i>Analyse détaillée par service
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Service</th>
                                <th>Opérations</th>
                                <th>Volume Financier</th>
                                <th>Panier Moyen</th>
                                <th>Délai Moyen (h)</th>
                                <th>Taux de Succès</th>
                                <th>Statuts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($service_stats as $stat)
                                <tr>
                                    <td>
                                        <strong>{{ $stat->nom }}</strong><br>
                                        <small class="text-muted">{{ $stat->code }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $stat->count }}</span>
                                    </td>
                                    <td class="fw-bold">{{ number_format($stat->total_amount, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ number_format($stat->avg_amount, 0, ',', ' ') }} FCFA</td>
                                    <td><span class="badge bg-warning text-dark">{{ $stat->avg_validation_time }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-{{ $stat->success_rate >= 75 ? 'success' : ($stat->success_rate >= 50 ? 'warning' : 'danger') }}">
                                                {{ round($stat->success_rate, 1) }}%
                                            </span>
                                            <div class="progress flex-grow-1" style="height: 5px; min-width: 60px;">
                                                <div class="progress-bar" 
                                                     style="width: {{ max(0, min(100, $stat->success_rate)) }}%; background-color: {{ $stat->success_rate >= 75 ? '#28a745' : ($stat->success_rate >= 50 ? '#ffc107' : '#dc3545') }}"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @forelse($stat->statuses as $status => $count)
                                            <span class="badge bg-secondary mb-1">
                                                {{ ucfirst(str_replace('_', ' ', $status)) }}: <strong>{{ $count }}</strong>
                                            </span>
                                        @empty
                                            <span class="text-muted">-</span>
                                        @endforelse
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .border-left-primary {
        border-left: 0.25rem solid #0d6efd !important;
    }
    .text-xs {
        font-size: .7rem;
    }
    .progress-sm {
        height: 5px;
    }
</style>
@endsection
