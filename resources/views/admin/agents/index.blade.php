@extends('layouts.app')

@section('title', 'Gestion des Agents - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users me-2 text-primary"></i>Gestion des Agents
            </h1>
            <p class="text-muted mb-0">Agents liés aux services avec accès limité aux requêtes</p>
        </div>
        <div>
            <a href="{{ route('admin.agents.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Nouvel Agent
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Agents
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_agents'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Agents Actifs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['active_agents'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Répartition par Service
                    </div>
                    <div class="row">
                        @foreach($stats['agents_by_service'] as $serviceName => $count)
                            <div class="col-6 mb-2">
                                <small class="text-muted">{{ $serviceName }}:</small>
                                <div class="h6 mb-0">{{ $count }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filtres
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('parc.vehicules.admin.agents.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Recherche</label>
                        <input type="text" name="search" class="form-control"
                               value="{{ request('search') }}" placeholder="Nom, prénom, matricule, email...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Service</label>
                        <select name="service_id" class="form-control">
                            <option value="">Tous les services</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}"
                                        {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                            <a href="{{ route('parc.vehicules.admin.agents.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des agents -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Agents
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Service</th>
                            <th>Statut</th>
                            <th>Dernière connexion</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agents as $agent)
                            <tr>
                                <td>{{ $agent->matricule }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            {{ strtoupper(substr($agent->prenom, 0, 1)) }}{{ strtoupper(substr($agent->nom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $agent->nom_complet }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $agent->email }}</td>
                                <td>{{ $agent->telephone ?? '-' }}</td>
                                <td>
                                    @if($agent->service)
                                        <span class="badge bg-info">{{ $agent->service->nom }}</span>
                                    @else
                                        <span class="text-muted">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    @if($agent->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    @if($agent->last_login)
                                        <small>{{ $agent->last_login->format('d/m/Y H:i') }}</small>
                                    @else
                                        <span class="text-muted">Jamais</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.agents.show', $agent->id) }}"
                                           class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.agents.edit', $agent->id) }}"
                                           class="btn btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.agents.toggle-status', $agent->id) }}"
                                              class="d-inline" onsubmit="return confirm('Êtes-vous sûr?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-{{ $agent->is_active ? 'danger' : 'success' }}"
                                                    title="{{ $agent->is_active ? 'Désactiver' : 'Activer' }}">
                                                <i class="fas fa-{{ $agent->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.agents.reset-password', $agent->id) }}"
                                              class="d-inline" onsubmit="return confirm('Réinitialiser le mot de passe?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-info" title="Réinitialiser mot de passe">
                                                <i class="fas fa-key"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <div>Aucun agent trouvé</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Affichage de {{ $agents->firstItem() }} à {{ $agents->lastItem() }}
                    sur {{ $agents->total() }} agents
                </div>
                {{ $agents->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
