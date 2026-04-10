@extends('layouts.app')

@section('title', 'Synchronisation Faciale')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-camera me-2 text-primary"></i>Synchronisation Faciale
                    </h1>
                    <p class="text-muted mb-0">Gérez la synchronisation des employés avec les terminaux Hikvision</p>
                </div>
                <div>
                    <button type="button" class="btn btn-success" onclick="syncAllPending()">
                        <i class="fas fa-sync me-2"></i>Synchroniser tout
                    </button>
                    <a href="{{ route('rh.employes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                            <small>Total employés</small>
                        </div>
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['synced'] ?? 0 }}</h4>
                            <small>Synchronisés</small>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['pending'] ?? 0 }}</h4>
                            <small>En attente</small>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['failed'] ?? 0 }}</h4>
                            <small>Échoués</small>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Statut synchronisation</label>
                    <select name="sync_status" class="form-select">
                        <option value="">Tous</option>
                        <option value="synced" {{ request('sync_status') == 'synced' ? 'selected' : '' }}>Synchronisés</option>
                        <option value="pending" {{ request('sync_status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="failed" {{ request('sync_status') == 'failed' ? 'selected' : '' }}>Échoués</option>
                        <option value="null" {{ request('sync_status') == 'null' ? 'selected' : '' }}>Non synchronisés</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Service</label>
                    <select name="service" class="form-select">
                        <option value="">Tous</option>
                        @foreach($services as $service)
                            <option value="{{ $service }}" {{ request('service') == $service ? 'selected' : '' }}>{{ $service }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filtrer
                        </button>
                        <a href="{{ route('rh.facial-sync.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-2"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des employés -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des employés</h5>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleSelectAll()">
                        <i class="fas fa-check-square me-2"></i>Tout sélectionner
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="bulkSync()" id="bulkSyncBtn" style="display: none;">
                        <i class="fas fa-sync me-2"></i>Synchroniser la sélection
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Matricule</th>
                            <th>Nom & Prénoms</th>
                            <th>Service</th>
                            <th>Photo</th>
                            <th>Statut sync</th>
                            <th>Dernière sync</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td>
                                    <input type="checkbox" class="employee-checkbox" value="{{ $employee->id }}" 
                                           @if(!$employee->photo_profil) disabled @endif>
                                </td>
                                <td><strong>{{ $employee->matricule }}</strong></td>
                                <td>{{ $employee->nom_complet }}</td>
                                <td>{{ $employee->service }}</td>
                                <td>
                                    @if($employee->photo_profil)
                                        <img src="{{ asset('storage/' . $employee->photo_profil) }}" 
                                             alt="{{ $employee->nom_complet }}" 
                                             class="rounded-circle" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <span class="badge bg-secondary">Aucune</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($employee->facial_sync_status)
                                        @case('synced')
                                            <span class="badge bg-success">Synchronisé</span>
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning">En attente</span>
                                            @break
                                        @case('failed')
                                            <span class="badge bg-danger">Échoué</span>
                                            @break
                                        @case('removed')
                                            <span class="badge bg-secondary">Supprimé</span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-dark">Non sync</span>
                                    @endswitch
                                </td>
                                <td>
                                    {{ $employee->facial_sync_at ? $employee->facial_sync_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($employee->photo_profil)
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="syncEmployee({{ $employee->id }})"
                                                    title="Synchroniser">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="checkStatus({{ $employee->id }})"
                                                title="Vérifier statut">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        @if($employee->facial_sync_status === 'synced')
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="removeEmployee({{ $employee->id }})"
                                                    title="Supprimer de la caméra">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Affichage de {{ $employees->firstItem() }} à {{ $employees->lastItem() }} 
                    sur {{ $employees->total() }} employés
                </div>
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de progression -->
<div class="modal fade" id="progressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Synchronisation en cours...</h5>
            </div>
            <div class="modal-body">
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 0%" id="progressBar"></div>
                </div>
                <div id="progressMessage">Initialisation...</div>
                <div id="progressDetails"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedEmployees = [];

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.employee-checkbox:not(:disabled)');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkSyncButton();
}

function updateBulkSyncButton() {
    const checkboxes = document.querySelectorAll('.employee-checkbox:checked');
    const bulkBtn = document.getElementById('bulkSyncBtn');
    
    if (checkboxes.length > 0) {
        bulkBtn.style.display = 'inline-block';
        selectedEmployees = Array.from(checkboxes).map(cb => cb.value);
    } else {
        bulkBtn.style.display = 'none';
        selectedEmployees = [];
    }
}

function syncEmployee(employeeId) {
    showProgress();
    
    fetch(`/rh/facial-sync/sync/${employeeId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideProgress();
        if (data.success) {
            showAlert('success', data.message);
            location.reload();
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        hideProgress();
        showAlert('error', 'Erreur lors de la synchronisation');
    });
}

function bulkSync() {
    if (selectedEmployees.length === 0) {
        showAlert('warning', 'Veuillez sélectionner des employés');
        return;
    }
    
    showProgress();
    
    fetch('/rh/facial-sync/bulk-sync', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            employee_ids: selectedEmployees
        })
    })
    .then(response => response.json())
    .then(data => {
        hideProgress();
        if (data.success) {
            showAlert('success', `Synchronisation terminée: ${data.results.success} succès, ${data.results.failed} échecs`);
            location.reload();
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        hideProgress();
        showAlert('error', 'Erreur lors de la synchronisation en masse');
    });
}

function syncAllPending() {
    if (!confirm('Voulez-vous synchroniser tous les employés en attente ?')) {
        return;
    }
    
    showProgress();
    
    fetch('/rh/facial-sync/sync-all', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideProgress();
        if (data.success) {
            showAlert('success', `Synchronisation terminée: ${data.results.success} succès, ${data.results.failed} échecs`);
            location.reload();
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        hideProgress();
        showAlert('error', 'Erreur lors de la synchronisation');
    });
}

function checkStatus(employeeId) {
    fetch(`/rh/facial-sync/status/${employeeId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let message = `Statut: ${data.status.status}`;
            if (data.status.data) {
                message += `\nNom: ${data.status.data.faceName || 'N/A'}`;
                message += `\nID: ${data.status.data.faceID || 'N/A'}`;
            }
            showAlert('info', message);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'Erreur lors de la vérification du statut');
    });
}

function removeEmployee(employeeId) {
    if (!confirm('Voulez-vous supprimer cet employé de la caméra ?')) {
        return;
    }
    
    fetch(`/rh/facial-sync/remove/${employeeId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            location.reload();
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'Erreur lors de la suppression');
    });
}

function showProgress() {
    const modal = new bootstrap.Modal(document.getElementById('progressModal'));
    modal.show();
}

function hideProgress() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('progressModal'));
    if (modal) {
        modal.hide();
    }
}

function showAlert(type, message) {
    // Implémenter votre système d'alerte (Toast, SweetAlert, etc.)
    alert(`${type.toUpperCase()}: ${message}`);
}

// Écouter les changements de checkboxes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('employee-checkbox')) {
        updateBulkSyncButton();
    }
});
</script>
@endpush
