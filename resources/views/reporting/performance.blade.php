@extends('layouts.app')

@section('title', 'Performance des Services, Agents et Véhicules - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-chart-bar me-2"></i>Performance Opérationnelle
        </h1>
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
            <a href="{{ route('reporting.services') }}" class="btn btn-outline-primary">
                <i class="fas fa-building me-2"></i>Services
            </a>
            <a href="{{ route('reporting.performance') }}" class="btn btn-warning active">
                <i class="fas fa-chart-bar me-2"></i>Performance
            </a>
        </div>
    </div>

    <!-- Performance par Service -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-building me-2"></i>Performance par Service
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Service</th>
                            <th><i class="fas fa-cube me-1"></i>Code</th>
                            <th><i class="fas fa-tasks me-1"></i>Opérations</th>
                            <th><i class="fas fa-percentage me-1"></i>Taux Succès</th>
                            <th><i class="fas fa-clock me-1"></i>Durée Moyenne (h)</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($service_performance ?? [] as $service)
                            <tr>
                                <td>
                                    <strong>{{ $service['nom'] }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $service['code'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $service['count'] }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-{{ $service['success_rate'] >= 75 ? 'success' : ($service['success_rate'] >= 50 ? 'warning' : 'danger') }}">
                                            {{ $service['success_rate'] }}%
                                        </span>
                                        <div class="progress flex-grow-1" style="height: 20px; min-width: 100px;">
                                            <div class="progress-bar" style="width: {{ $service['success_rate'] }}%; background-color: {{ $service['success_rate'] >= 75 ? '#28a745' : ($service['success_rate'] >= 50 ? '#ffc107' : '#dc3545') }}"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $service['avg_time'] }}</span>
                                </td>
                                <td>
                                    <i class="fas fa-circle text-success me-1"></i>Actif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Aucune donnée de service disponible
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Performance par Agent -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-user-tie me-2"></i>Performance par Agent/Responsable
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($agent_performance ?? [] as $agent)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card border-left-success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $agent['name'] }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-phone me-1"></i>{{ $agent['phone'] }}
                                        </small>
                                    </div>
                                    <i class="fas fa-user-circle fa-2x text-success opacity-25"></i>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small">Taux de Réussite</span>
                                        <span class="badge bg-success fw-bold">{{ $agent['success_rate'] }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: {{ $agent['success_rate'] }}%"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small">Productivité</span>
                                        <span class="badge bg-info fw-bold">{{ $agent['productivity'] }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-info" style="width: {{ $agent['productivity'] }}%"></div>
                                    </div>
                                </div>

                                <div class="border-top pt-2">
                                    <div class="text-center">
                                        <p class="mb-0 small text-muted">
                                            <i class="fas fa-tasks me-1"></i>
                                            <strong>{{ $agent['count'] }}</strong> opérations
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucune donnée d'agent disponible
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Performance par Véhicule/Engin -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-truck me-2"></i>Performance par Véhicule/Engin
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Immatriculation</th>
                            <th>Marque/Modèle</th>
                            <th>Statut</th>
                            <th><i class="fas fa-road me-1"></i>Km Parcourus</th>
                            <th><i class="fas fa-gas-pump me-1"></i>Carburant (FCFA)</th>
                            <th><i class="fas fa-tools me-1"></i>Maintenance (FCFA)</th>
                            <th><i class="fas fa-cog me-1"></i>Opérations</th>
                            <th>Coût/Km</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicle_performance ?? [] as $vehicle)
                            @php
                                $cost_per_km = $vehicle['km_traveled'] > 0 
                                    ? round(($vehicle['fuel_cost'] + $vehicle['maintenance_cost']) / $vehicle['km_traveled'], 0)
                                    : 0;
                            @endphp
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $vehicle['registration'] }}</strong>
                                </td>
                                <td>{{ $vehicle['brand_model'] }}</td>
                                <td>
                                    <span class="badge bg-{{ $vehicle['status_color'] }}">
                                        {{ ucfirst($vehicle['status']) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ number_format($vehicle['km_traveled'], 0, ',', ' ') }} km
                                    </span>
                                </td>
                                <td>
                                    <span class="text-danger fw-bold">
                                        {{ number_format($vehicle['fuel_cost'], 0, ',', ' ') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-warning fw-bold">
                                        {{ number_format($vehicle['maintenance_cost'], 0, ',', ' ') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $vehicle['ops_count'] }}</span>
                                </td>
                                <td>
                                    <strong class="text-info">
                                        {{ number_format($cost_per_km, 0, ',', ' ') }} FCFA
                                    </strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Aucune donnée de véhicule disponible
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Résumé Global -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="text-primary text-uppercase small fw-bold mb-1">
                        Services Actifs
                    </div>
                    <div class="h3 mb-0">{{ count($service_performance ?? []) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="text-success text-uppercase small fw-bold mb-1">
                        Agents/Responsables
                    </div>
                    <div class="h3 mb-0">{{ count($agent_performance ?? []) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="text-info text-uppercase small fw-bold mb-1">
                        Véhicules/Engins
                    </div>
                    <div class="h3 mb-0">{{ count($vehicle_performance ?? []) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="text-warning text-uppercase small fw-bold mb-1">
                        Opérations Total
                    </div>
                    <div class="h3 mb-0">
                        {{ collect($agent_performance ?? [])->sum('count') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 0.25rem solid #0d6efd !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #198754 !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #0dcaf0 !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #ffc107 !important;
    }
</style>
@endsection
