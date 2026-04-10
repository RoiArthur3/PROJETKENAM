@extends('layouts.app')

@section('title', 'Statistiques des Permissions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistiques des Permissions
                </h1>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Retour à la liste
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ App\Models\User::count() }}</h4>
                            <p class="card-text">Total utilisateurs</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
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
                            <h4 class="card-title">{{ count($allModules) }}</h4>
                            <p class="card-text">Modules disponibles</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cubes fa-2x"></i>
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
                            <h4 class="card-title">{{ $roles->count() }}</h4>
                            <p class="card-text">Rôles actifs</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-tag fa-2x"></i>
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
                            <h4 class="card-title">
                                {{ number_format(collect($stats)->sum('user_count'), 0) }}
                            </h4>
                            <p class="card-text">Permissions actives</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des statistiques par rôle -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>
                        Répartition des permissions par rôle
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Rôle</th>
                                    <th>Utilisateurs</th>
                                    @foreach($allModules as $key => $module)
                                        <th class="text-center">
                                            <small>{{ $module['label'] }}</small>
                                        </th>
                                    @endforeach
                                    <th class="text-center">Taux moyen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats as $roleStat)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ $roleStat['role'] == 'admin' ? 'danger' : ($roleStat['role'] == 'moderator' ? 'warning' : 'primary') }}">
                                                {{ ucfirst($roleStat['role']) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-bold me-2">{{ $roleStat['user_count'] }}</span>
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <div class="progress-bar" style="width: {{ ($roleStat['user_count'] / App\Models\User::count()) * 100 }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        @foreach($allModules as $key => $module)
                                            <td class="text-center">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span class="badge bg-{{ $roleStat['modules'][$key]['percentage'] > 50 ? 'success' : ($roleStat['modules'][$key]['percentage'] > 0 ? 'warning' : 'secondary') }}">
                                                        {{ $roleStat['modules'][$key]['count'] }}
                                                    </span>
                                                    <small class="text-muted">{{ $roleStat['modules'][$key]['percentage'] }}%</small>
                                                </div>
                                            </td>
                                        @endforeach
                                        <td class="text-center">
                                            @php
                                                $avgPercentage = collect($roleStat['modules'])->avg('percentage');
                                            @endphp
                                            <div class="d-flex flex-column align-items-center">
                                                <span class="badge bg-{{ $avgPercentage > 50 ? 'success' : ($avgPercentage > 0 ? 'warning' : 'secondary') }}">
                                                    {{ number_format($avgPercentage, 1) }}%
                                                </span>
                                                <div class="progress mt-1" style="width: 40px; height: 6px;">
                                                    <div class="progress-bar" style="width: {{ $avgPercentage }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques par module -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Répartition par module
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($allModules as $key => $module)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="{{ $module['icon'] }} me-2"></i>
                                            {{ $module['label'] }}
                                        </h6>
                                        <div class="mb-3">
                                            @php
                                                $totalUsers = collect($stats)->sum('user_count');
                                                $moduleUsers = collect($stats)->sum(function($stat) use ($key) {
                                                    return $stat['modules'][$key]['count'] ?? 0;
                                                });
                                                $percentage = $totalUsers > 0 ? ($moduleUsers / $totalUsers) * 100 : 0;
                                            @endphp
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">{{ $moduleUsers }} utilisateurs</span>
                                                <span class="badge bg-{{ $percentage > 50 ? 'success' : ($percentage > 0 ? 'warning' : 'secondary') }}">
                                                    {{ number_format($percentage, 1) }}%
                                                </span>
                                            </div>
                                            <div class="progress mt-2">
                                                <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                        <div class="small text-muted">
                                            @foreach($stats as $roleStat)
                                                @if($roleStat['modules'][$key]['count'] > 0)
                                                    <div class="d-flex justify-content-between">
                                                        <span>{{ ucfirst($roleStat['role']) }}:</span>
                                                        <span>{{ $roleStat['modules'][$key]['count'] }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
