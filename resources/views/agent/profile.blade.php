@extends('layouts.app')

@section('title', 'Mon Profil - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-edit me-2"></i>
                Mon Profil
            </h1>
            <p class="text-muted mb-0">Gérez vos informations personnelles</p>
        </div>
        <div>
            <a href="{{ route('agent.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour au tableau de bord
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit me-2"></i>Modifier mes informations
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('agent.profile.update') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label fw-bold">Nom complet</label>
                                <input type="text"
                                       name="nom"
                                       id="nom"
                                       class="form-control @error('nom') is-invalid @enderror"
                                       value="{{ old('nom', $user->nom) }}"
                                       required>
                                @error('nom')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="telephone" class="form-label fw-bold">Téléphone</label>
                            <input type="tel"
                                   name="telephone"
                                   id="telephone"
                                   class="form-control @error('telephone') is-invalid @enderror"
                                   value="{{ old('telephone', $user->telephone) }}"
                                   placeholder="+221 XX XX XX XX">
                            @error('telephone')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <hr>

                        <h6 class="text-secondary mb-3">
                            <i class="fas fa-lock me-2"></i>Changer le mot de passe
                        </h6>
                        <p class="text-muted small mb-3">Laissez vide si vous ne souhaitez pas modifier votre mot de passe</p>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="current_password" class="form-label fw-bold">Mot de passe actuel</label>
                                <input type="password"
                                       name="current_password"
                                       id="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror">
                                @error('current_password')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="new_password" class="form-label fw-bold">Nouveau mot de passe</label>
                                <input type="password"
                                       name="new_password"
                                       id="new_password"
                                       class="form-control @error('new_password') is-invalid @enderror">
                                @error('new_password')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="new_password_confirmation" class="form-label fw-bold">Confirmer le mot de passe</label>
                                <input type="password"
                                       name="new_password_confirmation"
                                       id="new_password_confirmation"
                                       class="form-control @error('new_password_confirmation') is-invalid @enderror">
                                @error('new_password_confirmation')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>La confirmation ne correspond pas
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('agent.dashboard') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-2"></i>Informations système
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">ID utilisateur</small>
                        <p class="mb-2 fw-bold">#{{ $user->id }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Rôle</small>
                        <p class="mb-2">
                            <span class="badge bg-primary">Agent</span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Service</small>
                        <p class="mb-2 fw-bold">{{ $user->service->nom ?? 'Non assigné' }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Date de création</small>
                        <p class="mb-2">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Dernière connexion</small>
                        <p class="mb-0">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Première connexion' }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-shield-alt me-2"></i>Sécurité
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Mot de passe</small>
                        <p class="mb-2">
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>Défini
                            </span>
                        </p>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Recommandations</small>
                        <ul class="small text-muted mb-0">
                            <li>Utilisez un mot de passe complexe</li>
                            <li>Changez votre mot de passe régulièrement</li>
                            <li>Ne partagez jamais vos identifiants</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation du mot de passe en temps réel
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('new_password_confirmation');
    const currentPassword = document.getElementById('current_password');

    function validatePasswords() {
        if (newPassword.value && confirmPassword.value) {
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
    }

    newPassword.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);

    // Exiger le mot de passe actuel si un nouveau mot de passe est défini
    newPassword.addEventListener('input', function() {
        if (this.value) {
            currentPassword.required = true;
        } else {
            currentPassword.required = false;
        }
    });

    // Confirmation avant soumission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (newPassword.value) {
            if (!confirm('Êtes-vous sûr de vouloir changer votre mot de passe ?')) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endsection
