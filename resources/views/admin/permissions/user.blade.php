@extends('layouts.app')

@section('title', 'Permissions - ' . $user->name)

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="h2 mb-1">
                            <i class="fas fa-shield-alt text-primary me-2"></i>
                            Permissions de {{ $user->name }}
                        </h1>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-{{ $role == 'admin' ? 'danger' : ($role == 'moderator' ? 'warning' : 'primary') }} fs-6">
                                <i class="fas fa-user-tag me-1"></i>
                                {{ ucfirst($role) }}
                            </span>
                            <span class="text-muted">
                                <i class="fas fa-envelope me-1"></i>
                                {{ $user->email }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
                        <i class="fas fa-user me-1"></i>
                        Profil utilisateur
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Formulaire principal -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.permissions.user.update', $user) }}">
                @csrf
                @method('PUT')

                <!-- Statistiques rapides -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 bg-primary text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-cubes fa-2x mb-2"></i>
                                <h4 class="card-title">{{ count($allModules) }}</h4>
                                <p class="card-text mb-0">Modules disponibles</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-success text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h4 class="card-title">{{ count(array_filter($currentPermissions)) }}</h4>
                                <p class="card-text mb-0">Modules actifs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-info text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-percentage fa-2x mb-2"></i>
                                <h4 class="card-title">{{ round((count(array_filter($currentPermissions)) / count($allModules)) * 100) }}%</h4>
                                <p class="card-text mb-0">Taux d'accès</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modules par catégories -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Configuration des Modules
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Modules obligatoires -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-danger mb-3">
                                <i class="fas fa-lock me-2"></i>
                                Modules Obligatoires
                            </h6>
                            <div class="alert alert-danger border-0 bg-danger bg-opacity-10">
                                <i class="fas fa-info-circle me-2"></i>
                                Ce module est automatiquement activé car tous les utilisateurs (sauf superadmin) doivent avoir accès aux requêtes.
                            </div>
                            <div class="row g-3">
                                @foreach($availableModules as $moduleKey => $module)
                                    @if($module['mandatory'])
                                        <div class="col-md-6">
                                            <div class="card border-success bg-success bg-opacity-5">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="form-check me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                   value="{{ $moduleKey }}"
                                                                   checked disabled
                                                                   id="mandatory_{{ $moduleKey }}">
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <label class="form-check-label fw-semibold text-success" for="mandatory_{{ $moduleKey }}">
                                                                <i class="{{ $module['icon'] }} me-2"></i>
                                                                {{ $module['label'] }}
                                                            </label>
                                                            <div class="small text-muted">Obligatoire pour ce rôle</div>
                                                        </div>
                                                        <div class="ms-2">
                                                            <i class="fas fa-lock text-success"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Modules optionnels -->
                        <div>
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-th-large me-2"></i>
                                Modules Optionnels
                            </h6>
                            <div class="alert alert-info border-0 bg-info bg-opacity-10">
                                <i class="fas fa-info-circle me-2"></i>
                                Sélectionnez les modules additionnels que {{ $user->name }} peut administrer. Tous les rôles sont au même niveau.
                            </div>
                            <div class="row g-3">
                                @foreach($availableModules as $moduleKey => $module)
                                    @if(!$module['mandatory'])
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm hover-lift clickable-module" data-module="{{ $moduleKey }}">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="form-check me-3">
                                                            <input class="form-check-input form-check-input-lg module-checkbox" type="checkbox"
                                                                   name="modules[]"
                                                                   value="{{ $moduleKey }}"
                                                                   id="module_{{ $moduleKey }}"
                                                                   {{ ($currentPermissions[$moduleKey] ?? false) ? 'checked' : '' }}>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <label class="form-check-label fw-semibold cursor-pointer" for="module_{{ $moduleKey }}">
                                                                <i class="{{ $module['icon'] }} text-primary me-2"></i>
                                                                {{ $module['label'] }}
                                                            </label>
                                                            <div class="small text-muted module-status">
                                                                @if($currentPermissions[$moduleKey] ?? false)
                                                                    <span class="text-success">
                                                                        <i class="fas fa-check-circle me-1"></i>
                                                                        Actif
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">
                                                                        <i class="fas fa-times-circle me-1"></i>
                                                                        Inactif
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="ms-2">
                                                            @if($currentPermissions[$moduleKey] ?? false)
                                                                <i class="fas fa-toggle-on text-success fa-lg module-toggle"></i>
                                                            @else
                                                                <i class="fas fa-toggle-off text-muted fa-lg module-toggle"></i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row mt-4 pt-4 border-top">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary" onclick="toggleAllModules()">
                                            <i class="fas fa-check-square me-1"></i>
                                            Tout cocher/décocher
                                        </button>
                                        <button type="button" class="btn btn-outline-info ms-2" onclick="resetToDefaults()">
                                            <i class="fas fa-undo me-1"></i>
                                            Réinitialiser
                                        </button>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-times me-1"></i>
                                            Annuler
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save me-1"></i>
                                            Sauvegarder les permissions
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar informations -->
        <div class="col-lg-4">
            <!-- Informations utilisateur -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        Informations Utilisateur
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 80px; height: 80px; font-size: 32px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold">Rôle</label>
                        <div>
                            <span class="badge bg-{{ $role == 'admin' ? 'danger' : ($role == 'moderator' ? 'warning' : 'primary') }} fs-6">
                                {{ ucfirst($role) }}
                            </span>
                        </div>
                    </div>
                    @if($user->phone)
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold">Téléphone</label>
                        <p class="form-control-plaintext">{{ $user->phone }}</p>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold">Dernière mise à jour</label>
                        <p class="form-control-plaintext">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Modules actifs -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        Modules Actifs
                        <span class="badge bg-white text-success ms-2">{{ count(array_filter($currentPermissions)) }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if(!empty(array_filter($currentPermissions)))
                        <div class="list-group list-group-flush">
                            @foreach($currentPermissions as $moduleKey => $hasPermission)
                                @if($hasPermission)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="{{ $allModules[$moduleKey]['icon'] }} text-success me-2"></i>
                                                <span class="fw-medium">{{ $allModules[$moduleKey]['label'] }}</span>
                                            </div>
                                            <span class="badge bg-success bg-opacity-20 text-success">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle text-muted fa-2x mb-2"></i>
                            <p class="text-muted mb-0">Aucun module actif</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="enableAllModules()">
                            <i class="fas fa-check-double me-1"></i>
                            Activer tous les modules
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="disableAllModules()">
                            <i class="fas fa-times me-1"></i>
                            Désactiver tous les modules optionnels
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="copyPermissions()">
                            <i class="fas fa-copy me-1"></i>
                            Copier cette configuration
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
}
.bg-gradient-success {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
}
.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8, #138496) !important;
}
.avatar {
    font-weight: bold;
}
.form-check-input-lg {
    width: 1.5em;
    height: 1.5em;
}
</style>

