@extends('layouts.app')

@section('title', 'Administration - Modifier un utilisateur')

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-edit me-2"></i>Modifier l'utilisateur: {{ $user->name }}
                    </h6>
                    <div>
                        @if($user->role === 'moderator' || $user->role === 'moderateur')
                            <a href="{{ route('admin.comptes.users.submodules.edit', $user) }}" class="btn btn-sm btn-info text-white me-2">
                                <i class="fas fa-user-shield me-1"></i>Permissions détaillées
                            </a>
                        @endif
                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }} me-2">
                            {{ $user->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                        <span class="badge bg-warning text-dark">Édition</span>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        {{-- =========================================
                             SECTION 1 : Informations générales
                        ========================================= --}}
                        <div class="row mb-4">
                            <div class="col-12 mb-3">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-id-card me-2"></i>Informations générales
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name"
                                           placeholder="Ex: Jean Dupont"
                                           required
                                           value="{{ old('name', $user->name) }}">
                                </div>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email"
                                           placeholder="exemple@kenam.ci"
                                           required
                                           value="{{ old('email', $user->email) }}">
                                </div>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Téléphone : champ "telephone" = celui utilisé pour la connexion --}}
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">
                                    Numéro de téléphone <span class="text-danger">*</span>
                                    <small class="text-muted">(utilisé pour la connexion)</small>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">🇨🇮 +225</span>
                                    <input type="tel"
                                           class="form-control @error('telephone') is-invalid @enderror"
                                           id="telephone" name="telephone"
                                           placeholder="0700000000"
                                           maxlength="10"
                                           required
                                           value="{{ old('telephone', $user->telephone ?: $user->phone) }}">
                                </div>
                                @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="form-text text-info">
                                    <i class="fas fa-info-circle me-1"></i>Format: 10 chiffres (ex: 0700000000)
                                </small>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="role_id" class="form-label">Rôle <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                    <select class="form-select @error('role') is-invalid @enderror"
                                            id="role_id" name="role" required>
                                        <option value="">Sélectionner un rôle</option>
                                        <option value="moderator"  {{ in_array(old('role', $user->role), ['moderator','moderateur']) ? 'selected' : '' }}>Modérateur</option>
                                        <option value="admin"      {{ old('role', $user->role) == 'admin'      ? 'selected' : '' }}>Admin</option>
                                        <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                    </select>
                                </div>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3 d-flex align-items-end">
                                <div class="form-check form-switch ms-2 mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_active">Compte actif</label>
                                </div>
                            </div>
                        </div>

                        {{-- =========================================
                             SECTION 2 : Mot de passe
                        ========================================= --}}
                        <div class="row mb-4">
                            <div class="col-12 mb-3">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-lock me-2"></i>Sécurité
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Nouveau mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password"
                                           placeholder="Laisser vide pour conserver">
                                </div>
                                <div class="form-text">Minimum 6 caractères. Laisser vide pour ne pas changer.</div>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           placeholder="Répéter le mot de passe">
                                </div>
                            </div>
                        </div>

                        {{-- =========================================
                             SECTION 3 : Permissions & Modules
                             Visible uniquement pour moderator/admin
                        ========================================= --}}
                        <div class="row mb-4" id="permissionsSection" style="display: none;">
                            <div class="col-12 mb-2">
                                <h5 class="text-primary border-bottom pb-2">
                                        <i class="fas fa-shield-alt me-2"></i>Services, métiers et sous-modules
                                </h5>
                                <div class="alert alert-warning small mb-3">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                        <strong>Admin :</strong> lorsqu'un service, métier ou module est coché, tous ses sous-modules sont autorisés automatiquement.
                                        <strong>Modérateur :</strong> les sous-modules doivent être cochés manuellement, avec au moins un sous-module par service, métier ou module sélectionné.
                                </div>
                            </div>

                            @php
                                $allowedSubmodules = is_array($user->submodules) ? $user->submodules : [];
                            @endphp

                            {{-- Affichage via config/submodules.php si disponible --}}
                            @if(config('submodules') && count(config('submodules')) > 0)
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle table-sm">
                                            <thead class="table-light text-center">
                                                <tr>
                                                    <th width="28%">Service / métier / module</th>
                                                    <th width="72%">Sous-modules à administrer</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(config('submodules', []) as $moduleKey => $moduleData)
                                                <tr>
                                                    <td class="bg-light">
                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                   name="modules[]"
                                                                   value="{{ $moduleKey }}"
                                                                   class="form-check-input module-checkbox"
                                                                   id="module_{{ $moduleKey }}"
                                                                   {{ (old('modules') && in_array($moduleKey, old('modules'))) || ($currentPermissions[$moduleKey] ?? false) ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold" for="module_{{ $moduleKey }}">
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
                                                                           id="sub_{{ $subKey }}"
                                                                           {{ (old('submodules') && in_array($subKey, old('submodules'))) || in_array($subKey, $allowedSubmodules) ? 'checked' : '' }}>
                                                                    <label class="form-check-label small" for="sub_{{ $subKey }}">
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

                            @elseif(isset($allModules) && count($allModules) > 0)
                                {{-- Fallback : liste simple (version en ligne sans config/submodules.php) --}}
                                <div class="col-12">
                                    <div class="row">
                                        @foreach($allModules as $moduleKey => $module)
                                        <div class="col-md-4 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       name="modules[]"
                                                       value="{{ $moduleKey }}"
                                                       id="module_{{ $moduleKey }}"
                                                       {{ (old('modules') && in_array($moduleKey, old('modules'))) || ($currentPermissions[$moduleKey] ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="module_{{ $moduleKey }}">
                                                    <i class="fas fa-cube me-1 text-primary"></i>
                                                    {{ is_array($module) ? ($module['label'] ?? $module['name'] ?? $moduleKey) : $module }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- =========================================
                             BOUTONS D'ACTION
                        ========================================= --}}
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye me-2"></i>Voir les détails
                                    </a>
                                    <div>
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect  = document.getElementById('role_id');
    const permSection = document.getElementById('permissionsSection');

    function togglePermissions(animate) {
        const role = roleSelect.value;
        const show = ['moderator', 'moderateur', 'admin'].includes(role);
        if (show) {
            permSection.style.display = 'block';
            if (role === 'admin') {
                document.querySelectorAll('.module-checkbox:checked').forEach(function(modCb) {
                    const moduleKey = modCb.value;
                    document.querySelectorAll('.submodule-checkbox[data-module="' + moduleKey + '"]')
                        .forEach(function(sub) { sub.checked = true; });
                });
            }
        } else {
            permSection.style.display = 'none';
        }
    }

    roleSelect.addEventListener('change', togglePermissions);
    togglePermissions(); // Au chargement

    // Cocher auto le module parent quand on coche un sous-module
    document.querySelectorAll('.submodule-checkbox').forEach(function(cb) {
        cb.addEventListener('change', function() {
            if (this.checked) {
                const moduleKey = this.dataset.module;
                const parentCb = document.getElementById('module_' + moduleKey);
                if (parentCb) parentCb.checked = true;
            }
        });
    });

    // Décocher tous les sous-modules si on décoche le module parent
    document.querySelectorAll('.module-checkbox').forEach(function(cb) {
        cb.addEventListener('change', function() {
            const role = roleSelect.value;
            if (this.checked && role === 'admin') {
                const moduleKey = this.value;
                document.querySelectorAll('.submodule-checkbox[data-module="' + moduleKey + '"]')
                    .forEach(function(sub) { sub.checked = true; });
                return;
            }

            if (!this.checked) {
                const moduleKey = this.value;
                document.querySelectorAll('.submodule-checkbox[data-module="' + moduleKey + '"]')
                    .forEach(function(sub) { sub.checked = false; });
            }
        });
    });

    // Validation métier: pour modérateur, chaque module coché doit avoir au moins un sous-module.
    const form = document.querySelector('form[action*="/admin/users/"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const role = roleSelect.value;
            if (!(role === 'moderator' || role === 'moderateur')) {
                return;
            }

            let invalidModuleLabel = null;
            document.querySelectorAll('.module-checkbox:checked').forEach(function(modCb) {
                const moduleKey = modCb.value;
                const checkedSubs = document.querySelectorAll('.submodule-checkbox[data-module="' + moduleKey + '"]:checked').length;
                if (checkedSubs === 0 && !invalidModuleLabel) {
                    const labelEl = document.querySelector('label[for="module_' + moduleKey + '"]');
                    invalidModuleLabel = labelEl ? labelEl.textContent.trim() : moduleKey;
                }
            });

            if (invalidModuleLabel) {
                e.preventDefault();
                alert('Pour le rôle Modérateur, chaque module coché doit avoir au moins un sous-module sélectionné. Module concerné: ' + invalidModuleLabel);
            }
        });
    }
});
</script>
@endpush

@endsection
