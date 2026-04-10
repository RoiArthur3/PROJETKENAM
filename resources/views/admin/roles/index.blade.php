@extends('layouts.app')

@section('title', 'Gestion des Rôles et Permissions')

@section('content')
<div class="content-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users-cog me-2"></i>Gestion des Rôles et Permissions
        </h1>
    </div>

    <!-- Tableau des rôles -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 fw-bold">
                <i class="fas fa-list me-2"></i>Liste des Rôles
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="rolesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom du Rôle</th>
                            <th>Permissions Actives</th>
                            <th>Nombre de Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rolesWithPermissions as $roleData)
                        <tr>
                            <td>{{ $roleData['role']->id }}</td>
                            <td>
                                <strong>{{ ucfirst($roleData['role']->name) }}</strong>
                            </td>
                            <td>
                                @if(count($roleData['permissions']) > 0)
                                    <div class="permission-tags">
                                        @foreach(array_slice($roleData['permissions'], 0, 3) as $permission)
                                            <span class="badge bg-info text-white me-1 mb-1">
                                                {{ ucfirst(str_replace('.', ' ', $permission)) }}
                                            </span>
                                        @endforeach
                                        @if(count($roleData['permissions']) > 3)
                                            <span class="badge bg-secondary text-white">
                                                +{{ count($roleData['permissions']) - 3 }} autres
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">Aucune permission</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-center">
                                    <span class="badge {{ $roleData['permissions_count'] > 0 ? 'bg-success' : 'bg-danger' }} text-white">
                                        {{ $roleData['permissions_count'] }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.roles.permissions.edit', $roleData['role']->id) }}" 
                                       class="btn btn-sm btn-primary" 
                                       title="Gérer les permissions">
                                        <i class="fas fa-shield-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ count($rolesWithPermissions) }}</h4>
                            <p class="mb-0">Total rôles</p>
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
                            <?php 
                            $rolesWithPerms = collect($rolesWithPermissions)->filter(function($role) {
                                return $role['permissions_count'] > 0;
                            })->count();
                            ?>
                            <h4 class="mb-0">{{ $rolesWithPerms }}</h4>
                            <p class="mb-0">Rôles configurés</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <?php 
                            $rolesWithoutPerms = collect($rolesWithPermissions)->filter(function($role) {
                                return $role['permissions_count'] == 0;
                            })->count();
                            ?>
                            <h4 class="mb-0">{{ $rolesWithoutPerms }}</h4>
                            <p class="mb-0">Rôles sans permissions</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                            <?php 
                            $totalPerms = collect($rolesWithPermissions)->sum('permissions_count');
                            ?>
                            <h4 class="mb-0">{{ $totalPerms }}</h4>
                            <p class="mb-0">Total permissions</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-key fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.permission-tags {
    max-width: 300px;
}

.permission-tags .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

#rolesTable {
    font-size: 0.9rem;
}

#rolesTable th {
    background-color: #f8f9fc;
    border-bottom: 2px solid #e3e6f0;
}
</style>
@endsection
