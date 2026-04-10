@extends('layouts.app')

@section('title', 'Modifier l\'utilisateur')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">
            <i class="fas fa-user-edit me-2"></i>
            Modifier l'utilisateur
        </h1>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-user-cog me-2"></i>
                        Informations de l'utilisateur
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom complet *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Rôle *</label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" name="role" required onchange="toggleModules()">
                                    <option value="">Sélectionner un rôle</option>
                                    <option value="superadmin" {{ old('role', $user->role) === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="moderator" {{ old('role', $user->role) === 'moderator' ? 'selected' : '' }}>Modérateur</option>
                                    <option value="agent" {{ old('role', $user->role) === 'agent' ? 'selected' : '' }}>Agent</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Modules du modérateur -->
                        <div id="moderatorModulesEdit" class="mb-3" style="{{ old('role', $user->role) === 'moderator' ? '' : 'display: none;' }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold mb-0">
                                    <i class="fas fa-user-shield me-1 text-info"></i>Modules du Modérateur
                                </label>
                                @if($user->role === 'moderator')
                                <a href="{{ route('submodules.edit', $user->id) }}" 
                                   class="btn btn-sm btn-outline-info"
                                   title="Gérer les sous-modules détaillés">
                                    <i class="fas fa-cogs me-1"></i>Sous-modules
                                </a>
                                @endif
                            </div>
                            <p class="text-muted small mb-2">Modules de base : <span class="badge bg-success">Opérations</span> <span class="badge bg-success">Validations</span></p>
                            <p class="text-muted small mb-2">Cochez les modules supplémentaires :</p>
                            @php
                                $moderatorAvailableModules = [
                                    'treasury' => ['label' => 'Trésorerie', 'icon' => 'fa-coins'],
                                    'accounting' => ['label' => 'Comptabilité', 'icon' => 'fa-calculator'],
                                    'hr' => ['label' => 'Ressources Humaines', 'icon' => 'fa-users-cog'],
                                    'fleet' => ['label' => 'Matériel Roulant', 'icon' => 'fa-truck-loading'],
                                    'suppliers' => ['label' => 'Fournisseurs', 'icon' => 'fa-industry'],
                                    'warehouse' => ['label' => 'Entrepôt / Magasin', 'icon' => 'fa-warehouse'],
                                    'commercial' => ['label' => 'Commercial', 'icon' => 'fa-briefcase'],
                                    'projects' => ['label' => 'Projets', 'icon' => 'fa-project-diagram'],
                                    'reporting' => ['label' => 'Reporting', 'icon' => 'fa-chart-line'],
                                    'audit' => ['label' => 'Checking / Audit', 'icon' => 'fa-clipboard-check'],
                                ];
                                $userPermissions = \DB::table('model_has_permissions')
                                    ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
                                    ->where('model_has_permissions.model_id', $user->id)
                                    ->where('model_has_permissions.model_type', 'App\\Models\\User')
                                    ->pluck('permissions.name')
                                    ->toArray();
                            @endphp
                            <div class="row">
                                @foreach($moderatorAvailableModules as $moduleKey => $moduleInfo)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="modules[]" 
                                               value="{{ $moduleKey }}" 
                                               id="module_{{ $moduleKey }}"
                                               {{ in_array($moduleKey, $userPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module_{{ $moduleKey }}">
                                            <i class="fas {{ $moduleInfo['icon'] }} me-1"></i>
                                            {{ $moduleInfo['label'] }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleModules() {
    const role = document.getElementById('role').value;
    const moderatorModules = document.getElementById('moderatorModulesEdit');
    
    if (role === 'moderator') {
        moderatorModules.style.display = 'block';
    } else {
        moderatorModules.style.display = 'none';
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    toggleModules();
});
</script>
@endsection
