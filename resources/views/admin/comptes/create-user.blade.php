@extends('layouts.app')

@section('title', 'Créer un Utilisateur | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Créer un Utilisateur</h5>
            <a href="{{ route('admin.comptes.index') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left me-1"></i>Retour</a>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Création universelle :</strong> Choisissez le type de compte selon les permissions nécessaires.
                <ul class="mb-0 mt-2">
                    <li><strong>Agent :</strong> Uniquement Opérations et Validations. <span class="badge bg-success">Créé aussi dans RH pour la paie</span></li>
                    <li><strong>Modérateur :</strong> Plusieurs modules au choix</li>
                    <li><strong>Admin :</strong> Modérateur sans dashboard général</li>
                    <li><strong>Super Admin :</strong> Accès total à tous les modules</li>
                </ul>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.comptes.users.store') }}" class="row g-3">
                @csrf

                <!-- Informations de base -->
                <div class="col-12">
                    <h6 class="text-muted mb-3"><i class="fas fa-user me-2"></i>Informations de base</h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Nom complet <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           name="name" value="{{ old('name') }}" required placeholder="Ex: Jean Dupont">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required placeholder="Ex: user@kenamservices.net">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Téléphone <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                           name="phone" value="{{ old('phone') }}" required placeholder="Ex: +225 07 00 00 00 00">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Mot de passe <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required placeholder="Min 8 caractères">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Confirmer mot de passe <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                           name="password_confirmation" required placeholder="Confirmer le mot de passe">
                    @error('password_confirmation')
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
                        <option value="agent" {{ old('role') == 'agent' ? 'selected' : '' }}>Agent</option>
                        <option value="moderator" {{ old('role') == 'moderator' ? 'selected' : '' }}>Modérateur</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Service <span id="serviceRequired" class="text-danger" style="display: none;">*</span></label>
                    <select class="form-select @error('service_id') is-invalid @enderror" name="service_id" id="serviceSelect">
                        <option value="">Aucun service</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Validation du compte -->
                <div class="col-12">
                    <h6 class="text-muted mb-3 mt-4"><i class="fas fa-shield-alt me-2"></i>Validation du compte</h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Statut du compte</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_active" id="is_active_yes" value="1" {{ old('is_active', 1) == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active_yes">
                            <i class="fas fa-check-circle text-success me-2"></i>Compte actif
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_active" id="is_active_no" value="0" {{ old('is_active', 1) == 0 ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active_no">
                            <i class="fas fa-times-circle text-danger me-2"></i>Compte inactif
                        </label>
                    </div>
                    <small class="text-muted">Un compte inactif ne pourra pas se connecter</small>
                </div>

                <!-- Modules (pour modérateur et admin) -->
                <div class="col-12" id="modulesSection" style="display: none;">
                    <h6 class="text-muted mb-3 mt-4"><i class="fas fa-cogs me-2"></i>Modules autorisés</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">Modules disponibles</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="modules[]" value="dashboard" id="module_dashboard" {{ in_array('dashboard', old('modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module_dashboard">
                                            <i class="fas fa-tachometer-alt text-success me-2"></i>Dashboard
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="modules[]" value="operations" id="module_operations" {{ in_array('operations', old('modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module_operations">
                                            <i class="fas fa-cogs text-primary me-2"></i>Opérations
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="modules[]" value="validations" id="module_validations" {{ in_array('validations', old('modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module_validations">
                                            <i class="fas fa-tasks text-info me-2"></i>Validations
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="modules[]" value="stock" id="module_stock" {{ in_array('stock', old('modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module_stock">
                                            <i class="fas fa-warehouse text-warning me-2"></i>Stock
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="modules[]" value="commercial" id="module_commercial" {{ in_array('commercial', old('modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module_commercial">
                                            <i class="fas fa-briefcase text-warning me-2"></i>Commercial
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">Modules avancés</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="modules[]" value="admin" id="module_admin" {{ in_array('admin', old('modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module_admin">
                                            <i class="fas fa-user-shield text-danger me-2"></i>Admin
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="modules[]" value="comptabilite" id="module_comptabilite" {{ in_array('comptabilite', old('modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module_comptabilite">
                                            <i class="fas fa-calculator text-info me-2"></i>Comptabilité
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="modules[]" value="rh" id="module_rh" {{ in_array('rh', old('modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module_rh">
                                            <i class="fas fa-users-cog text-danger me-2"></i>RH
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="modules[]" value="parc" id="module_parc" {{ in_array('parc', old('modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module_parc">
                                            <i class="fas fa-truck text-purple me-2"></i>Parc Auto
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="modules[]" value="tresorerie" id="module_tresorerie" {{ in_array('tresorerie', old('modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module_tresorerie">
                                            <i class="fas fa-money-bill-wave text-success me-2"></i>Trésorerie
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="col-12 mt-4">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.comptes.index') }}" class="btn btn-light">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Créer l'utilisateur
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('roleSelect');
    const modulesSection = document.getElementById('modulesSection');
    const serviceRequired = document.getElementById('serviceRequired');
    const serviceSelect = document.getElementById('serviceSelect');

    function updateFormVisibility() {
        const role = roleSelect.value;

        const allModuleCheckboxes = modulesSection.querySelectorAll('input[type="checkbox"]');

        // Gérer la visibilité et l'état des modules en fonction du rôle
        if (role === 'moderator' || role === 'admin') {
            modulesSection.style.display = 'block';
            allModuleCheckboxes.forEach(cb => cb.disabled = false);

            const dashboardCheckbox = document.getElementById('module_dashboard');
            if (role === 'admin') {
                dashboardCheckbox.disabled = true;
                dashboardCheckbox.checked = false;
            }
        } else if (role === 'superadmin') {
            modulesSection.style.display = 'block';
            allModuleCheckboxes.forEach(cb => {
                cb.checked = true;
                cb.disabled = true;
            });
        } else {
            modulesSection.style.display = 'none';
        }

        // Afficher service comme obligatoire pour les agents
        if (role === 'agent') {
            serviceRequired.style.display = 'inline';
            serviceSelect.setAttribute('required', 'required');
        } else {
            serviceRequired.style.display = 'none';
            serviceSelect.removeAttribute('required');
        }
    }

    roleSelect.addEventListener('change', updateFormVisibility);
    updateFormVisibility(); // Initialisation
});
</script>
@endsection
