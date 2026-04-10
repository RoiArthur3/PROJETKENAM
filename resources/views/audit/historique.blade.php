@extends('layouts.app')

@section('title', 'Historique Audit - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Historique des Activités</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('audit.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="{{ route('audit.controles') }}" class="btn btn-outline-primary">
                <i class="fas fa-clipboard-check me-2"></i>Contrôles
            </a>
            <a href="{{ route('audit.rapports') }}" class="btn btn-outline-info">
                <i class="fas fa-file-alt me-2"></i>Rapports
            </a>
            <a href="{{ route('audit.historique') }}" class="btn btn-primary">
                <i class="fas fa-history me-2"></i>Historique
            </a>
        </div>
    </div>

    <!-- KPIs Historique -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Actions Aujourd'hui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\AuditLog::whereDate('created_at', today())->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Utilisateurs Actifs (24h)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\AuditLog::where('created_at', '>=', now()->subDay())->distinct('user_id')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Logs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\AuditLog::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau Historique -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4">Journal d'audit</h5>

            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Rechercher une action, un utilisateur..." id="searchInput">
                </div>
                <div class="col-md-4">
                    <select class="form-select">
                        <option value="">Tous les utilisateurs</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info w-100">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Utilisateur</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Détails</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; display:flex; justify-content:center; align-items:center; border-radius:50%; font-size:12px;">
                                        {{ substr($log->user->name ?? '?', 0, 2) }}
                                    </div>
                                    <span class="fw-bold">{{ $log->user->name ?? 'Inconnu' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ str_contains($log->action, 'update') ? 'warning' : (str_contains($log->action, 'delete') ? 'danger' : 'success') }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>{{ class_basename($log->model_type) }} #{{ $log->model_id }}</td>
                            <td>
                                <small class="text-muted text-wrap" style="max-width: 300px; display:block;">
                                    User-Agent: {{ $log->user_agent }}
                                </small>
                            </td>
                            <td>{{ $log->ip_address }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">Aucune activité enregistrée</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
