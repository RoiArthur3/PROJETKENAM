@extends('layouts.app')

@section('title', 'Créer un Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="m-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Créer un Utilisateur
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.comptes.users.store') }}" method="POST">
                        @csrf

                        <!-- Nom et Email -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Téléphone -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="telephone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                       id="telephone" name="telephone" value="{{ old('telephone') }}" required>
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mot de passe -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirmation mot de passe et Rôle -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                       id="password_confirmation" name="password_confirmation" required>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="role" class="form-label">Rôle <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">Sélectionner un rôle</option>
                                    <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="moderator" {{ old('role') == 'moderator' ? 'selected' : '' }}>Modérateur</option>
                                    <option value="agent" {{ old('role') == 'agent' ? 'selected' : '' }}>Agent</option>
                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Utilisateur</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Service et Statut -->
                        <div class="row mb-3">
                            <div class="col-md-6"></div>

                            <div class="col-md-6">
                                <label for="is_active" class="form-label">Statut</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input @error('is_active') is-invalid @enderror"
                                           type="checkbox" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">
                                        Compte actif
                                    </label>
                                </div>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description des rôles -->
                        <div class="alert alert-info" id="role-permissions">
                            <h6><i class="fas fa-info-circle me-2"></i>Permissions du rôle :</h6>
                            <ul class="mb-0" id="role-permissions-list"></ul>
                        </div>

                        <!-- Boutons -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>
                                    Créer l'utilisateur
                                </button>
                                <a href="{{ route('admin.comptes.users.index') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    const list = document.getElementById('role-permissions-list');

    const rolePermissions = {
        superadmin: [
            'Accès TOTAL (tous les modules + dashboard)'
        ],
        admin: [
            'Accès à TOUS les modules (Opérations, Suivi, RH, etc.)',
            'Pas d\'accès au Dashboard général'
        ],
        moderator: [
            'Accès à TOUS les modules (comme Admin)',
            'Pas d\'accès au Dashboard général',
            'Peut être restreint sur certains modules (via permissions fines)'
        ],
        agent: [
            'Accès limité à : Opérations et Suivi & Validations'
        ],
        user: [
            'Accès limité à : Opérations et Suivi & Validations'
        ]
    };

    function renderPermissions() {
        const role = roleSelect.value;
        const items = rolePermissions[role] || ['Sélectionnez un rôle pour voir ses permissions'];
        list.innerHTML = '';
        items.forEach(text => {
            const li = document.createElement('li');
            li.innerHTML = '<strong>' + text + '</strong>';
            list.appendChild(li);
        });
    }

    roleSelect.addEventListener('change', renderPermissions);
    renderPermissions();
});
</script>
@endsection
