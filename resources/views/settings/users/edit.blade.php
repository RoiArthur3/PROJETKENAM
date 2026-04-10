@extends('layouts.app')

@section('title', 'Modifier un Utilisateur - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-edit me-2 text-primary"></i>Modifier un Utilisateur
            </h1>
            <p class="text-muted mb-0">Modification du compte de {{ $user->name }}</p>
        </div>
        <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
        </a>
    </div>

    <!-- Alertes -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Erreur de validation :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulaire -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations de l'utilisateur</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Informations de base -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    Nom complet <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">
                                    Numéro de téléphone <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $user->telephone) }}"
                                       required pattern="[0-9]{10}" title="Entrez 10 chiffres"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">10 chiffres (ex: 0123456789)</div>
                            </div>
                        </div>

                        <!-- Email et mot de passe -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    Adresse email <small class="text-muted">(facultatif)</small>
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}"
                                       placeholder="email@exemple.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    Nouveau mot de passe <small class="text-muted">(laisser vide pour conserver)</small>
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimum 8 caractères</div>
                            </div>
                        </div>

                        <!-- Confirmation mot de passe -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">
                                    Confirmer le nouveau mot de passe
                                </label>
                                <input type="password" class="form-control" id="password_confirmation"
                                       name="password_confirmation">
                            </div>
                        </div>

                        <!-- Rôle -->
                        <div class="mb-3">
                            <label for="role" class="form-label">Rôle <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="" disabled>Sélectionnez un rôle</option>
                                @foreach($roles as $key => $label)
                                    <option value="{{ $key }}" {{ old('role', $user->role) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Modules et sous-modules (admin/modérateur) -->
                        @php
                            $selectedModules = is_array(old('modules')) ? old('modules') : (is_array($user->modules) ? $user->modules : []);
                            $selectedSubmodules = is_array(old('submodules')) ? old('submodules') : (is_array($user->submodules) ? $user->submodules : []);
                        @endphp
                        <div id="modulesEditSection" class="mb-3" style="{{ in_array(old('role', $user->role), ['admin', 'moderator']) ? '' : 'display: none;' }}">
                            <label class="form-label fw-bold mb-2">
                                <i class="fas fa-user-shield me-1 text-info"></i>Modules et sous-modules
                            </label>
                            <div class="alert alert-info small mb-2">
                                Admin = coche un module => tous ses sous-modules sont cochés automatiquement.
                                Modérateur = sous-modules à choisir manuellement.
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle table-sm">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th width="28%">Module</th>
                                            <th width="72%">Sous-modules</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(($allModules ?? []) as $moduleKey => $moduleData)
                                        <tr>
                                            <td class="bg-light">
                                                <div class="form-check">
                                                    <input type="checkbox"
                                                           name="modules[]"
                                                           value="{{ $moduleKey }}"
                                                           class="form-check-input module-checkbox"
                                                           id="edit_module_{{ $moduleKey }}"
                                                           {{ in_array($moduleKey, $selectedModules) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="edit_module_{{ $moduleKey }}">
                                                        <i class="{{ $moduleData['icon'] ?? 'fas fa-cube' }} me-1 text-primary"></i>
                                                        {{ $moduleData['name'] ?? $moduleKey }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row g-1">
                                                    @foreach($moduleData['submodules'] ?? [] as $subKey => $subData)
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                   name="submodules[]"
                                                                   value="{{ $subKey }}"
                                                                   class="form-check-input submodule-checkbox"
                                                                   data-module="{{ $moduleKey }}"
                                                                   id="edit_sub_{{ $subKey }}"
                                                                   {{ in_array($subKey, $selectedSubmodules) ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="edit_sub_{{ $subKey }}">
                                                                <i class="{{ $subData['icon'] ?? 'fas fa-dot-circle' }} me-1 text-muted"></i>
                                                                {{ $subData['name'] ?? $subKey }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Informations système (lecture seule) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Date de création</label>
                                <input type="text" class="form-control" value="{{ $user->created_at->format('d/m/Y H:i') }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dernière connexion</label>
                                <input type="text" class="form-control"
                                       value="{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais connecté' }}"
                                       readonly>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Mettre à jour l'utilisateur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function currentRole() {
    return document.getElementById('role')?.value || '';
}

function toggleModulesEditSection() {
    const section = document.getElementById('modulesEditSection');
    const role = currentRole();
    if (!section) return;

    if (role === 'admin' || role === 'moderator') {
        section.style.display = '';
        if (role === 'admin') {
            document.querySelectorAll('.module-checkbox:checked').forEach(function(modCb) {
                const moduleKey = modCb.value;
                document.querySelectorAll('.submodule-checkbox[data-module="' + moduleKey + '"]')
                    .forEach(function(subCb) { subCb.checked = true; });
            });
        }
    } else {
        section.style.display = 'none';
        document.querySelectorAll('.module-checkbox, .submodule-checkbox').forEach(function(cb) {
            cb.checked = false;
        });
    }
}

document.getElementById('role').addEventListener('change', toggleModulesEditSection);

// Validation côté client pour la confirmation du mot de passe
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;

    if (password && password !== confirmPassword) {
        this.setCustomValidity('Les mots de passe ne correspondent pas');
    } else {
        this.setCustomValidity('');
    }
});

document.querySelectorAll('.submodule-checkbox').forEach(function(subCb) {
    subCb.addEventListener('change', function() {
        if (this.checked) {
            const moduleKey = this.dataset.module;
            const parent = document.getElementById('edit_module_' + moduleKey);
            if (parent) parent.checked = true;
        }
    });
});

document.querySelectorAll('.module-checkbox').forEach(function(modCb) {
    modCb.addEventListener('change', function() {
        const role = currentRole();
        const moduleKey = this.value;

        if (this.checked && role === 'admin') {
            document.querySelectorAll('.submodule-checkbox[data-module="' + moduleKey + '"]')
                .forEach(function(subCb) { subCb.checked = true; });
            return;
        }

        if (!this.checked) {
            document.querySelectorAll('.submodule-checkbox[data-module="' + moduleKey + '"]')
                .forEach(function(subCb) { subCb.checked = false; });
        }
    });
});

document.querySelector('form[action*="settings/users/"]')?.addEventListener('submit', function(e) {
    if (currentRole() !== 'moderator') return;

    let invalidModule = null;
    document.querySelectorAll('.module-checkbox:checked').forEach(function(modCb) {
        const moduleKey = modCb.value;
        const checkedSubs = document.querySelectorAll('.submodule-checkbox[data-module="' + moduleKey + '"]:checked').length;
        if (checkedSubs === 0 && !invalidModule) {
            invalidModule = moduleKey;
        }
    });

    if (invalidModule) {
        e.preventDefault();
        alert('Pour le rôle Modérateur, chaque module coché doit avoir au moins un sous-module. Module: ' + invalidModule);
    }
});

toggleModulesEditSection();
</script>
@endsection
