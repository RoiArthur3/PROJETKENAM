@extends('layouts.app')

@section('title', 'Gestion des Permissions Admin')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        Gestion des Permissions Admin
                    </h1>
                    <p class="text-muted mb-0">Configurez les droits d'accès pour l'administrateur</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire de configuration -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Configuration des Permissions
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.permissions.update') }}">
                        @csrf
                        @method('PUT')

                        <!-- Option d'accès complet -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" name="admin_has_full_access"
                                           id="admin_has_full_access" value="1"
                                           {{ $config['admin_has_full_access'] ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="admin_has_full_access">
                                        <i class="fas fa-unlock-alt me-2"></i>
                                        Accès complet à tous les modules
                                    </label>
                                    <div class="form-text">
                                        Si activé, l'administrateur aura accès à tous les modules sans restriction.
                                        Si désactivé, vous pouvez spécifier les modules à restreindre ci-dessous.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modules restreints -->
                        <div class="row" id="restrictedModulesSection">
                            <div class="col-12">
                                <h6 class="fw-semibold mb-3">
                                    <i class="fas fa-ban me-2"></i>
                                    Modules Restreints
                                </h6>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Sélectionnez les modules que l'administrateur ne pourra pas voir.
                                </div>

                                <div class="row g-3">
                                    @foreach($allModules as $module)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       name="restricted_modules[]"
                                                       value="{{ $module }}"
                                                       id="module_{{ $module }}"
                                                       {{ in_array($module, $config['restricted_modules']) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="module_{{ $module }}">
                                                    <i class="fas fa-cube me-1"></i>
                                                    {{ ucfirst($module) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Enregistrer les modifications
                                    </button>
                                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Retour
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Aperçu des permissions actuelles -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-eye me-2"></i>
                        Aperçu des Permissions Actuelles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-success mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                Modules Accessibles
                            </h6>
                            <div class="row g-2">
                                @php
                                    $accessibleModules = array_diff($allModules, $config['restricted_modules']);
                                @endphp
                                @foreach($accessibleModules as $module)
                                    <div class="col-6">
                                        <div class="d-flex align-items-center p-2 bg-success bg-opacity-10 border border-success rounded">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <small class="fw-medium">{{ ucfirst($module) }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-danger mb-3">
                                <i class="fas fa-times-circle me-2"></i>
                                Modules Restreints
                            </h6>
                            <div class="row g-2">
                                @foreach($config['restricted_modules'] as $module)
                                    <div class="col-6">
                                        <div class="d-flex align-items-center p-2 bg-danger bg-opacity-10 border border-danger rounded">
                                            <i class="fas fa-times-circle text-danger me-2"></i>
                                            <small class="fw-medium">{{ ucfirst($module) }}</small>
                                        </div>
                                    </div>
                                @endforeach
                                @if(empty($config['restricted_modules']))
                                    <div class="col-12">
                                        <div class="text-muted text-center py-3">
                                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                                            <p>Aucun module restreint</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fullAccessSwitch = document.getElementById('admin_has_full_access');
    const restrictedSection = document.getElementById('restrictedModulesSection');

    function toggleRestrictedSection() {
        if (fullAccessSwitch.checked) {
            restrictedSection.style.opacity = '0.5';
            restrictedSection.style.pointerEvents = 'none';
            // Décocher toutes les cases
            document.querySelectorAll('input[name="restricted_modules[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        } else {
            restrictedSection.style.opacity = '1';
            restrictedSection.style.pointerEvents = 'auto';
        }
    }

    // État initial
    toggleRestrictedSection();

    // Changement de l'interrupteur
    fullAccessSwitch.addEventListener('change', toggleRestrictedSection);
});
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745, #1e7e34) !important;
}

.form-check-lg .form-check-input {
    width: 1.5em;
    height: 1.5em;
}

.form-check-lg .form-check-label {
    font-size: 1.1em;
}
</style>
@endsection
