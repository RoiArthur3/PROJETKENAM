@extends('layouts.app')

@section('title', 'Créer un Service | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building me-2"></i>Nouveau Service Opérationnel
                    </h6>
                    <a href="/admin/services" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="/admin/services">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nom" class="form-label fw-bold">Nom du service *</label>
                                <input type="text" name="nom" id="nom"
                                       class="form-control @error('nom') is-invalid @enderror"
                                       value="{{ old('nom') }}" required
                                       placeholder="Ex: Logistique, Transport, Maintenance">
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email du service</label>
                                <input type="email" name="email" id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       placeholder="service@kenamservices.net">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" name="telephone" id="telephone"
                                       class="form-control @error('telephone') is-invalid @enderror"
                                       value="{{ old('telephone') }}"
                                       placeholder="Ex: +225 07 00 00 00 00">
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" name="responsable" id="responsable"
                                       class="form-control @error('responsable') is-invalid @enderror"
                                       value="{{ old('responsable') }}"
                                       placeholder="Nom du responsable">
                                @error('responsable')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="couleur" class="form-label">Couleur du service</label>
                                <input type="color" name="couleur" id="couleur"
                                       class="form-control form-control-color @error('couleur') is-invalid @enderror"
                                       value="{{ old('couleur', '#007bff') }}">
                                @error('couleur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="icone" class="form-label">Icône</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i id="icone-preview"></i></span>
                                    <input type="text" name="icone" id="icone"
                                           class="form-control @error('icone') is-invalid @enderror"
                                           value="{{ old('icone', 'fas fa-cogs') }}"
                                           placeholder="fas fa-cogs">
                                    @error('icone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Utilisez les classes d'icônes Font Awesome (ex: fas fa-cogs)</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="actif" id="actif" value="1"
                                           class="form-check-input" {{ old('actif', '1') ? 'checked' : '' }}>
                                    <label for="actif" class="form-check-label">Service actif</label>
                                    <div class="form-text">Cochez cette case pour rendre le service disponible dans les opérations</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/admin/services" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Créer le service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Mise à jour de l'aperçu de l'icône en temps réel
document.getElementById('icone').addEventListener('input', function() {
    document.getElementById('icone-preview').className = this.value;
});

// Initialisation de l'aperçu de l'icône au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('icone-preview').className = document.getElementById('icone').value;
});
</script>
@endpush

@endsection
