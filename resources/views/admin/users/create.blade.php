@extends('layouts.app')

@section('title', 'Administration - Créer un utilisateur')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem;
        min-height: 38px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #e5e7eb;
        border: 1px solid #d1d5db;
        border-radius: 0.25rem;
        padding: 0 0.5rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        margin-right: 5px;
        color: #6b7280;
    }
    .form-label.required:after {
        content: " *";
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-plus me-2"></i>Créer un nouvel utilisateur
                    </h6>
                    <span class="badge bg-secondary">Création</span>
                </div>
                <div class="card-body">
                    <form id="userForm" method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Informations générales</h5>
                            </div>

                            <!-- Première ligne: Nom complet et Téléphone -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label required">Nom complet</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Ex: Jean Dupont" required value="{{ old('name') }}">
                                </div>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label required">Numéro de téléphone (pour connexion)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" placeholder="Ex: 0700000000" required value="{{ old('telephone') }}" pattern="[0-9]{10}" maxlength="10">
                                </div>
                                @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="form-text text-info"><i class="fas fa-info-circle me-1"></i>Format: 10 chiffres (ex: 0700000000)</small>
                            </div>

                            <!-- Deuxième ligne: Email et Rôle -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label required">Adresse email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="exemple@kenam.ci" required value="{{ old('email') }}">
                                </div>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Rôle</label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="role" id="role_moderator" value="moderator" {{ old('role', 'moderator') == 'moderator' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="role_moderator">
                                            <strong>Modérateur</strong> - Modules et sous-modules strictement cochés
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="role" id="role_admin" value="admin" {{ old('role') == 'admin' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_admin">
                                            <strong>Admin</strong> - Dashboard général + modules/sous-modules cochés
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="role" id="role_superadmin" value="superadmin" {{ old('role') == 'superadmin' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_superadmin">
                                            <strong>Super Admin</strong> - Accès à TOUT sans exception
                                        </label>
                                    </div>
                                </div>
                                <div class="form-text">Super Admin: accès total. Admin/Modérateur: accès strict via services, métiers, modules et sous-modules.</div>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- Information de connexion -->
                            <div class="col-12 mb-3">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Informations de connexion :</strong> L'utilisateur se connectera avec son numéro de téléphone et son mot de passe.
                                </div>
                            </div>

                            <!-- Services / métiers / modules + sous-modules (visible pour Admin et Modérateur) -->
                            <div class="col-12 mb-3" id="modulesSection" style="display: none;">
                                <label class="form-label">Services, métiers, modules et sous-modules autorisés</label>
                                <div class="alert alert-info small mb-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Choisissez d'abord le service, le métier ou le module, puis les sous-modules à administrer.
                                    Les permissions sont strictes pour Admin et Modérateur.
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle table-sm">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th width="28%">Service / métier / module</th>
                                                <th width="72%">Sous-modules à administrer</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($allModules as $moduleKey => $moduleData)
                                            <tr>
                                                <td class="bg-light">
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                               name="modules[]"
                                                               value="{{ $moduleKey }}"
                                                               class="form-check-input module-checkbox"
                                                               id="module_{{ $moduleKey }}"
                                                               {{ in_array($moduleKey, old('modules', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold" for="module_{{ $moduleKey }}">
                                                            <i class="{{ $moduleData['icon'] ?? 'fas fa-cube' }} me-1 text-primary"></i>
                                                            {{ $moduleData['name'] ?? $moduleKey }}
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="row g-1">
                                                        @foreach($moduleData['submodules'] ?? [] as $subKey => $subData)
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                       name="submodules[]"
                                                                       value="{{ $subKey }}"
                                                                       class="form-check-input submodule-checkbox"
                                                                       data-module="{{ $moduleKey }}"
                                                                       id="sub_{{ $subKey }}"
                                                                       {{ in_array($subKey, old('submodules', [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label small" for="sub_{{ $subKey }}">
                                                                    <i class="{{ $subData['icon'] ?? 'fas fa-dot-circle' }} me-1 text-muted"></i>
                                                                    {{ $subData['name'] ?? $subKey }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @error('modules')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                @error('submodules')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <!-- Troisième ligne: Mot de passe et Confirmation -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label required">Mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Mot de passe sécurisé" required>
                                </div>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label required">Confirmer le mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirmez le mot de passe" required>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-user-plus me-2"></i>Créer l'utilisateur
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialiser Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Afficher/masquer la section modules selon le rôle sélectionné
    function toggleModulesSection() {
        const selectedRole = $('input[name="role"]:checked').val();
        if (selectedRole === 'moderator' || selectedRole === 'moderateur' || selectedRole === 'admin') {
            $('#modulesSection').slideDown();
            if (selectedRole === 'admin') {
                // Admin: pré-cocher automatiquement tous les sous-modules des modules cochés.
                $('input[name="modules[]"]:checked').each(function() {
                    const moduleKey = $(this).val();
                    $('.submodule-checkbox[data-module="' + moduleKey + '"]').prop('checked', true);
                });
            }
        } else {
            $('#modulesSection').slideUp();
            $('input[name="modules[]"]').prop('checked', false);
            $('input[name="submodules[]"]').prop('checked', false);
        }
    }

    // Écouter les changements de rôle
    $('input[name="role"]').on('change', toggleModulesSection);

    // Initialiser l'affichage au chargement
    toggleModulesSection();

    // Validation simple du formulaire
    $('#userForm').on('submit', function(e) {
        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const phone = $('#telephone').val().trim();
        const password = $('#password').val().trim();
        const role = $('input[name="role"]:checked').val();

        // Validation du téléphone - doit être exactement 10 chiffres
        if (!/^[0-9]{10}$/.test(phone)) {
            e.preventDefault();
            alert('Le numéro de téléphone doit contenir exactement 10 chiffres (ex: 0700000000)');
            $('#telephone').focus();
            return false;
        }

        // Validation basique
        if (!name || !email || !password || !role) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return false;
        }

        // Validation email
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            e.preventDefault();
            alert('Veuillez entrer une adresse email valide.');
            $('#email').focus();
            return false;
        }

        // Validation mot de passe
        if (password.length < 8) {
            e.preventDefault();
            alert('Le mot de passe doit contenir au moins 8 caractères.');
            $('#password').focus();
            return false;
        }

        return true;
    });

    // Validation du téléphone en temps réel
    $('#telephone').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, ''); // Ne garder que les chiffres
        $(this).val(value);

        if (value.length > 10) {
            $(this).val(value.substring(0, 10));
        }

        // Indicateur visuel
        if (/^[0-9]{10}$/.test(value)) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else if (value.length > 0) {
            $(this).removeClass('is-valid').addClass('is-invalid');
        } else {
            $(this).removeClass('is-valid is-invalid');
        }
    });

    // Empêcher la saisie de caractères non numériques
    $('#telephone').on('keypress', function(e) {
        let charCode = (e.which) ? e.which : e.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            e.preventDefault();
            return false;
        }
        return true;
    });

    // Cocher le module parent si un sous-module est sélectionné.
    $('.submodule-checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            const moduleKey = $(this).data('module');
            $('#module_' + moduleKey).prop('checked', true);
        }
    });

    // Décocher les sous-modules si le module parent est décoché.
    $('.module-checkbox').on('change', function() {
        const selectedRole = $('input[name="role"]:checked').val();
        const moduleKey = $(this).val();

        if ($(this).is(':checked') && selectedRole === 'admin') {
            // Admin: quand module coché, tous ses sous-modules deviennent cochés.
            $('.submodule-checkbox[data-module="' + moduleKey + '"]').prop('checked', true);
            return;
        }

        if (!$(this).is(':checked')) {
            $('.submodule-checkbox[data-module="' + moduleKey + '"]').prop('checked', false);
        }
    });

    // Validation métier: pour modérateur, chaque module coché doit avoir au moins un sous-module coché.
    $('#userForm').on('submit', function(e) {
        const selectedRole = $('input[name="role"]:checked').val();
        if (!(selectedRole === 'moderator' || selectedRole === 'moderateur')) {
            return true;
        }

        let invalidModuleLabel = null;
        $('input[name="modules[]"]:checked').each(function() {
            const moduleKey = $(this).val();
            const checkedCount = $('.submodule-checkbox[data-module="' + moduleKey + '"]:checked').length;
            if (checkedCount === 0 && !invalidModuleLabel) {
                invalidModuleLabel = $(this).closest('.form-check').find('label').text().trim();
            }
        });

        if (invalidModuleLabel) {
            e.preventDefault();
            alert('Pour le rôle Modérateur, chaque module coché doit avoir au moins un sous-module sélectionné. Module concerné: ' + invalidModuleLabel);
            return false;
        }

        return true;
    });
});
</script>
@endpush
