@extends('layouts.app')

@section('title', 'Modifier Synchronisation Caméra - KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-camera me-2"></i>Modifier Synchronisation Caméra
            </h1>
            <p class="text-muted mb-0">Mise à jour de l'association avec la reconnaissance faciale</p>
        </div>
        <a href="{{ route('rh.camera-sync.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    <!-- Informations du personnel -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informations du Personnel</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 text-center">
                    @if($personnel->photo_profil)
                        <img src="{{ asset('storage/' . $personnel->photo_profil) }}" 
                             alt="Photo actuelle" class="rounded-circle mb-2" 
                             style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mb-2" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-user text-white fa-2x"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-10">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td><strong>Matricule:</strong></td>
                            <td><span class="badge bg-secondary">{{ $personnel->matricule }}</span></td>
                            <td><strong>Nom:</strong></td>
                            <td>{{ $personnel->nom }}</td>
                        </tr>
                        <tr>
                            <td><strong>Prénoms:</strong></td>
                            <td>{{ $personnel->prenoms }}</td>
                            <td><strong>Service:</strong></td>
                            <td>{{ $personnel->service ?? 'Non assigné' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Poste:</strong></td>
                            <td>{{ $personnel->poste ?? 'Non défini' }}</td>
                            <td><strong>Date d'embauche:</strong></td>
                            <td>{{ $personnel->date_embauche ? $personnel->date_embauche->format('d/m/Y') : 'Non définie' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire de modification -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Modification de la Synchronisation</h5>
        </div>
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

            <form method="POST" action="{{ route('rh.camera-sync.update', $personnel->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- ID Caméra -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label">
                            <i class="fas fa-camera me-2"></i>ID Personnel Caméra
                        </label>
                        <input type="text" name="camera_person_id" class="form-control" 
                               value="{{ old('camera_person_id', $personnel->camera_person_id) }}"
                               placeholder="Ex: EMP001, CAM_12345, etc.">
                        <div class="form-text">
                            Identifiant unique pour la reconnaissance faciale sur la caméra Hikvision
                        </div>
                        @error('camera_person_id')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Photo de profil -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label">
                            <i class="fas fa-image me-2"></i>Photo de Profil
                        </label>
                        <input type="file" name="photo_profil" class="form-control" 
                               accept="image/*" onchange="previewPhoto(event)">
                        <div class="form-text">
                            Format: JPG, PNG (max 2MB) - Laissez vide pour garder l'actuelle
                        </div>
                        @error('photo_profil')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Photo actuelle et aperçu -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Photo Actuelle</label>
                        <div class="border rounded p-3 text-center bg-light" style="min-height: 150px;">
                            @if($personnel->photo_profil)
                                <img src="{{ asset('storage/' . $personnel->photo_profil) }}" 
                                     alt="Photo actuelle" class="rounded-circle" 
                                     style="max-width: 120px; max-height: 120px; object-fit: cover;">
                                <div class="mt-2">
                                    <small class="text-muted">Photo actuelle</small>
                                </div>
                            @else
                                <div class="text-muted">
                                    <i class="fas fa-user fa-3x mb-2"></i>
                                    <p>Aucune photo</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nouvelle Photo (Aperçu)</label>
                        <div class="border rounded p-3 text-center bg-light" style="min-height: 150px;">
                            <div id="photoPreview" class="d-flex align-items-center justify-content-center" style="height: 120px;">
                                <div class="text-muted">
                                    <i class="fas fa-image fa-2x mb-2"></i>
                                    <p class="mb-0">Aucune nouvelle photo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statut actuel -->
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label">Statut Actuel de la Synchronisation</label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>ID Caméra:</strong>
                                        @if($personnel->camera_person_id)
                                            <span class="badge bg-success ms-2">{{ $personnel->camera_person_id }}</span>
                                        @else
                                            <span class="badge bg-secondary ms-2">Non configuré</span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Photo:</strong>
                                        @if($personnel->photo_profil)
                                            <span class="badge bg-success ms-2">Disponible</span>
                                        @else
                                            <span class="badge bg-secondary ms-2">Absente</span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Statut Global:</strong>
                                        @if($personnel->camera_person_id && $personnel->photo_profil)
                                            <span class="badge bg-success ms-2">
                                                <i class="fas fa-check me-1"></i>Prêt pour synchronisation
                                            </span>
                                        @elseif($personnel->camera_person_id || $personnel->photo_profil)
                                            <span class="badge bg-warning ms-2">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Incomplet
                                            </span>
                                        @else
                                            <span class="badge bg-secondary ms-2">
                                                <i class="fas fa-times me-1"></i>Vide
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('rh.camera-sync.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        @if($personnel->camera_person_id && $personnel->photo_profil)
                            <button type="button" class="btn btn-outline-success ms-2" onclick="syncNow()">
                                <i class="fas fa-sync me-2"></i>Synchroniser maintenant
                            </button>
                        @endif
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>Supprimer la synchronisation
                        </button>
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
                <p class="text-muted">Cela supprimera:</p>
                <ul>
                    <li>L'ID caméra associé</li>
                    <li>La photo de profil</li>
                    <li>Le statut de synchronisation</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" action="{{ route('rh.camera-sync.destroy', $personnel->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewPhoto(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('photoPreview');
    
    if (file) {
        // Vérifier le type de fichier
        if (!file.type.startsWith('image/')) {
            alert('Veuillez sélectionner un fichier image valide');
            event.target.value = '';
            return;
        }
        
        // Vérifier la taille (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('La photo ne doit pas dépasser 2MB');
            event.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Aperçu" 
                     class="rounded-circle" 
                     style="max-width: 120px; max-height: 120px; object-fit: cover;">
                <div class="mt-2">
                    <small class="text-success">
                        <i class="fas fa-check me-1"></i>${file.name} (${(file.size / 1024).toFixed(1)} KB)
                    </small>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = `
            <div class="text-muted">
                <i class="fas fa-image fa-2x mb-2"></i>
                <p class="mb-0">Aucune nouvelle photo</p>
            </div>
        `;
    }
}

function syncNow() {
    if (confirm('Voulez-vous synchroniser ce personnel avec la caméra maintenant ?')) {
        fetch(`/rh/camera-sync/sync/{{ $personnel->id }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Synchronisation réussie !');
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

function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection
