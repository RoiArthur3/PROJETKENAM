@extends('layouts.app')

@section('title', 'Synchronisation Caméra - KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-camera me-2"></i>Synchronisation Caméra
            </h1>
            <p class="text-muted mb-0">Gestion de l'association des personnels avec la reconnaissance faciale</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Personnel</h5>
                    <h2>{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Synchronisés</h5>
                    <h2>{{ $stats['synced'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Non Synchronisés</h5>
                    <h2>{{ $stats['not_synced'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Avec Photo</h5>
                    <h2>{{ $stats['with_photo'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('rh.camera-sync.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher par nom, prénom ou matricule" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="sync_status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="synced" {{ request('sync_status') == 'synced' ? 'selected' : '' }}>Synchronisés</option>
                            <option value="not_synced" {{ request('sync_status') == 'not_synced' ? 'selected' : '' }}>Non synchronisés</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filtrer
                        </button>
                        <a href="{{ route('rh.camera-sync.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Réinitialiser
                        </a>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" form="bulk-sync-form" class="btn btn-success" disabled id="bulkSyncBtn">
                            <i class="fas fa-sync me-2"></i>Synchroniser la sélection
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des personnels -->
    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form id="bulk-sync-form" method="POST" action="{{ route('rh.camera-sync.bulk-sync') }}">
                @csrf
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>Photo</th>
                                <th>Matricule</th>
                                <th>Nom & Prénoms</th>
                                <th>ID Caméra</th>
                                <th>Statut Sync</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($personnels as $personnel)
                            <tr>
                                <td>
                                    <input type="checkbox" name="personnel_ids[]" value="{{ $personnel->id }}" 
                                           class="form-check-input personnel-checkbox" 
                                           @if(!$personnel->camera_person_id || !$personnel->photo_profil) disabled @endif>
                                </td>
                                <td>
                                    @if($personnel->photo_profil)
                                        <img src="{{ asset('storage/' . $personnel->photo_profil) }}" 
                                             alt="Photo" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $personnel->matricule }}</span>
                                </td>
                                <td>
                                    <strong>{{ $personnel->nom }}</strong><br>
                                    <small class="text-muted">{{ $personnel->prenoms }}</small>
                                </td>
                                <td>
                                    @if($personnel->camera_person_id)
                                        <span class="badge bg-success">{{ $personnel->camera_person_id }}</span>
                                    @else
                                        <span class="text-muted">Non configuré</span>
                                    @endif
                                </td>
                                <td>
                                    @if($personnel->camera_person_id && $personnel->photo_profil)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Prêt
                                        </span>
                                    @elseif($personnel->camera_person_id || $personnel->photo_profil)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Incomplet
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times me-1"></i>Vide
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if($personnel->camera_person_id && $personnel->photo_profil)
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="syncSingle({{ $personnel->id }})" title="Synchroniser">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        @endif
                                        
                                        @if($personnel->camera_person_id || $personnel->photo_profil)
                                            <a href="{{ route('rh.camera-sync.edit', $personnel->id) }}" 
                                               class="btn btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('rh.camera-sync.create', $personnel->id) }}" 
                                               class="btn btn-outline-success" title="Configurer">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        @endif
                                        
                                        @if($personnel->camera_person_id || $personnel->photo_profil)
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmDelete({{ $personnel->id }})" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucun personnel trouvé</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Affichage de {{ $personnels->firstItem() }} à {{ $personnels->lastItem() }} 
                        sur {{ $personnels->total() }} résultats
                    </div>
                    <div>
                        {{ $personnels->links() }}
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la synchronisation de ce personnel ?</p>
                <p class="text-muted">Cela supprimera l'ID caméra et la photo associée.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Sélection/Désélection tout
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.personnel-checkbox:not(:disabled)');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkSyncButton();
});

// Gestion de la sélection individuelle
document.querySelectorAll('.personnel-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkSyncButton);
});

function updateBulkSyncButton() {
    const checkedBoxes = document.querySelectorAll('.personnel-checkbox:checked');
    const bulkSyncBtn = document.getElementById('bulkSyncBtn');
    
    if (checkedBoxes.length > 0) {
        bulkSyncBtn.disabled = false;
        bulkSyncBtn.textContent = `Synchroniser (${checkedBoxes.length})`;
    } else {
        bulkSyncBtn.disabled = true;
        bulkSyncBtn.textContent = 'Synchroniser la sélection';
    }
}

// Synchronisation individuelle
function syncSingle(id) {
    if (confirm('Voulez-vous synchroniser ce personnel avec la caméra ?')) {
        fetch(`/rh/camera-sync/sync/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la synchronisation');
        });
    }
}

// Confirmation de suppression
function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/rh/camera-sync/destroy/${id}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection
