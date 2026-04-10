@extends('layouts.app')

@section('title', 'Configurer Synchronisation Caméra - KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-camera me-2"></i>Configurer Synchronisation Caméra
            </h1>
            <p class="text-muted mb-0">Association du personnel avec la reconnaissance faciale</p>
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

    <!-- Formulaire de configuration -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Configuration de la Synchronisation</h5>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('rh.camera-sync.store', $personnel->id) }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <!-- ID Caméra -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label">
                            <i class="fas fa-camera me-2"></i>ID Personnel Caméra *
                        </label>
                        <input type="text" name="camera_person_id" class="form-control" 
                               value="{{ old('camera_person_id') }}" required
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
                            <i class="fas fa-image me-2"></i>Photo de Profil *
                        </label>
                        <input type="file" name="photo_profil" class="form-control" 
                               accept="image/*" required onchange="previewPhoto(event)">
                        <div class="form-text">
                            Format: JPG, PNG (max 2MB) - Photo visage claire pour reconnaissance faciale
                        </div>
                        @error('photo_profil')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Aperçu de la photo -->
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label">Aperçu de la Photo</label>
                        <div class="border rounded p-3 text-center bg-light" style="min-height: 200px;">
                            <div id="photoPreview" class="d-flex align-items-center justify-content-center" style="height: 150px;">
                                <div class="text-muted">
                                    <i class="fas fa-image fa-3x mb-2"></i>
                                    <p>Aucune photo sélectionnée</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="alert alert-info mb-4">
                    <h6><i class="fas fa-info-circle me-2"></i>Instructions pour une bonne synchronisation:</h6>
                    <ul class="mb-0">
                        <li>Utilisez une photo récente et claire du visage</li>
                        <li>Le visage doit être bien visible et centré</li>
                        <li>Évitez les photos avec accessoires (lunettes, masques)</li>
                        <li>L'ID caméra doit être unique pour chaque personnel</li>
                        <li>Format recommandé: Carré, haute qualité, fond neutre</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('rh.camera-sync.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i>Configurer et Synchroniser
                    </button>
                </div>
            </form>
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
                     style="max-width: 150px; max-height: 150px; object-fit: cover;">
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
                <i class="fas fa-image fa-3x mb-2"></i>
                <p>Aucune photo sélectionnée</p>
            </div>
        `;
    }
}
</script>
@endsection
