@extends('layouts.app')

@section('title', 'Modifier Mot de Passe | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-key mr-2 text-primary"></i>Modifier Mot de Passe
            </h1>
            <p class="text-muted">Changer le mot de passe du responsable du service: {{ $service->nom }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.services.responsables.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Retour
            </a>
        </div>
    </div>

    <!-- Informations du responsable -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Service:</strong> {{ $service->nom }}
                        </div>
                        <div class="col-md-3">
                            <strong>Responsable:</strong> {{ $service->responsable->name }}
                        </div>
                        <div class="col-md-3">
                            <strong>Email:</strong> {{ $service->responsable->email }}
                        </div>
                        <div class="col-md-3">
                            <strong>Statut:</strong>
                            <span class="badge {{ $service->responsable->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $service->responsable->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire de modification -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Nouveau mot de passe</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.services.responsables.update-password', $service) }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Nouveau mot de passe *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password"
                                               value="{{ old('password', '12345678') }}" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                                <i class="fas fa-eye" id="passwordToggle"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Minimum 6 caractères</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmer mot de passe *</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                           name="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="auto_generate_password"
                                               name="auto_generate_password" value="1" checked
                                               onchange="togglePasswordField()">
                                        <label class="form-check-label" for="auto_generate_password">
                                            Utiliser le mot de passe par défaut (12345678)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alertes -->
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Attention:</strong> Le changement de mot de passe sera immédiat. L'utilisateur devra utiliser le nouveau mot de passe pour sa prochaine connexion.
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Mettre à jour le mot de passe
                            </button>
                            <a href="{{ route('admin.services.responsables.index') }}" class="btn btn-outline-secondary ml-2">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Historique du compte</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Date création:</strong></td>
                            <td>{{ $service->responsable_compte_cree_le?->format('d/m/Y H:i') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Compte auto:</strong></td>
                            <td>
                                @if($service->responsable_compte_auto)
                                    <span class="badge bg-success">Oui</span>
                                @else
                                    <span class="badge bg-secondary">Non</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Dernière connexion:</strong></td>
                            <td>{{ $service->responsable->last_login_at?->format('d/m/Y H:i') ?? 'Jamais' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email vérifié:</strong></td>
                            <td>
                                @if($service->responsable->email_verified_at)
                                    <span class="badge bg-success">Oui</span>
                                @else
                                    <span class="badge bg-warning">Non</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    @if($service->responsable_notes)
                    <div class="mt-3">
                        <h6>Notes:</h6>
                        <p class="text-muted small">{{ $service->responsable_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Recommandations</h6>
                </div>
                <div class="card-body">
                    <h6>Pour un mot de passe sécurisé:</h6>
                    <ul>
                        <li>Minimum 8 caractères</li>
                        <li>Lettres majuscules et minuscules</li>
                        <li>Chiffres</li>
                        <li>Caractères spéciaux</li>
                    </ul>

                    <h6>Mot de passe par défaut:</h6>
                    <div class="alert alert-info">
                        <code>12345678</code><br>
                        <small>Facile à mémoriser mais peu sécurisé</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggle');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

function togglePasswordField() {
    const checkbox = document.getElementById('auto_generate_password');
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirmation');

    if (checkbox.checked) {
        passwordField.value = '12345678';
        confirmField.value = '12345678';
        passwordField.disabled = true;
        confirmField.disabled = true;
    } else {
        passwordField.disabled = false;
        confirmField.disabled = false;
        passwordField.focus();
    }
}

// Initialiser l'état
document.addEventListener('DOMContentLoaded', function() {
    togglePasswordField();
});
</script>
@endsection