<script>
function toggleAllModules() {
    const checkboxes = document.querySelectorAll('.module-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);

    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
        updateModuleVisual(checkbox);
    });
}

function enableAllModules() {
    const checkboxes = document.querySelectorAll('.module-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
        updateModuleVisual(checkbox);
    });
}

function disableAllModules() {
    const checkboxes = document.querySelectorAll('.module-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
        updateModuleVisual(checkbox);
    });
}

function resetToDefaults() {
    // Réinitialiser aux valeurs par défaut selon le rôle
    location.reload();
}

function copyPermissions() {
    const checkboxes = document.querySelectorAll('.module-checkbox:checked');
    const modules = Array.from(checkboxes).map(cb => cb.value);

    // Copier dans le presse-papiers
    const text = `Configuration permissions pour {{ $user->name }} ({{ $role }}):\n${modules.join('\n')}`;
    navigator.clipboard.writeText(text).then(() => {
        alert('Configuration copiée dans le presse-papiers !');
    });
}

function updateModuleVisual(checkbox) {
    const card = checkbox.closest('.clickable-module');
    const toggle = card.querySelector('.module-toggle');
    const status = card.querySelector('.module-status span');

    if (checkbox.checked) {
        if (toggle) {
            toggle.className = 'fas fa-toggle-on text-success fa-lg module-toggle';
        }
        if (status) {
            status.className = 'text-success';
            status.innerHTML = '<i class="fas fa-check-circle me-1"></i>Actif';
        }
        // Ajouter une classe pour le style
        card.classList.add('border-success');
        card.classList.remove('border-light');
    } else {
        if (toggle) {
            toggle.className = 'fas fa-toggle-off text-muted fa-lg module-toggle';
        }
        if (status) {
            status.className = 'text-muted';
            status.innerHTML = '<i class="fas fa-times-circle me-1"></i>Inactif';
        }
        // Retirer la classe de style
        card.classList.remove('border-success');
        card.classList.add('border-light');
    }
}

// Initialisation des événements
document.addEventListener('DOMContentLoaded', function() {
    // Événements sur les checkboxes
    document.querySelectorAll('.module-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            updateModuleVisual(this);
        });

        // Rendre la carte cliquable
        const card = checkbox.closest('.clickable-module');
        if (card) {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('input[type="checkbox"]')) {
                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                        updateModuleVisual(checkbox);
                    }
                }
            });
        }
    });

    // Style des cartes cliquables
    document.querySelectorAll('.clickable-module').forEach(function(card) {
        card.style.cursor = 'pointer';
    });
});
</script>
@endsection
