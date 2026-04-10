@extends('layouts.app')

@section('title', 'Rôles et Permissions - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-shield me-2 text-primary"></i>Rôles et Permissions
            </h1>
            <p class="text-muted mb-0">Gestion des rôles système et leurs permissions</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="fas fa-plus me-1"></i>Nouveau Rôle
        </button>
    </div>

    <!-- Alertes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Liste des rôles -->
    <div class="row">
        @forelse($roles as $role)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-user-tag me-2 text-{{ ['admin' => 'danger', 'rh' => 'success', 'agent' => 'primary', 'client' => 'info'][$role->name] ?? 'secondary' }}"></i>
                            {{ ucfirst($role->name) }}
                        </h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="editRole({{ $role->id }})">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')">
                                    <i class="fas fa-trash me-2"></i>Supprimer
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($role->description)
                            <p class="text-muted mb-3">{{ $role->description }}</p>
                        @endif

                        <div class="mb-3">
                            <h6>Permissions associées :</h6>
                            @if($role->permissions && $role->permissions->count() > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($role->permissions as $permission)
                                        <span class="badge bg-light text-dark">{{ $permission->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">Aucune permission définie</p>
                            @endif
                        </div>

                        <div class="row text-center">
                            <div class="col-6">
                                <div class="h5 mb-0">{{ $role->users_count ?? 0 }}</div>
                                <small class="text-muted">Utilisateurs</small>
                            </div>
                            <div class="col-6">
                                <div class="h5 mb-0">{{ $role->permissions->count() }}</div>
                                <small class="text-muted">Permissions</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucun rôle défini</h5>
                        <p class="text-muted">Créez votre premier rôle pour commencer</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                            <i class="fas fa-plus me-1"></i>Créer le premier rôle
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal de création de rôle -->
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Créer un nouveau rôle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('settings.roles.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du rôle <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Utilisez des minuscules et des tirets (ex: gestionnaire-stock)</div>
                    </div>
                    <div class="mb-3">
                        <label for="display_name" class="form-label">Nom d'affichage</label>
                        <input type="text" class="form-control" id="display_name" name="display_name" value="{{ old('display_name') }}">
                        <div class="form-text">Nom affiché dans l'interface (optionnel)</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le rôle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editRole(roleId) {
    // TODO: Implémenter l'édition de rôle
    alert('Fonction d\'édition à implémenter');
}

function deleteRole(roleId, roleName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le rôle "${roleName}" ?`)) {
        // TODO: Implémenter la suppression de rôle
        alert('Fonction de suppression à implémenter');
    }
}
</script>
@endsection
