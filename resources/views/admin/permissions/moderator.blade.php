@extends('layouts.app')

@section('title', 'Gestion des Permissions Modérateur')

@section('content')
<div class="content-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-shield me-2"></i>Gestion des Permissions Modérateur
        </h1>
        <div>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour aux rôles
            </a>
        </div>
    </div>

    <!-- Sélection du modérateur -->
    <div class="card shadow mb-4">
        <div class="card-header bg-warning text-white">
            <h6 class="m-0 fw-bold">
                <i class="fas fa-users me-2"></i>Sélectionner un Modérateur
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="moderatorSelect" class="form-label fw-bold">Choisir un modérateur:</label>
                    <select class="form-select" id="moderatorSelect">
                        <option value="">-- Sélectionner un modérateur --</option>
                        @foreach($moderators as $moderator)
                            <option value="{{ $moderator->id }}">{{ $moderator->name }} ({{ $moderator->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button class="btn btn-warning" id="loadPermissionsBtn" disabled>
                        <i class="fas fa-eye me-2"></i>Charger les permissions
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration des permissions -->
    <div class="card shadow" id="permissionsCard" style="display: none;">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 fw-bold">
                <i class="fas fa-cog me-2"></i>Configuration des Modules
                <span id="moderatorName" class="ms-2"></span>
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary fw-bold mb-3">
                        <i class="fas fa-cube me-2"></i>Modules Disponibles
                    </h6>

                    <div class="module-list">
                        @foreach($allModules as $moduleKey => $moduleName)
                        <div class="form-check mb-2">
                            <input class="form-check-input module-checkbox"
                                   type="checkbox"
                                   name="modules[]"
                                   value="{{ $moduleKey }}"
                                   id="module_{{ $moduleKey }}">
                            <label class="form-check-label" for="module_{{ $moduleKey }}">
                                <i class="fas fa-cube me-2"></i>{{ $moduleName }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="text-success fw-bold mb-3">
                        <i class="fas fa-check-circle me-2"></i>Modules Sélectionnés
                    </h6>

                    <div id="selectedModules" class="selected-modules-list">
                        <p class="text-muted">Aucun module sélectionné</p>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-success" id="savePermissionsBtn" disabled>
                            <i class="fas fa-save me-2"></i>Enregistrer les permissions
                        </button>
                        <button class="btn btn-outline-secondary ms-2" id="clearSelectionBtn">
                            <i class="fas fa-times me-2"></i>Effacer la sélection
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $moderators->count() }}</h4>
                            <p class="mb-0">Modérateurs actifs</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-shield fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ count($allModules) }}</h4>
                            <p class="mb-0">Modules disponibles</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cubes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="selectedCount">0</h4>
                            <p class="mb-0">Modules sélectionnés</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="remainingCount">{{ count($allModules) }}</h4>
                            <p class="mb-0">Modules restants</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-hourglass-half fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mb-0">Chargement des permissions...</p>
            </div>
        </div>
    </div>
</div>

<script>
let currentModeratorId = null;
let currentPermissions = [];

// Activer/désactiver le bouton de chargement
document.getElementById('moderatorSelect').addEventListener('change', function() {
    const loadBtn = document.getElementById('loadPermissionsBtn');
    loadBtn.disabled = !this.value;
});

// Charger les permissions du modérateur
document.getElementById('loadPermissionsBtn').addEventListener('click', function() {
    const moderatorId = document.getElementById('moderatorSelect').value;
    if (!moderatorId) return;

    currentModeratorId = moderatorId;

    // Afficher le modal de chargement
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();

    fetch(`/admin/moderators/${moderatorId}/permissions`)
        .then(response => response.json())
        .then(data => {
            modal.hide();

            if (data.error) {
                alert(data.error);
                return;
            }

            // Afficher le nom du modérateur
            document.getElementById('moderatorName').textContent = `- ${data.moderator.name}`;

            // Cocher les permissions existantes
            currentPermissions = data.permissions;
            document.querySelectorAll('.module-checkbox').forEach(checkbox => {
                checkbox.checked = currentPermissions.includes(checkbox.value);
            });

            // Mettre à jour l'affichage
            updateSelectedModules();

            // Afficher la carte des permissions
            document.getElementById('permissionsCard').style.display = 'block';

            // Activer le bouton de sauvegarde
            document.getElementById('savePermissionsBtn').disabled = false;
        })
        .catch(error => {
            modal.hide();
            console.error('Error:', error);
            alert('Erreur lors du chargement des permissions');
        });
});

// Mettre à jour l'affichage des modules sélectionnés
function updateSelectedModules() {
    const selectedCheckboxes = document.querySelectorAll('.module-checkbox:checked');
    const selectedModulesDiv = document.getElementById('selectedModules');

    if (selectedCheckboxes.length === 0) {
        selectedModulesDiv.innerHTML = '<p class="text-muted">Aucun module sélectionné</p>';
    } else {
        let html = '<div class="list-group">';
        selectedCheckboxes.forEach(checkbox => {
            const label = document.querySelector(`label[for="${checkbox.id}"]`).textContent.trim();
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${label}</span>
                    <span class="badge bg-primary rounded-pill">${checkbox.value}</span>
                </div>
            `;
        });
        html += '</div>';
        selectedModulesDiv.innerHTML = html;
    }

    // Mettre à jour les statistiques
    document.getElementById('selectedCount').textContent = selectedCheckboxes.length;
    document.getElementById('remainingCount').textContent = document.querySelectorAll('.module-checkbox').length - selectedCheckboxes.length;
}

// Écouter les changements de checkboxes
document.querySelectorAll('.module-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedModules);
});

// Sauvegarder les permissions
document.getElementById('savePermissionsBtn').addEventListener('click', function() {
    if (!currentModeratorId) return;

    const selectedPermissions = Array.from(document.querySelectorAll('.module-checkbox:checked'))
        .map(cb => cb.value);

    // Afficher le modal de chargement
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();

    fetch(`/admin/moderators/${currentModeratorId}/permissions`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            permissions: selectedPermissions
        })
    })
    .then(response => response.json())
    .then(data => {
        modal.hide();

        if (data.success) {
            alert(data.message);
        } else {
            alert('Erreur: ' + (data.error || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        modal.hide();
        console.error('Error:', error);
        alert('Erreur lors de la sauvegarde des permissions');
    });
});

// Effacer la sélection
document.getElementById('clearSelectionBtn').addEventListener('click', function() {
    document.querySelectorAll('.module-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectedModules();
});
</script>

<style>
.module-list {
    max-height: 400px;
    overflow-y: auto;
}

.selected-modules-list {
    max-height: 300px;
    overflow-y: auto;
}

.form-check {
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: background-color 0.2s;
}

.form-check:hover {
    background-color: #f8f9fa;
}

.form-check-input:checked + .form-check-label {
    color: #0d6efd;
    font-weight: 600;
}

.list-group-item {
    border-left: 4px solid #0d6efd;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endsection
