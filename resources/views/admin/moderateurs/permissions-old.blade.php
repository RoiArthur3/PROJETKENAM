@extends('layouts.app')

@section('title', 'Gestion des permissions - ' . $moderator->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-shield me-2 text-info"></i>
                Permissions du modérateur : {{ $moderator->name }}
            </h1>
            <p class="text-muted mb-0">
                Email : {{ $moderator->email }} | 
                Modules actuels : {{ implode(', ', json_decode($moderator->modules ?? '[]') ?: ['Aucun']) }}
            </p>
        </div>
        <div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('users.update-submodules', $moderator->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Modules disponibles -->
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-cogs me-2"></i>
                            Modules et sous-modules autorisés
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $allModules = config('submodules');
                            $allowedModules = json_decode($moderator->modules ?? '[]', true);
                            $allowedSubmodules = json_decode($moderator->submodules ?? '[]', true);
                        @endphp

                        @foreach($allModules as $moduleKey => $moduleData)
                        <div class="module-section mb-4" style="border-left: 4px solid #007bff; padding-left: 15px;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="form-check form-switch me-3">
                                    <input class="form-check-input module-toggle" type="checkbox" 
                                           name="modules[]" 
                                           value="{{ $moduleKey }}" 
                                           id="module_{{ $moduleKey }}"
                                           data-module="{{ $moduleKey }}"
                                           onchange="toggleSubmodules('{{ $moduleKey }}')"
                                           {{ in_array($moduleKey, $allowedModules) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="module_{{ $moduleKey }}">
                                        <i class="{{ $moduleData['icon'] }} me-2"></i>
                                        {{ $moduleData['name'] }}
                                    </label>
                                </div>
                                <small class="text-muted">
                                    <span class="selected-count" id="count_{{ $moduleKey }}">
                                        {{ count(array_intersect(array_keys($moduleData['submodules']), $allowedSubmodules)) }}/{{ count($moduleData['submodules']) }}
                                    </span>
                                    sous-modules sélectionnés
                                </small>
                            </div>

                            <!-- Sous-modules -->
                            <div class="submodules-container ms-4" 
                                 id="submodules_{{ $moduleKey }}"
                                 style="{{ in_array($moduleKey, $allowedModules) ? '' : 'display: none;' }}">
                                <div class="row">
                                    @foreach($moduleData['submodules'] as $subKey => $subData)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input submodule-checkbox" 
                                                   type="checkbox" 
                                                   name="submodules[]" 
                                                   value="{{ $subKey }}" 
                                                   id="sub_{{ $subKey }}"
                                                   data-module="{{ $moduleKey }}"
                                                   onchange="updateCount('{{ $moduleKey }}')"
                                                   {{ in_array($subKey, $allowedSubmodules) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sub_{{ $subKey }}">
                                                <i class="{{ $subData['icon'] }} me-2 text-muted"></i>
                                                {{ $subData['name'] }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Actions rapides -->
                                <div class="d-flex gap-2 mt-3">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary"
                                            onclick="selectAllSubmodules('{{ $moduleKey }}')">
                                        <i class="fas fa-check-double me-1"></i>Tout sélectionner
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary"
                                            onclick="deselectAllSubmodules('{{ $moduleKey }}')">
                                        <i class="fas fa-times me-1"></i>Tout désélectionner
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <!-- Actions globales -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                <button type="button" class="btn btn-outline-primary" onclick="selectAllModules()">
                                    <i class="fas fa-check-double me-2"></i>Tout sélectionner
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetAll()">
                                    <i class="fas fa-times me-2"></i>Tout réinitialiser
                                </button>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Enregistrer les permissions
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Script simple sans dépendances externes
function toggleSubmodules(moduleKey) {
    const checkbox = document.getElementById('module_' + moduleKey);
    const container = document.getElementById('submodules_' + moduleKey);
    
    if (checkbox.checked) {
        container.style.display = 'block';
        // Cocher tous les sous-modules par défaut
        const submodules = container.querySelectorAll('.submodule-checkbox');
        submodules.forEach(cb => cb.checked = true);
    } else {
        container.style.display = 'none';
        // Décocher tous les sous-modules
        const submodules = container.querySelectorAll('.submodule-checkbox');
        submodules.forEach(cb => cb.checked = false);
    }
    updateCount(moduleKey);
}

function selectAllSubmodules(moduleKey) {
    const container = document.getElementById('submodules_' + moduleKey);
    const submodules = container.querySelectorAll('.submodule-checkbox');
    submodules.forEach(cb => cb.checked = true);
    updateCount(moduleKey);
}

function deselectAllSubmodules(moduleKey) {
    const container = document.getElementById('submodules_' + moduleKey);
    const submodules = container.querySelectorAll('.submodule-checkbox');
    submodules.forEach(cb => cb.checked = false);
    updateCount(moduleKey);
}

function updateCount(moduleKey) {
    const container = document.getElementById('submodules_' + moduleKey);
    const total = container.querySelectorAll('.submodule-checkbox').length;
    const checked = container.querySelectorAll('.submodule-checkbox:checked').length;
    document.getElementById('count_' + moduleKey).textContent = checked + '/' + total;
}

function selectAllModules() {
    document.querySelectorAll('.module-toggle').forEach(toggle => {
        toggle.checked = true;
        toggleSubmodules(toggle.dataset.module);
    });
}

function resetAll() {
    document.querySelectorAll('.module-toggle').forEach(toggle => {
        toggle.checked = false;
        toggleSubmodules(toggle.dataset.module);
    });
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Mettre à jour tous les compteurs
    document.querySelectorAll('.module-toggle').forEach(toggle => {
        updateCount(toggle.dataset.module);
    });
});
</script>
@endsection
