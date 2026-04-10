@extends('layouts.app')

@section('title', 'Modifier l\'utilisateur | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Modifier l'utilisateur</h5>
            <a href="{{ route('admin.comptes.permissions', ['user_id' => $user->id]) }}" class="btn btn-sm btn-light">
                <i class="fas fa-arrow-left me-1"></i>Retour aux permissions
            </a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.comptes.update', $user) }}" class="row g-3">
                @csrf
                @method('PUT')

                <!-- Informations de base -->
                <div class="col-12">
                    <h6 class="text-muted mb-3"><i class="fas fa-user me-2"></i>Informations de base</h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Nom complet <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           name="name" value="{{ old('name', $user->name) }}" required placeholder="Ex: Jean Dupont">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email', $user->email) }}" required placeholder="Ex: user@kenamservices.net">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Téléphone <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                           name="phone" value="{{ old('phone', $user->phone) }}" required placeholder="Ex: +225 07 00 00 00 00">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Service</label>
                    <select class="form-select @error('service_id') is-invalid @enderror" name="service_id">
                        <option value="">Sélectionner un service...</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id', $user->service_id) == $service->id ? 'selected' : '' }}>
                                {{ $service->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Type de compte -->
                <div class="col-12">
                    <h6 class="text-muted mb-3 mt-4"><i class="fas fa-user-tag me-2"></i>Type de compte</h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Rôle <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" name="role" id="roleSelect" required>
                        <option value="">Sélectionner un rôle...</option>
                        <option value="agent" {{ old('role', $user->role) == 'agent' ? 'selected' : '' }}>Agent</option>
                        <option value="moderator" {{ old('role', $user->role) == 'moderator' ? 'selected' : '' }}>Modérateur</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Statut du compte</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">
                            Compte actif
                        </label>
                    </div>
                </div>

                <!-- Modules (pour modérateur et admin) -->
                <div class="col-12" id="modulesSection">
                    <h6 class="text-muted mb-3 mt-4"><i class="fas fa-cogs me-2"></i>Modules autorisés</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">Modules disponibles</h6>
                                </div>
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($modules as $module)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="modules[]"
                                                   value="{{ $module }}" id="module_{{ $module }}"
                                                   {{ is_array($userModules) && in_array($module, $userModules) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="module_{{ $module }}">
                                                {{ ucfirst($module) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">Modules avancés</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info small">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Les permissions sont automatiquement appliquées selon le rôle sélectionné.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="col-12 mt-4">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.comptes.permissions', ['user_id' => $user->id]) }}" class="btn btn-light">
                            <i class="fas fa-times me-1"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('roleSelect');
    const modulesSection = document.getElementById('modulesSection');

    function toggleModulesSection() {
        const selectedRole = roleSelect.value;
        if (selectedRole === 'moderator' || selectedRole === 'admin') {
            modulesSection.style.display = 'block';
        } else {
            modulesSection.style.display = 'none';
        }
    }

    roleSelect.addEventListener('change', toggleModulesSection);
    toggleModulesSection(); // Initial check
});
</script>
@endpush
@endsection
