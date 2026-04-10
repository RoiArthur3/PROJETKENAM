@extends('layouts.app')

@section('title', 'Créer un Responsable | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-plus mr-2 text-primary"></i>Créer un Responsable
            </h1>
            <p class="text-muted">Créer un compte responsable pour le service: {{ $service->nom }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.services.responsables.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Retour
            </a>
        </div>
    </div>

    <!-- Informations du service -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Service:</strong> {{ $service->nom }}
                        </div>
                        @if($service->code)
                        <div class="col-md-4">
                            <strong>Code:</strong> {{ $service->code }}
                        </div>
                        @endif
                        @if($service->email)
                        <div class="col-md-4">
                            <strong>Email service:</strong> {{ $service->email }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire de création -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du responsable</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.services.responsables.store', $service) }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nom complet *</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name', 'Responsable ' . $service->nom) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="{{ old('email', strtolower(str_replace(' ', '.', $service->nom)) . '@kenam.ci') }}" required>
                                    <small class="form-text text-muted">Format suggéré: nom.service@kenam.ci</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telephone">Téléphone</label>
                                    <input type="tel" class="form-control" id="telephone" name="telephone"
                                           value="{{ old('telephone', $service->telephone ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Mot de passe *</label>
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

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions qui seront accordées -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle mr-2"></i>Permissions accordées automatiquement:</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <i class="fas fa-check text-success mr-1"></i>Tableau de bord
                                </div>
                                <div class="col-md-4">
                                    <i class="fas fa-check text-success mr-1"></i>Opérations
                                </div>
                                <div class="col-md-4">
                                    <i class="fas fa-check text-success mr-1"></i>Ressources Humaines
                                </div>
                                <div class="col-md-4">
                                    <i class="fas fa-check text-success mr-1"></i>Parc Auto
                                </div>
                                <div class="col-md-4">
                                    <i class="fas fa-check text-success mr-1"></i>Fournisseurs
                                </div>
                                <div class="col-md-4">
                                    <i class="fas fa-check text-success mr-1"></i>Comptabilité
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Créer le compte
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
                    <h6 class="m-0 font-weight-bold text-warning">Informations importantes</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Important:</strong> Le mot de passe par défaut est <code>12345678</code>
                    </div>

                    <h6>Rôle du responsable:</h6>
                    <ul>
                        <li>Gérer les opérations de son service</li>
                        <li>Valider les demandes</li>
                        <li>Accéder aux modules de base</li>
                        <li>Gérer les ressources du service</li>
                    </ul>

                    <h6>Après la création:</h6>
                    <ul>
                        <li>Le responsable recevra le rôle "responsable"</li>
                        <li>Les permissions seront configurées automatiquement</li>
                        <li>Le compte sera actif immédiatement</li>
                        <li>Le mot de passe pourra être modifié ultérieurement</li>
                    </ul>
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

    if (checkbox.checked) {
        passwordField.value = '12345678';
        passwordField.disabled = true;
    } else {
        passwordField.disabled = false;
        passwordField.focus();
    }
}
</script>
@endsection
