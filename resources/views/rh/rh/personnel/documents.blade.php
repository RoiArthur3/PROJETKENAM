@extends('layouts.app')

@section('title', 'Documents - ' . $personnel->nom . ' ' . $personnel->prenoms)

@section('content')
<div class="container-fluid mt-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                <i class="fas fa-file me-2"></i>Documents - {{ $personnel->nom }} {{ $personnel->prenoms }}
            </h2>
            <small class="text-muted">Matricule: {{ $personnel->matricule }} | Gestion des documents RH</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('personnel.show', $personnel->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour fiche
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                <i class="fas fa-upload me-1"></i>Ajouter un document
            </button>
        </div>
    </div>

    <!-- Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiques documents -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $personnel->documents->count() }}</h4>
                            <small>Total documents</small>
                        </div>
                        <i class="fas fa-file fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $personnel->documents()->where('statut', 'VALIDE')->count() }}</h4>
                            <small>Documents validés</small>
                        </div>
                        <i class="fas fa-check fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $personnel->documents()->where('statut', 'EN_ATTENTE')->count() }}</h4>
                            <small>En attente</small>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $personnel->documents()->where('statut', 'EXPIRE')->count() }}</h4>
                            <small>Expirés</small>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="mb-1">Stockage utilisé</h6>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" style="width: 45%"></div>
                    </div>
                    <small>4.5 MB / 10 MB</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('personnel.documents', $personnel->id) }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Rechercher</label>
                    <input type="text" name="search" class="form-control" placeholder="Nom du fichier..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type de document</label>
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="CV" {{ request('type') == 'CV' ? 'selected' : '' }}>CV</option>
                        <option value="CONTRAT" {{ request('type') == 'CONTRAT' ? 'selected' : '' }}>Contrat</option>
                        <option value="DIPLOME" {{ request('type') == 'DIPLOME' ? 'selected' : '' }}>Diplôme</option>
                        <option value="ATTESTATION" {{ request('type') == 'ATTESTATION' ? 'selected' : '' }}>Attestation</option>
                        <option value="CASIER_JUDICIAIRE" {{ request('type') == 'CASIER_JUDICIAIRE' ? 'selected' : '' }}>Casier judiciaire</option>
                        <option value="CERTIFICAT_MEDICAL" {{ request('type') == 'CERTIFICAT_MEDICAL' ? 'selected' : '' }}>Certificat médical</option>
                        <option value="AUTRE" {{ request('type') == 'AUTRE' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="EN_ATTENTE" {{ request('statut') == 'EN_ATTENTE' ? 'selected' : '' }}>En attente</option>
                        <option value="VALIDE" {{ request('statut') == 'VALIDE' ? 'selected' : '' }}>Validé</option>
                        <option value="REJETE" {{ request('statut') == 'REJETE' ? 'selected' : '' }}>Rejeté</option>
                        <option value="EXPIRE" {{ request('statut') == 'EXPIRE' ? 'selected' : '' }}>Expiré</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search me-1"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Documents par catégorie -->
    <div class="row">
        <!-- Documents officiels -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>Documents officiels
                    </h6>
                </div>
                <div class="card-body">
                    @if($personnel->photo_profil || $personnel->cv_path || $personnel->contrat_path || $personnel->casier_judiciaire_path || $personnel->certificat_medical_path)
                        <div class="list-group list-group-flush">
                            @if($personnel->photo_profil)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-image text-primary me-2"></i>
                                    <strong>Photo de profil</strong>
                                    <br><small class="text-muted">Identité</small>
                                </div>
                                <div>
                                    <span class="badge bg-success me-2">Validé</span>
                                    <a href="{{ asset('storage/' . $personnel->photo_profil) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($personnel->cv_path)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <strong>CV</strong>
                                    <br><small class="text-muted">Recrutement</small>
                                </div>
                                <div>
                                    <span class="badge bg-success me-2">Validé</span>
                                    <a href="{{ asset('storage/' . $personnel->cv_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($personnel->contrat_path)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file-contract text-success me-2"></i>
                                    <strong>Contrat de travail</strong>
                                    <br><small class="text-muted">Juridique</small>
                                </div>
                                <div>
                                    <span class="badge bg-success me-2">Validé</span>
                                    <a href="{{ asset('storage/' . $personnel->contrat_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($personnel->casier_judiciaire_path)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-shield-alt text-warning me-2"></i>
                                    <strong>Casier judiciaire</strong>
                                    <br><small class="text-muted"> légal</small>
                                </div>
                                <div>
                                    <span class="badge bg-success me-2">Validé</span>
                                    <a href="{{ asset('storage/' . $personnel->casier_judiciaire_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($personnel->certificat_medical_path)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-heartbeat text-info me-2"></i>
                                    <strong>Certificat médical</strong>
                                    <br><small class="text-muted">Santé</small>
                                </div>
                                <div>
                                    <span class="badge bg-success me-2">Validé</span>
                                    <a href="{{ asset('storage/' . $personnel->certificat_medical_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-folder-open text-muted fa-2x mb-2"></i>
                            <p class="text-muted mb-0">Aucun document officiel</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Formation et diplômes -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>Formation et diplômes
                    </h6>
                </div>
                <div class="card-body">
                    @if($personnel->diplomes_path || $personnel->attestations_path || $personnel->lettre_motivation_path)
                        <div class="list-group list-group-flush">
                            @if($personnel->diplomes_path)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-graduation-cap text-dark me-2"></i>
                                    <strong>Diplômes</strong>
                                    <br><small class="text-muted">Formation</small>
                                </div>
                                <div>
                                    <span class="badge bg-success me-2">Validé</span>
                                    <a href="{{ asset('storage/' . $personnel->diplomes_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($personnel->attestations_path)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-certificate text-secondary me-2"></i>
                                    <strong>Attestations</strong>
                                    <br><small class="text-muted">Expérience</small>
                                </div>
                                <div>
                                    <span class="badge bg-success me-2">Validé</span>
                                    <a href="{{ asset('storage/' . $personnel->attestations_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($personnel->lettre_motivation_path)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                    <strong>Lettre de motivation</strong>
                                    <br><small class="text-muted">Recrutement</small>
                                </div>
                                <div>
                                    <span class="badge bg-success me-2">Validé</span>
                                    <a href="{{ asset('storage/' . $personnel->lettre_motivation_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-folder-open text-muted fa-2x mb-2"></i>
                            <p class="text-muted mb-0">Aucun document de formation</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Documents additionnels (tableau) -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-folder me-2"></i>Tous les documents
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nom du fichier</th>
                            <th>Type</th>
                            <th>Taille</th>
                            <th>Date d'ajout</th>
                            <th>Expiration</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $document)
                        <tr>
                            <td>
                                <i class="fas fa-file me-2"></i>
                                {{ $document->nom_fichier }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $document->type_document }}</span>
                            </td>
                            <td>{{ $document->taille_fichier }}</td>
                            <td>{{ \Carbon\Carbon::parse($document->created_at)->format('d/m/Y') }}</td>
                            <td>
                                @if($document->date_expiration)
                                    {{ \Carbon\Carbon::parse($document->date_expiration)->format('d/m/Y') }}
                                    @if($document->date_expiration->isPast())
                                        <span class="badge bg-danger ms-1">Expiré</span>
                                    @elseif($document->date_expiration->diffInDays(now()) <= 30)
                                        <span class="badge bg-warning ms-1">Bientôt</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @switch($document->statut)
                                    @case('EN_ATTENTE')
                                        <span class="badge bg-warning">En attente</span>
                                        @break
                                    @case('VALIDE')
                                        <span class="badge bg-success">Validé</span>
                                        @break
                                    @case('REJETE')
                                        <span class="badge bg-danger">Rejeté</span>
                                        @break
                                    @case('EXPIRE')
                                        <span class="badge bg-secondary">Expiré</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $document->statut }}</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ asset('storage/' . $document->chemin_fichier) }}" target="_blank" 
                                       class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ asset('storage/' . $document->chemin_fichier) }}" 
                                       class="btn btn-sm btn-outline-success" title="Télécharger" download>
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Document -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>Ajouter un document
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('personnel.documents.store', $personnel->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fichier *</label>
                        <input type="file" name="fichier" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip" required>
                        <small class="text-muted">Formats acceptés: PDF, DOC, DOCX, JPG, PNG, ZIP (max 10MB)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type de document *</label>
                        <select name="type_document" class="form-select" required>
                            <option value="">Sélectionner...</option>
                            <option value="CV">CV</option>
                            <option value="CONTRAT">Contrat</option>
                            <option value="DIPLOME">Diplôme</option>
                            <option value="ATTESTATION">Attestation</option>
                            <option value="CASIER_JUDICIAIRE">Casier judiciaire</option>
                            <option value="CERTIFICAT_MEDICAL">Certificat médical</option>
                            <option value="PHOTO">Photo</option>
                            <option value="CNI">Carte d'identité</option>
                            <option value="PASSEPORT">Passeport</option>
                            <option value="PERMIS">Permis de conduire</option>
                            <option value="AUTRE">Autre</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date d'expiration</label>
                        <input type="date" name="date_expiration" class="form-control">
                        <small class="text-muted">Pour les documents avec date de validité</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Description du document..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i>Télécharger
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Validation du fichier
document.querySelector('input[name="fichier"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const maxSize = 10 * 1024 * 1024; // 10MB
    
    if (file) {
        if (file.size > maxSize) {
            e.target.value = '';
            alert('Le fichier est trop volumineux. Taille maximale: 10MB');
        }
        
        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                             'image/jpeg', 'image/png', 'application/zip'];
        
        if (!allowedTypes.includes(file.type) && !file.name.match(/\.(pdf|doc|docx|jpg|jpeg|png|zip)$/i)) {
            e.target.value = '';
            alert('Format de fichier non autorisé');
        }
    }
});
</script>
@endpush
