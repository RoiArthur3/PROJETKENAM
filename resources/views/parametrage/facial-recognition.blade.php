@extends('layouts.app')

@section('title', 'Reconnaissance Faciale - KENAM')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-face-smile me-2 text-purple"></i>
                    Reconnaissance Faciale
                </h1>
                <div>
                    <button type="button" class="btn btn-primary" onclick="syncPendingRecords()">
                        <i class="fas fa-sync me-1"></i>Synchroniser en attente
                    </button>
                    <button type="button" class="btn btn-info" onclick="loadRecords()">
                        <i class="fas fa-refresh me-1"></i>Actualiser
                    </button>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title" id="totalRecords">0</h4>
                                    <p class="card-text">Total enregistrements</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x opacity-75"></i>
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
                                    <h4 class="card-title" id="todayRecords">0</h4>
                                    <p class="card-text">Enregistrements aujourd'hui</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-day fa-2x opacity-75"></i>
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
                                    <h4 class="card-title" id="pendingSync">0</h4>
                                    <p class="card-text">En attente de sync</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title" id="activeDevices">0</h4>
                                    <p class="card-text">Devices actifs</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-camera fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filtres
                    </h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Device</label>
                            <select id="filterDevice" class="form-select">
                                <option value="">Tous les devices</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Employé</label>
                            <input type="text" id="filterEmployee" class="form-control" placeholder="Matricule ou nom">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Statut</label>
                            <select id="filterStatus" class="form-select">
                                <option value="">Tous</option>
                                <option value="recognized">Reconnu</option>
                                <option value="unknown">Inconnu</option>
                                <option value="uncertain">Incertain</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sync</label>
                            <select id="filterSyncStatus" class="form-select">
                                <option value="">Tous</option>
                                <option value="pending">En attente</option>
                                <option value="synced">Synchronisé</option>
                                <option value="failed">Échec</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-outline-primary w-100" onclick="applyFilters()">
                                <i class="fas fa-search me-1"></i>Rechercher
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tableau des enregistrements -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Enregistrements de Reconnaissance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Employé</th>
                                    <th>Device</th>
                                    <th>Heure</th>
                                    <th>Score</th>
                                    <th>Direction</th>
                                    <th>Statut</th>
                                    <th>Sync</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="recordsTableBody">
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Chargement...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <nav class="mt-3">
                        <ul class="pagination justify-content-center" id="pagination">
                            <!-- Pagination générée par JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour synchroniser un employé -->
<div class="modal fade" id="syncModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-cog me-2"></i>Synchroniser Employé
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="syncForm">
                    <div class="mb-3">
                        <label class="form-label">Employé</label>
                        <select id="syncPersonnel" class="form-select" required>
                            <option value="">Sélectionner un employé</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Device</label>
                        <select id="syncDevice" class="form-select" required>
                            <option value="">Sélectionner un device</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Photo (optionnel)</label>
                        <textarea id="syncPhoto" class="form-control" rows="3" placeholder="Coller une photo en base64..."></textarea>
                        <small class="text-muted">Laisser vide pour utiliser la photo de l'employé</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="performSync()">
                    <i class="fas fa-sync me-1"></i>Synchroniser
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let filters = {};

// Charger les statistiques
function loadStats() {
    fetch('/api/facial-recognition/stats', {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalRecords').textContent = data.stats.total_records;
            document.getElementById('todayRecords').textContent = data.stats.today_records;
            document.getElementById('pendingSync').textContent = data.stats.pending_sync;
            document.getElementById('activeDevices').textContent = data.stats.devices_active;
        }
    })
    .catch(error => console.error('Error loading stats:', error));
}

// Charger les enregistrements
function loadRecords(page = 1) {
    currentPage = page;
    
    const params = new URLSearchParams({
        page: page,
        per_page: 20,
        ...filters
    });

    document.getElementById('recordsTableBody').innerHTML = `
        <tr>
            <td colspan="9" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </td>
        </tr>
    `;

    fetch('/api/facial-recognition/records?' + params.toString(), {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayRecords(data.data);
            displayPagination(data.pagination);
        }
    })
    .catch(error => console.error('Error loading records:', error));
}

