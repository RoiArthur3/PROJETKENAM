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
                        <div class="accordion mb-3" id="accordion_{{ $moduleKey }}">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading_{{ $moduleKey }}">
                                    <div class="d-flex align-items-center w-100 p-2">
                                        <div class="form-check form-switch me-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="modules[]" 
                                                   value="{{ $moduleKey }}" 
                                                   id="module_{{ $moduleKey }}"
                                                   {{ in_array($moduleKey, $allowedModules) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="module_{{ $moduleKey }}">
                                                <i class="{{ $moduleData['icon'] }} me-2"></i>
                                                {{ $moduleData['name'] }}
                                            </label>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary ms-auto" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse_{{ $moduleKey }}"
                                                aria-expanded="true" 
                                                aria-controls="collapse_{{ $moduleKey }}">
                                            <i class="fas fa-chevron-down me-1"></i>
                                            Voir les sous-modules
                                        </button>
                                    </div>
                                </h2>
                                <div id="collapse_{{ $moduleKey }}" 
                                     class="accordion-collapse collapse {{ in_array($moduleKey, $allowedModules) ? 'show' : '' }}" 
                                     aria-labelledby="heading_{{ $moduleKey }}" 
                                     data-bs-parent="#accordion_{{ $moduleKey }}">
                                    <div class="accordion-body">
                                        <div class="row">
                                            @foreach($moduleData['submodules'] as $subKey => $subData)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="submodules[]" 
                                                           value="{{ $subKey }}" 
                                                           id="sub_{{ $subKey }}"
                                                           {{ in_array($subKey, $allowedSubmodules) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="sub_{{ $subKey }}">
                                                        <i class="{{ $subData['icon'] }} me-2 text-muted"></i>
                                                        {{ $subData['name'] }}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        
                                        <div class="mt-3 pt-3 border-top">
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary"
                                                        onclick="selectAllInModule('{{ $moduleKey }}')">
                                                    <i class="fas fa-check-double me-1"></i>Tout sélectionner
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-secondary"
                                                        onclick="deselectAllInModule('{{ $moduleKey }}')">
                                                    <i class="fas fa-times me-1"></i>Tout désélectionner
                                                </button>
                                            </div>
                                            <small class="text-muted ms-3">
                                                <span id="count_{{ $moduleKey }}">
                                                    {{ count(array_intersect(array_keys($moduleData['submodules']), $allowedSubmodules)) }}/{{ count($moduleData['submodules']) }}
                                                </span>
                                                sous-modules sélectionnés
                                            </small>
                                        </div>
                                    </div>
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
                                <button type="submit" class="btn btn-success btn-lg">
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
function selectAllInModule(moduleKey) {
    const container = document.getElementById('collapse_' + moduleKey);
    const checkboxes = container.querySelectorAll('input[type="checkbox"][name="submodules[]"]');
    checkboxes.forEach(cb => cb.checked = true);
    updateModuleCount(moduleKey);
}

function deselectAllInModule(moduleKey) {
    const container = document.getElementById('collapse_' + moduleKey);
    const checkboxes = container.querySelectorAll('input[type="checkbox"][name="submodules[]"]');
    checkboxes.forEach(cb => cb.checked = false);
    updateModuleCount(moduleKey);
}

function updateModuleCount(moduleKey) {
    const container = document.getElementById('collapse_' + moduleKey);
    const checkboxes = container.querySelectorAll('input[type="checkbox"][name="submodules[]"]');
    const checked = container.querySelectorAll('input[type="checkbox"][name="submodules[]"]:checked');
    document.getElementById('count_' + moduleKey).textContent = checked.length + '/' + checkboxes.length;
}

function selectAllModules() {
    // Sélectionner tous les modules
    document.querySelectorAll('input[type="checkbox"][name="modules[]"]').forEach(cb => cb.checked = true);
    
    // Sélectionner tous les sous-modules
    document.querySelectorAll('input[type="checkbox"][name="submodules[]"]').forEach(cb => cb.checked = true);
    
    // Mettre à jour tous les compteurs
    document.querySelectorAll('[id^="count_"]').forEach(counter => {
        const moduleKey = counter.id.replace('count_', '');
        updateModuleCount(moduleKey);
    });
}

function resetAll() {
    // Désélectionner tous les modules
    document.querySelectorAll('input[type="checkbox"][name="modules[]"]').forEach(cb => cb.checked = false);
    
    // Désélectionner tous les sous-modules
    document.querySelectorAll('input[type="checkbox"][name="submodules[]"]').forEach(cb => cb.checked = false);
    
    // Mettre à jour tous les compteurs
    document.querySelectorAll('[id^="count_"]').forEach(counter => {
        const moduleKey = counter.id.replace('count_', '');
        updateModuleCount(moduleKey);
    });
}

// Mettre à jour les compteurs quand on coche/décoche un sous-module
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[type="checkbox"][name="submodules[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Trouver le module parent
            const container = this.closest('[id^="collapse_"]');
            if (container) {
                const moduleKey = container.id.replace('collapse_', '');
                updateModuleCount(moduleKey);
            }
        });
    });
});
</script>
@endsection
