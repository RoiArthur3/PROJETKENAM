@extends('layouts.app')

@section('title', 'Modifier un Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="m-0">
                        <i class="fas fa-user-edit me-2"></i>
                        Modifier un Utilisateur
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.comptes.users.update', $user->id) }}" method="POST">
                        @csrf @method('PUT')

                        <!-- Nom et Email -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
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
                                       id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}" required>
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mot de passe (optionnel) -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" placeholder="Laisser vide pour ne pas changer">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirmation mot de passe et Rôle -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                       id="password_confirmation" name="password_confirmation" placeholder="Laisser vide pour ne pas changer">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="role" class="form-label">Rôle <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required onchange="updatePermissionsDisplay()">
                                    <option value="">Sélectionner un rôle</option>
                                    <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="moderator" {{ old('role', $user->role) == 'moderator' ? 'selected' : '' }}>Modérateur</option>
                                    <option value="agent" {{ old('role', $user->role) == 'agent' ? 'selected' : '' }}>Agent</option>
                                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Utilisateur</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="is_active" class="form-label">Statut</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input @error('is_active') is-invalid @enderror"
                                           type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Compte actif
                                    </label>
                                </div>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Permissions selon le rôle -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-shield-alt me-2"></i>Permissions du rôle sélectionné :</h6>
                                    <div id="role-permissions">
                                        @if($user->role === 'moderator' && !empty($userPermissions))
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="text-success">Modules autorisés:</h6>
                                                    <ul class="list-unstyled">
                                                        @php
                                                        $modules = [
                                                            'dashboard' => ['dashboard.access'],
                                                            'operations' => ['operations.access', 'operations.create', 'operations.edit', 'operations.delete', 'operations.validate'],
                                                            'validations' => ['validations.view', 'validations.approve', 'validations.reject'],
                                                            'rh' => ['rh.access', 'rh.employees', 'rh.contracts'],
                                                            'projects' => ['projects.access', 'projects.create', 'projects.edit', 'projects.delete'],
                                                            'commercial' => ['commercial.access', 'commercial.clients', 'commercial.contracts'],
                                                            'comptabilite' => ['comptabilite.access', 'comptabilite.invoices', 'comptabilite.payments'],
                                                            'tresorerie' => ['tresorerie.access', 'tresorerie.transactions', 'tresorerie.reports'],
                                                            'fournisseurs' => ['fournisseurs.access', 'fournisseurs.manage'],
                                                            'magasin' => ['magasin.access', 'magasin.inventory', 'magasin.movements'],
                                                            'entrepots' => ['entrepots.access', 'entrepots.manage'],
                                                            'materiel' => ['materiel.access', 'materiel.manage'],
                                                            'audit' => ['audit.access', 'audit.reports'],
                                                            'reporting' => ['reporting.access', 'reporting.view', 'reporting.export'],
                                                            'settings' => ['settings.access', 'settings.users', 'settings.roles']
                                                        ];

                                                        $accessibleModules = [];
                                                        foreach ($modules as $module => $requiredPermissions) {
                                                            foreach ($requiredPermissions as $permission) {
                                                                if (in_array($permission, $userPermissions)) {
                                                                    $accessibleModules[] = $module;
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                        $accessibleModules = array_unique($accessibleModules);
                                                        sort($accessibleModules);

                                                        foreach ($accessibleModules as $module) {
                                                            echo '<li><i class="fas fa-check-circle text-success me-2"></i>' . ucfirst($module) . '</li>';
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-danger">Modules non autorisés:</h6>
                                                    <ul class="list-unstyled">
                                                        @php
                                                        $allModules = ['dashboard', 'operations', 'validations', 'rh', 'projects', 'commercial', 'comptabilite', 'tresorerie', 'fournisseurs', 'magasin', 'entrepots', 'materiel', 'audit', 'reporting', 'settings'];
                                                        $inaccessibleModules = array_diff($allModules, $accessibleModules);

                                                        foreach ($inaccessibleModules as $module) {
                                                            echo '<li><i class="fas fa-times-circle text-danger me-2"></i>' . ucfirst($module) . '</li>';
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        @else
                                            <p class="mb-0">Sélectionnez un rôle pour voir les permissions associées</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions personnalisées (admin/moderator uniquement) -->
                        @if(in_array($user->role, ['admin', 'moderator']))
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-cogs me-2"></i>Modules autorisés pour ce {{ $user->role }}:</h6>
                                    <div class="row">
                                        @if($user->role === 'moderator')
                                            <!-- Modules du sidebar pour modérateur -->
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="operations" id="module-operations"
                                                           @if(in_array('operations', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-operations">
                                                        <i class="fas fa-cogs me-1"></i> Opérations
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="validations" id="module-validations"
                                                           @if(in_array('validations', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-validations">
                                                        <i class="fas fa-tasks me-1"></i> Suivi & Validation
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="rh" id="module-rh"
                                                           @if(in_array('rh', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-rh">
                                                        <i class="fas fa-users-cog me-1"></i> RH
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="projects" id="module-projects"
                                                           @if(in_array('projects', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-projects">
                                                        <i class="fas fa-project-diagram me-1"></i> Projets
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="commercial" id="module-commercial"
                                                           @if(in_array('commercial', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-commercial">
                                                        <i class="fas fa-briefcase me-1"></i> Commercial
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="comptabilite" id="module-comptabilite"
                                                           @if(in_array('comptabilite', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-comptabilite">
                                                        <i class="fas fa-calculator me-1"></i> Comptabilité
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="tresorerie" id="module-tresorerie"
                                                           @if(in_array('tresiserie', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-tresorerie">
                                                        <i class="fas fa-coins me-1"></i> Trésorerie
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="fournisseurs" id="module-fournisseurs"
                                                           @if(in_array('fournisseurs', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-fournisseurs">
                                                        <i class="fas fa-industry me-1"></i> Fournisseurs
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="magasin" id="module-magasin"
                                                           @if(in_array('magasin', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-magasin">
                                                        <i class="fas fa-warehouse me-1"></i> Magasin
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="entrepots" id="module-entrepots"
                                                           @if(in_array('entrepots', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-entrepots">
                                                        <i class="fas fa-building me-1"></i> Entrepôts
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="materiel" id="module-materiel"
                                                           @if(in_array('materiel', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-materiel">
                                                        <i class="fas fa-truck-loading me-1"></i> Matériel Roulant
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="audit" id="module-audit"
                                                           @if(in_array('audit', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-audit">
                                                        <i class="fas fa-clipboard-check me-1"></i> Checking (Audit)
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="reporting" id="module-reporting"
                                                           @if(in_array('reporting', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-reporting">
                                                        <i class="fas fa-chart-line me-1"></i> Reporting
                                                    </label>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Modules du sidebar pour admin -->
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="dashboard" id="module-dashboard"
                                                           @if(in_array('dashboard', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-dashboard">
                                                        <i class="fas fa-tachometer-alt me-1"></i> Tableau de bord
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="operations" id="module-operations"
                                                           @if(in_array('operations', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-operations">
                                                        <i class="fas fa-cogs me-1"></i> Opérations
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="validations" id="module-validations"
                                                           @if(in_array('validations', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-validations">
                                                        <i class="fas fa-tasks me-1"></i> Suivi & Validation
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="rh" id="module-rh"
                                                           @if(in_array('rh', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-rh">
                                                        <i class="fas fa-users-cog me-1"></i> RH
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="projects" id="module-projects"
                                                           @if(in_array('projects', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-projects">
                                                        <i class="fas fa-project-diagram me-1"></i> Projets
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="commercial" id="module-commercial"
                                                           @if(in_array('commercial', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-commercial">
                                                        <i class="fas fa-briefcase me-1"></i> Commercial
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="comptabilite" id="module-comptabilite"
                                                           @if(in_array('comptabilite', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-comptabilite">
                                                        <i class="fas fa-calculator me-1"></i> Comptabilité
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="tresorerie" id="module-tresorerie"
                                                           @if(in_array('tresorerie', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-tresorerie">
                                                        <i class="fas fa-coins me-1"></i> Trésorerie
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="fournisseurs" id="module-fournisseurs"
                                                           @if(in_array('fournisseurs', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-fournisseurs">
                                                        <i class="fas fa-industry me-1"></i> Fournisseurs
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="magasin" id="module-magasin"
                                                           @if(in_array('magasin', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-magasin">
                                                        <i class="fas fa-warehouse me-1"></i> Magasin
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="entrepots" id="module-entrepots"
                                                           @if(in_array('entrepots', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-entrepots">
                                                        <i class="fas fa-building me-1"></i> Entrepôts
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="materiel" id="module-materiel"
                                                           @if(in_array('materiel', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-materiel">
                                                        <i class="fas fa-truck-loading me-1"></i> Matériel Roulant
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="audit" id="module-audit"
                                                           @if(in_array('audit', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-audit">
                                                        <i class="fas fa-clipboard-check me-1"></i> Checking (Audit)
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="reporting" id="module-reporting"
                                                           @if(in_array('reporting', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-reporting">
                                                        <i class="fas fa-chart-line me-1"></i> Reporting
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="settings" id="module-settings"
                                                           @if(in_array('settings', $accessibleModules ?? [])) checked @endif>
                                                    <label class="form-check-label" for="module-settings">
                                                        <i class="fas fa-cogs me-1"></i> Paramètres
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        @if($user->role === 'moderator')
                                            Cochez les modules que ce modérateur peut utiliser. Le tableau de bord n'est pas disponible pour les modérateurs.
                                        @else
                                            Cochez les modules que cet administrateur peut utiliser.
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Boutons d'action -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                </button>
                                <a href="{{ route('admin.comptes.users.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript pour la gestion des permissions -->
    <script>
        function updatePermissionsDisplay() {
            const role = document.getElementById('role').value;
            const permissionsDiv = document.getElementById('role-permissions');

            if (role === 'moderator') {
                // Afficher les modules du modérateur
                permissionsDiv.innerHTML = '<p class="mb-0">Modules configurés ci-dessous</p>';
            } else {
                // Afficher les permissions par défaut pour les autres rôles
                const permissions = {
                    'superadmin': {
                        'dashboard': 'Accès au tableau de bord',
                        'operations': 'Gestion des opérations',
                        'validations': 'Validation des demandes',
                        'admin': 'Administration complète',
                        'reports': 'Accès aux rapports',
                        'settings': 'Paramètres système'
                    },
                    'admin': {
                        'dashboard': 'Accès au tableau de bord',
                        'operations': 'Gestion des opérations',
                        'validations': 'Validation des demandes',
                        'admin': 'Administration',
                        'reports': 'Accès aux rapports'
                    },
                    'agent': {
                        'operations': 'Création d\'opérations',
                        'dashboard': 'Tableau de bord personnel'
                    },
                    'user': {
                        'dashboard': 'Tableau de bord personnel'
                    }
                };

                if (permissions[role]) {
                    let html = '<div class="row">';
                    for (const [module, description] of Object.entries(permissions[role])) {
                        html += `<div class="col-md-6 mb-2"><i class="fas fa-check text-success me-2"></i><strong>${module}:</strong> ${description}</div>`;
                    }
                    html += '</div>';
                    permissionsDiv.innerHTML = html;
                } else {
                    permissionsDiv.innerHTML = '<p class="mb-0">Aucune permission spécifique pour ce rôle</p>';
                }
            }
        }

        // Initialiser l'affichage au chargement
        document.addEventListener('DOMContentLoaded', function() {
            updatePermissionsDisplay();
        });
    </script>
@endsection