// Afficher les enregistrements
function displayRecords(records) {
    const tbody = document.getElementById('recordsTableBody');
    
    if (records.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-muted">
                    Aucun enregistrement trouvé
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = records.map(record => `
        <tr>
            <td>${record.id}</td>
            <td>
                <div>
                    <strong>${record.employee_name || 'Inconnu'}</strong><br>
                    <small class="text-muted">${record.employee_code || '-'}</small>
                </div>
            </td>
            <td>
                <span class="badge bg-info">${record.device?.name || '-'}</span>
            </td>
            <td>${formatDateTime(record.recognition_time)}</td>
            <td>
                <span class="badge ${getConfidenceBadgeClass(record.confidence_score)}">
                    ${(record.confidence_score * 100).toFixed(1)}%
                </span>
            </td>
            <td>
                <span class="badge ${getDirectionBadgeClass(record.direction)}">
                    ${record.formatted_direction || '-'}
                </span>
            </td>
            <td>
                <span class="badge ${getStatusBadgeClass(record.status)}">
                    ${record.status}
                </span>
            </td>
            <td>
                <span class="badge ${getSyncBadgeClass(record.sync_status)}">
                    ${record.formatted_status}
                </span>
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewRecord(${record.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${record.sync_status === 'failed' ? `
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="retrySync(${record.id})">
                            <i class="fas fa-redo"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        </tr>
    `).join('');
}

// Afficher la pagination
function displayPagination(pagination) {
    const pagination = document.getElementById('pagination');
    let html = '';

    // Previous
    html += `
        <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadRecords(${pagination.current_page - 1})">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `;

    // Pages
    for (let i = 1; i <= pagination.last_page; i++) {
        html += `
            <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadRecords(${i})">${i}</a>
            </li>
        `;
    }

    // Next
    html += `
        <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadRecords(${pagination.current_page + 1})">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `;

    pagination.innerHTML = html;
}

// Classes pour les badges
function getConfidenceBadgeClass(score) {
    if (score >= 0.8) return 'bg-success';
    if (score >= 0.6) return 'bg-warning';
    return 'bg-danger';
}

function getDirectionBadgeClass(direction) {
    switch(direction) {
        case 'entry': return 'bg-primary';
        case 'exit': return 'bg-secondary';
        default: return 'bg-light text-dark';
    }
}

function getStatusBadgeClass(status) {
    switch(status) {
        case 'recognized': return 'bg-success';
        case 'uncertain': return 'bg-warning';
        case 'unknown': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function getSyncBadgeClass(syncStatus) {
    switch(syncStatus) {
        case 'synced': return 'bg-success';
        case 'syncing': return 'bg-warning';
        case 'failed': return 'bg-danger';
        case 'pending': return 'bg-info';
        default: return 'bg-secondary';
    }
}

// Formater la date
function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('fr-FR');
}

// Appliquer les filtres
function applyFilters() {
    filters = {
        device_id: document.getElementById('filterDevice').value,
        employee_code: document.getElementById('filterEmployee').value,
        status: document.getElementById('filterStatus').value,
        sync_status: document.getElementById('filterSyncStatus').value
    };
    loadRecords(1);
}

// Synchroniser les enregistrements en attente
function syncPendingRecords() {
    if (!confirm('Synchroniser tous les enregistrements en attente ?')) {
        return;
    }

    fetch('/api/facial-recognition/sync-pending', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token'),
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Synchronisation terminée: ${data.synced_count} succès, ${data.failed_count} échecs`);
            loadRecords(currentPage);
            loadStats();
        }
    })
    .catch(error => console.error('Error syncing pending records:', error));
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadRecords();
    
    // Charger les devices pour les filtres
    fetch('/api/hikvision/devices', {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const deviceSelect = document.getElementById('filterDevice');
            const syncDeviceSelect = document.getElementById('syncDevice');
            
            const options = data.devices.map(device => 
                `<option value="${device.id}">${device.name}</option>`
            ).join('');
            
            deviceSelect.innerHTML = '<option value="">Tous les devices</option>' + options;
            syncDeviceSelect.innerHTML = '<option value="">Sélectionner un device</option>' + options;
        }
    })
    .catch(error => console.error('Error loading devices:', error));
});
</script>
@endpush
