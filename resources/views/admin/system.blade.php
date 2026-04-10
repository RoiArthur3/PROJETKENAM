@extends('layouts.app')

@section('title', 'Paramètres Système - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs me-2 text-primary"></i>Paramètres Système
            </h1>
            <p class="text-muted mb-0">Configuration générale de l'application</p>
        </div>
    </div>

    <!-- Cartes de paramétrage -->
    <div class="row">
        <!-- Paramètres Généraux -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-building me-2"></i>Informations Entreprise
                    </h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom de l'entreprise</label>
                            <input type="text" class="form-control" value="KENAM SERVICES" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Adresse</label>
                            <input type="text" class="form-control" placeholder="Adresse complète">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Téléphone</label>
                                <input type="tel" class="form-control" placeholder="+225 XX XX XX XX XX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" placeholder="contact@kenam.ci">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Paramètres Application -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-sliders-h me-2"></i>Configuration Application
                    </h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Devise par défaut</label>
                            <select class="form-select">
                                <option selected>FCFA (Franc CFA)</option>
                                <option>EUR (Euro)</option>
                                <option>USD (Dollar)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fuseau horaire</label>
                            <select class="form-select">
                                <option selected>Africa/Abidjan (GMT)</option>
                                <option>Africa/Lagos (GMT+1)</option>
                                <option>Europe/Paris (GMT+1)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Langue</label>
                            <select class="form-select">
                                <option selected>Français</option>
                                <option>English</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Paramètres avancés -->
    <div class="row">
        <!-- Types de requêtes -->
        <div class="col-lg-6 mb-4" id="types-operations">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-list-ul me-2"></i>Types de requêtes
                    </h6>
                </div>
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success py-2">{{ session('status') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger py-2">{{ session('error') }}</div>
                    @endif

                    <h6 class="fw-bold mb-3">Créer un type</h6>
                    <form method="POST" action="{{ route('admin.types.store') }}" class="row g-2 align-items-end mb-4">
                        @csrf
                        <div class="col-12 col-md-3">
                            <label class="form-label">Code</label>
                            <input type="text" name="code" class="form-control" placeholder="EX: OPV" required>
                        </div>
                        <div class="col-12 col-md-5">
                            <label class="form-label">Libellé</label>
                            <input type="text" name="libelle" class="form-control" placeholder="Ex: Opération de visite" required>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label">Couleur</label>
                            <input type="text" name="couleur" class="form-control" placeholder="#0d6efd">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label">Icône</label>
                            <input type="text" name="icone" class="form-control" placeholder="fa-tasks">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control" placeholder="Courte description (optionnel)">
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-plus me-1"></i>Ajouter
                            </button>
                        </div>
                    </form>

                    <h6 class="fw-bold mb-3">Types existants</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Libellé</th>
                                    <th>Statut</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($types ?? collect()) as $t)
                                    <tr>
                                        <td><code>{{ $t->code }}</code></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($t->couleur)
                                                    <span class="badge" style="background-color: {{ $t->couleur }}">&nbsp;</span>
                                                @endif
                                                <span>{{ $t->libelle }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $t->actif ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $t->actif ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('admin.types.toggle', $t) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-sm {{ $t->actif ? 'btn-outline-warning' : 'btn-outline-success' }}" title="Activer/Désactiver">
                                                    <i class="fas {{ $t->actif ? 'fa-pause' : 'fa-play' }}"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.types.destroy', $t) }}" class="d-inline" onsubmit="return confirm('Supprimer ce type ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Aucun type défini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-sitemap me-2"></i>Services
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Gérer les services et départements de l'entreprise.</p>
                    <a href="{{ route('admin.settings.services') }}" class="btn btn-warning w-100">
                        <i class="fas fa-cog me-2"></i>Configurer
                    </a>
                </div>
            </div>
        </div>

        <!-- Catégories Fournisseurs -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-tags me-2"></i>Catégories Fournisseurs
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Gérer les catégories de fournisseurs.</p>
                    <a href="{{ route('admin.settings.fournisseurs.categories') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-cog me-2"></i>Configurer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Paramètres Parc & Finances -->
    <div class="row">
        <!-- Types de Véhicules -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-truck me-2"></i>Types de Véhicules
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Gérer les types de véhicules du parc auto.</p>
                    <a href="{{ route('admin.settings.parc.types-vehicules') }}" class="btn btn-primary w-100">
                        <i class="fas fa-cog me-2"></i>Configurer
                    </a>
                </div>
            </div>
        </div>

        <!-- Centres de Coût -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-calculator me-2"></i>Centres de Coût
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Gérer les centres de coût pour la comptabilité analytique.</p>
                    <a href="{{ route('admin.settings.finances.centres-cout') }}" class="btn btn-success w-100">
                        <i class="fas fa-cog me-2"></i>Configurer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Paramètres Chauffeurs -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-dark text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-id-card me-2"></i>Chauffeurs
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Gérer les chauffeurs listés dans Parc > Affectations.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.utilisateurs.create', ['role' => 'chauffeur']) }}" class="btn btn-dark">
                            <i class="fas fa-plus me-2"></i>Ajouter un chauffeur
                        </a>
                        <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-outline-dark">
                            <i class="fas fa-users me-2"></i>Gérer les chauffeurs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Paramètres SMTP / Email -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-envelope me-2"></i>Configuration Email (SMTP)
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.smtp.update') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nom d'utilisateur (Email)</label>
                                <input type="email" name="mail_username" class="form-control" placeholder="votre-email@datasnf.net" value="{{ old('mail_username', config('mail.mailers.smtp.username')) }}" required>
                                <small class="text-muted">Votre adresse email complète</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Mot de passe</label>
                                <input type="password" name="mail_password" class="form-control" placeholder="••••••••" value="{{ old('mail_password') }}">
                                <small class="text-muted">Le mot de passe de votre compte email</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Hôte SMTP</label>
                                <input type="text" name="mail_host" class="form-control" value="{{ old('mail_host', config('mail.mailers.smtp.host', 'mail.datasnf.net')) }}" required>
                                <small class="text-muted">Exemple: mail.datasnf.net</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Port SMTP</label>
                                <select name="mail_port" class="form-select" required>
                                    <option value="465" {{ old('mail_port', config('mail.mailers.smtp.port')) == 465 ? 'selected' : '' }}>465 (SSL)</option>
                                    <option value="587" {{ old('mail_port', config('mail.mailers.smtp.port')) == 587 ? 'selected' : '' }}>587 (TLS)</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Chiffrement</label>
                                <select name="mail_encryption" class="form-select" required>
                                    <option value="ssl" {{ old('mail_encryption', config('mail.mailers.smtp.encryption')) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="tls" {{ old('mail_encryption', config('mail.mailers.smtp.encryption')) == 'tls' ? 'selected' : '' }}>TLS</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Adresse d'envoi</label>
                                <input type="email" name="mail_from_address" class="form-control" value="{{ old('mail_from_address', config('mail.from.address')) }}" required>
                                <small class="text-muted">Email affiché comme expéditeur</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nom d'envoi</label>
                                <input type="text" name="mail_from_name" class="form-control" value="{{ old('mail_from_name', config('mail.from.name', 'KENAM Services')) }}" required>
                                <small class="text-muted">Nom affiché comme expéditeur</small>
                            </div>
                        </div>

                        <input type="hidden" name="mail_mailer" value="smtp">

                        <div class="alert alert-info border-0">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>Informations de Configuration Mail DataSNF
                            </h6>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>📥 POP3</strong>
                                    <ul class="list-unstyled small mt-2">
                                        <li><strong>Hôte:</strong> mail.datasnf.net</li>
                                        <li><strong>Port:</strong> 995 (SSL) ou 110</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <strong>📧 IMAP</strong>
                                    <ul class="list-unstyled small mt-2">
                                        <li><strong>Hôte:</strong> mail.datasnf.net</li>
                                        <li><strong>Port:</strong> 993 (SSL) ou 143</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <strong>📤 SMTP</strong>
                                    <ul class="list-unstyled small mt-2">
                                        <li><strong>Hôte:</strong> mail.datasnf.net</li>
                                        <li><strong>Port:</strong> 465 (SSL) ou 587 (TLS)</li>
                                    </ul>
                                </div>
                            </div>
                            <hr>
                            <p class="mb-0 small">
                                <strong>⚠️ Important:</strong> Utilisez votre adresse email complète comme nom d'utilisateur et le mot de passe que vous avez défini pour votre compte email.
                            </p>
                        </div>

                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save me-2"></i>Enregistrer la configuration SMTP
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sauvegarde & Maintenance -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-database me-2"></i>Sauvegarde & Maintenance
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold">Sauvegarde Base de Données</h6>
                            <p class="text-muted small">Dernière sauvegarde: Jamais</p>
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download me-1"></i>Créer une sauvegarde
                            </button>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold">Cache Application</h6>
                            <p class="text-muted small">Vider le cache pour améliorer les performances</p>
                            <button class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-broom me-1"></i>Vider le cache
                            </button>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold">Logs Système</h6>
                            <p class="text-muted small">Consulter les logs d'erreurs et d'activité</p>
                            <button class="btn btn-outline-info btn-sm">
                                <i class="fas fa-file-alt me-1"></i>Voir les logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
