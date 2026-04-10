@extends('layouts.app')

@section('title', 'Gestion des Permissions - ' . $role->name)

@section('content')
<div class="content-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-shield-alt me-2"></i>Gestion des Permissions
        </h1>
        <div>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour aux rôles
            </a>
        </div>
    </div>

    <!-- Informations du rôle -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 fw-bold">
                <i class="fas fa-user-tag me-2"></i>Rôle: {{ $role->name }}
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.permissions.update', $role->id) }}" method="POST">
                @csrf
                
                <!-- Permissions par module -->
                @foreach($groupedPermissions as $moduleName => $permissions)
                    @if($permissions->count() > 0)
                    <div class="mb-4">
                        <h6 class="text-primary fw-bold mb-3">
                            <i class="fas fa-cube me-2"></i>{{ $moduleName }}
                        </h6>
                        
                        <div class="row">
                            @foreach($permissions as $permission)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission->id }}"
                                           id="permission_{{ $permission->id }}"
                                           @if(in_array($permission->id, $rolePermissions)) checked @endif>
                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                        <strong>{{ ucfirst(str_replace('.', ' ', $permission->name)) }}</strong>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-between mt-4">
                    <div>
                        <button type="button" class="btn btn-outline-primary" onclick="selectAllPermissions()">
                            <i class="fas fa-check-square me-2"></i>Tout sélectionner
                        </button>
                        <button type="button" class="btn btn-outline-secondary ms-2" onclick="deselectAllPermissions()">
                            <i class="fas fa-square me-2"></i>Tout désélectionner
                        </button>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Enregistrer les permissions
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ count($rolePermissions) }}</h4>
                            <p class="mb-0">Permissions actives</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $groupedPermissions->flatten()->count() - count($rolePermissions) }}</h4>
                            <p class="mb-0">Permissions disponibles</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $groupedPermissions->flatten()->count() }}</h4>
                            <p class="mb-0">Total permissions</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(function(checkbox) {
        checkbox.checked = true;
    });
}

function deselectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(function(checkbox) {
        checkbox.checked = false;
    });
}
</script>
@endsection
