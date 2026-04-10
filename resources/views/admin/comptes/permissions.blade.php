@extends('layouts.app')

@section('title', 'Permissions des Utilisateurs | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-shield-alt mr-2 text-primary"></i>Permissions des Utilisateurs
            </h1>
            <p class="text-muted">Gestion des permissions et accès aux modules</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.comptes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Retour aux comptes
            </a>
        </div>
    </div>

    <!-- Formulaire de gestion des permissions -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Configuration des permissions</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.comptes.permissions.update', $selectedUser->id ?? '') }}">
                        @csrf

                        <!-- Sélection de l'utilisateur -->
                        <div class="form-group mb-4">
                            <label for="user_select">Sélectionner un utilisateur</label>
                            <select class="form-control" id="user_select" name="user_id" onchange="this.form.submit()">
                                <option value="">-- Choisir un utilisateur --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ ($selectedUser && $selectedUser->id == $user->id) ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if($selectedUser)
                        <!-- Informations détaillées de l'utilisateur sélectionné -->
                        <div class="card border-primary mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="m-0">
                                    <i class="fas fa-user-circle mr-2"></i>Informations du compte
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Nom complet</label>
                                            <input type="text" class="form-control" value="{{ $selectedUser->name }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Email</label>
                                            <input type="email" class="form-control" value="{{ $selectedUser->email }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Téléphone</label>
                                            <input type="tel" class="form-control" value="{{ $selectedUser->phone ?? 'Non défini' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Rôle</label>
                                            <div>
                                                @if($selectedUser->role)
                                                    <span class="badge bg-{{ $selectedUser->role == 'admin' ? 'danger' : ($selectedUser->role == 'superadmin' ? 'warning' : ($selectedUser->role == 'moderator' ? 'info' : 'secondary')) }} fs-6">
                                                        <i class="fas fa-user-shield mr-1"></i>{{ ucfirst($selectedUser->role) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Aucun rôle</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Service</label>
                                            <div>
                                                @if($selectedUser->service)
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-building mr-1"></i>{{ $selectedUser->service->nom }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Aucun service</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Statut du compte</label>
                                            <div>
                                                @if($selectedUser->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle mr-1"></i>Actif
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle mr-1"></i>Inactif
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Dernière connexion</label>
                                            <div>
                                                @if($selectedUser->last_login_at)
                                                    <span class="text-success">
                                                        <i class="fas fa-clock mr-1"></i>{{ $selectedUser->last_login_at->format('d/m/Y H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="fas fa-clock mr-1"></i>Jamais connecté
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.comptes.edit-credentials', $selectedUser->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-user-edit mr-1"></i>Modifier les accès
                                    </a>
                                    <a href="{{ route('admin.comptes.edit', $selectedUser->id) }}" class="btn btn-outline-secondary btn-sm ml-2">
                                        <i class="fas fa-user-cog mr-1"></i>Profil complet
                                    </a>
                                    <button type="button" class="btn btn-outline-warning btn-sm ml-2" onclick="confirmResetPassword({{ $selectedUser->id }})">
                                        <i class="fas fa-key mr-1"></i>Réinitialiser le mot de passe
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Formulaire de modification des informations de connexion -->
                        <div class="card border-info mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="m-0">
                                    <i class="fas fa-lock mr-2"></i>Modifier les informations de connexion
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.comptes.update-credentials', $selectedUser->id) }}" id="updateCredentialsForm">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ $selectedUser->email }}" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Numéro de téléphone</label>
                                        <input type="tel" name="phone" class="form-control" value="{{ $selectedUser->phone ?? '' }}" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Nouveau mot de passe</label>
                                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                                        <small class="form-text text-muted">Laissez vide pour conserver le mot de passe actuel</small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Confirmer le mot de passe</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Mettre à jour
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Modules disponibles -->
                        <div class="row">
                            @foreach($modules as $moduleCode => $moduleInfo)
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                   id="module_{{ $moduleCode }}"
                                                   name="modules[]"
                                                   value="{{ $moduleCode }}"
                                                   {{ in_array($moduleCode, $userModules) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="module_{{ $moduleCode }}">
                                                <i class="{{ $moduleInfo['icon'] }} mr-2"></i>
                                                <strong>{{ $moduleInfo['label'] }}</strong>
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1">{{ $moduleInfo['route'] ?? '' }}</small>

                                        @if(!empty($moduleInfo['mandatory_for']))
                                        <div class="mt-2">
                                            <small class="text-info">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Obligatoire pour: {{ implode(', ', $moduleInfo['mandatory_for']) }}
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Modules obligatoires -->
                        <div class="mt-4">
                            <h6>Modules obligatoires</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-success bg-light">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                       id="module_operations"
                                                       name="modules[]"
                                                       value="operations"
                                                       checked disabled>
                                                <label class="form-check-label text-success" for="module_operations">
                                                    <i class="fas fa-tasks mr-2"></i>
                                                    <strong>Requêtes</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted d-block mt-1">Obligatoire pour tous les utilisateurs</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-success bg-light">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                       id="module_dashboard"
                                                       name="modules[]"
                                                       value="dashboard"
                                                       {{ in_array('dashboard', $userModules) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="module_dashboard">
                                                    <i class="fas fa-tachometer-alt mr-2"></i>
                                                    <strong>Tableau de bord</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted d-block mt-1">Recommandé pour tous</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Enregistrer les permissions
                            </button>
                            <a href="{{ route('admin.comptes.index') }}" class="btn btn-outline-secondary ml-2">
                                Annuler
                            </a>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Résumé des permissions</h6>
                </div>
                <div class="card-body">
                    @if($selectedUser)
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Modules accessibles ({{ count($userModules) }})</h6>
                        <div class="modules-grid">
                            @foreach($userModules as $module)
                            <div class="module-item">
                                <i class="{{ $modules[$module]['icon'] ?? 'fas fa-cog' }} text-primary mr-2"></i>
                                <span>{{ $modules[$module]['label'] ?? $module }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Statistiques</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-number text-primary">{{ count($userModules) }}</div>
                                    <div class="stat-label">Modules</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-number text-success">{{ count($modules) - count($userModules) }}</div>
                                    <div class="stat-label">Non accessibles</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle mr-2"></i>Impact sur l'utilisateur
                        </h6>
                        <ul class="mb-0">
                            <li>L'utilisateur verra uniquement les modules sélectionnés dans sa sidebar</li>
                            <li>Les permissions sont mises à jour immédiatement</li>
                            <li>Le module "Opérations" reste obligatoire</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Note:</strong> La modification des permissions affecte immédiatement l'accès de l'utilisateur.
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-user-circle fa-3x mb-3 text-muted"></i>
                        <h6>Aucun utilisateur sélectionné</h6>
                        <p class="mb-0">Sélectionnez un utilisateur pour gérer ses permissions.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 8px;
}

.module-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.stat-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 8px;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
}

.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
}
</style>

<script>
function confirmResetPassword(userId) {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser le mot de passe de cet utilisateur ?\n\nUn email sera envoyé avec les nouvelles instructions.')) {
        // Rediriger vers la route de réinitialisation
        window.location.href = '/admin/comptes/' + userId + '/reset-password';
    }
}

// Auto-soumission quand on sélectionne un utilisateur
document.getElementById('user_select')?.addEventListener('change', function() {
    if (this.value) {
        this.form.submit();
    }
});

// Auto-soumission quand on coche/décoche un module
document.querySelectorAll('input[name="modules[]"]').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        // Ajouter un petit délai pour éviter les soumissions multiples
        setTimeout(function() {
            checkbox.closest('form').submit();
        }, 100);
    });
});
</script>
@endsection
