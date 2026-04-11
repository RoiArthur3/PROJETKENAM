@extends('layouts.app')

@section('title', 'Modifier mon profil')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-content">

        <!-- MODERN DASHBOARD HEADER -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="fas fa-user-edit me-3"></i>Modifier mon profil
                    </h1>
                    <p class="subtitle mb-0">
                        Mettez à jour vos informations personnelles et votre mot de passe
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('profile.dashboard') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Retour au profil
                        </a>
                        <button class="btn btn-light btn-sm" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>Actualiser
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end align-items-center mb-4">
            <span class="data-source-badge profile">
                <i class="fas fa-user-edit me-1"></i>Modification Profil
            </span>
            <span class="ms-2 text-muted">
                <i class="fas fa-clock me-1"></i>{{ now()->format('d/m/Y H:i') }}
            </span>
        </div>

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="modern-card mb-4">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="fas fa-user me-2"></i>Informations personnelles
                    </h5>
                </div>
                <div class="modern-card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nom</label>
                            <input type="text" class="form-control modern-input" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control modern-input" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="telephone" class="form-label fw-semibold">Téléphone</label>
                            <input type="tel" class="form-control modern-input" id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}" pattern="0[0-9]{9}" maxlength="10">
                            @error('telephone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn-modern btn-primary-modern">
                            <i class="fas fa-save me-1"></i>Enregistrer les modifications
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="modern-card mb-4">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="fas fa-lock me-2"></i>Changer le mot de passe
                    </h5>
                </div>
                <div class="modern-card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">Mot de passe actuel</label>
                            <input type="password" class="form-control modern-input" id="current_password" name="current_password" required autocomplete="current-password">
                            @error('current_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Nouveau mot de passe</label>
                            <input type="password" class="form-control modern-input" id="password" name="password" required autocomplete="new-password">
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirmer le nouveau mot de passe</label>
                            <input type="password" class="form-control modern-input" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn-modern btn-warning-modern">
                            <i class="fas fa-key me-1"></i>Changer le mot de passe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-warning-modern {
    background: linear-gradient(135deg, var(--kenam-orange), var(--kenam-orange-dark));
    color: white;
}

.btn-warning-modern:hover {
    background: linear-gradient(135deg, var(--kenam-orange-dark), var(--kenam-orange));
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(255, 107, 53, 0.3);
}

.form-label {
    color: var(--gray-700);
    margin-bottom: 0.5rem;
}

.text-danger {
    color: #dc2626 !important;
}
</style>
@endsection
