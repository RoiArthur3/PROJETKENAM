@extends('layouts.app')

@section('title', 'Profils et Permissions')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fas fa-users-cog text-primary me-2"></i>
                        Profils et Permissions
                    </h1>
                    <p class="text-muted mb-0">Visualisez chaque profil avec ses permissions et modules accessibles</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour
                    </a>
                    <a href="{{ route('admin.permissions.statistics') }}" class="btn btn-outline-info">
                        <i class="fas fa-chart-bar me-1"></i>
                        Statistiques
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Profiles Cards -->
    @foreach($profilesData as $profile)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user-tag me-2"></i>
                                {{ ucfirst($profile['role']->name) }}
                                @if($profile['role']->description)
                                    <small class="opacity-75">- {{ $profile['role']->description }}</small>
                                @endif
                            </h5>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-white text-primary me-3">
                                <i class="fas fa-users me-1"></i>
                                {{ $profile['users_count'] }} utilisateur(s)
                            </span>
                            <button class="btn btn-sm btn-outline-light" type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#profile_{{ $profile['role']->id }}"
                                    aria-expanded="false">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="collapse show" id="profile_{{ $profile['role']->id }}">
                    <div class="card-body">
                        <!-- Permissions Section -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-3">
                                    <i class="fas fa-key text-warning me-2"></i>
                                    Permissions du Rôle
                                </h6>
                                <div class="row g-2">
                                    @if(in_array('*', $profile['permissions']))
                                        <div class="col-12">
                                            <div class="d-flex align-items-center p-2 bg-warning bg-opacity-10 border border-warning rounded">
                                                <i class="fas fa-star text-warning me-2"></i>
                                                <small class="fw-medium">Accès complet à toutes les permissions</small>
                                            </div>
                                        </div>
                                    @else
                                        @foreach($profile['permissions'] as $permission)
                                            <div class="col-6">
                                                <div class="d-flex align-items-center p-2 bg-success bg-opacity-10 border border-success rounded">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    <small class="fw-medium">{{ $permission }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-3">
                                    <i class="fas fa-cubes text-info me-2"></i>
                                    Modules Accessibles
                                </h6>
                                <div class="row g-2">
                                    @if(in_array('*', $profile['modules']))
                                        <div class="col-12">
                                            <div class="d-flex align-items-center p-2 bg-info bg-opacity-10 border border-info rounded">
                                                <i class="fas fa-globe text-info me-2"></i>
                                                <small class="fw-medium">Accès à tous les modules</small>
                                            </div>
                                        </div>
                                    @else
                                        @foreach($profile['modules'] as $module)
                                            <div class="col-6">
                                                <div class="d-flex align-items-center p-2 bg-primary bg-opacity-10 border border-primary rounded">
                                                    <i class="fas fa-cube text-primary me-2"></i>
                                                    <small class="fw-medium">{{ ucfirst($module) }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Users List -->
                        @if($profile['users']->count() > 0)
                        <div class="border-top pt-3">
                            <h6 class="fw-semibold mb-3">
                                <i class="fas fa-users text-secondary me-2"></i>
                                Utilisateurs avec ce profil
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Modules Actifs</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($profile['users'] as $userData)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ strtoupper(substr($userData['user']->name, 0, 1)) }}
                                                    </div>
                                                    {{ $userData['user']->name }}
                                                </div>
                                            </td>
                                            <td>{{ $userData['user']->email }}</td>
                                            <td>{{ $userData['user']->telephone ?? '-' }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($userData['active_modules'] as $module)
                                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">
                                                            {{ ucfirst($module) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.permissions.user', $userData['user']->id) }}" 
                                                       class="btn btn-outline-primary" title="Voir les permissions">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.users.edit', $userData['user']->id) }}" 
                                                       class="btn btn-outline-secondary" title="Modifier l'utilisateur">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <p>Aucun utilisateur n'a ce profil pour le moment.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Summary Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Résumé des Profils
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($profilesData as $profile)
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">{{ $profile['users_count'] }}</h4>
                                <h6 class="text-muted mb-2">{{ ucfirst($profile['role']->name) }}</h6>
                                <div class="d-flex justify-content-center gap-1 mb-2">
                                    <span class="badge bg-success">
                                        {{ count($profile['permissions']) }} perms
                                    </span>
                                    <span class="badge bg-info">
                                        {{ count($profile['modules']) }} mods
                                    </span>
                                </div>
                                <small class="text-muted">
                                    @if(in_array('*', $profile['permissions']))
                                        <i class="fas fa-star text-warning"></i> Accès complet
                                    @else
                                        <i class="fas fa-key"></i> Permissions limitées
                                    @endif
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745, #1e7e34) !important;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
    font-weight: bold;
}

.collapse .card-body {
    border-top: none;
}

.badge {
    font-size: 0.75em;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle collapse animation
    const collapseButtons = document.querySelectorAll('[data-bs-toggle="collapse"]');
    
    collapseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('i');
            const target = document.querySelector(this.getAttribute('data-bs-target'));
            
            if (target.classList.contains('show')) {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            } else {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        });
    });
});
</script>
@endsection
