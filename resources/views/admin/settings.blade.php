@extends('layouts.app')

@section('title', 'Paramètres Système | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cog mr-2 text-primary"></i>Paramètres Système
            </h1>
            <p class="text-muted">Configuration générale de l'application</p>
        </div>
        <div class="col-auto d-flex gap-2">
            @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin'))
            <a href="{{ route('admin.paie.index') }}" class="btn btn-warning">
                <i class="fas fa-calculator mr-1"></i>Calcul de la paie
            </a>
            <a href="{{ route('admin.comptes.users.create') }}" class="btn btn-success" style="background-color: #28a745; border-color: #28a745;">
                <i class="fas fa-user-plus mr-1"></i>Créer un utilisateur
            </a>
            <a href="{{ route('admin.types-operations.create') }}" class="btn btn-primary">
                <i class="fas fa-cogs mr-1"></i>Créer Type d'Opération
            </a>
            <a href="{{ route('admin.services.create') }}" class="btn btn-info">
                <i class="fas fa-building mr-1"></i>Créer Service Opérationnel
            </a>
            @endif
            <button class="btn btn-success" style="background-color: #28a745; border-color: #28a745;" onclick="window.history.back()">
                <i class="fas fa-arrow-left mr-1"></i>Retour
            </button>
        </div>
    </div>

    <!-- Paramètres généraux -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building mr-2"></i>Informations de l'entreprise
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name">Nom de l'entreprise</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name"
                                           value="{{ $settings['company_name'] ?? 'KENAM Services' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_email">Email</label>
                                    <input type="email" class="form-control" id="company_email" name="company_email"
                                           value="{{ $settings['company_email'] ?? 'contact@kenam.ci' }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_phone">Téléphone</label>
                                    <input type="tel" class="form-control" id="company_phone" name="company_phone"
                                           value="{{ $settings['company_phone'] ?? '+225 XX XX XX XX XX' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_address">Adresse</label>
                                    <input type="text" class="form-control" id="company_address" name="company_address"
                                           value="{{ $settings['company_address'] ?? 'Adresse de l\'entreprise' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tax_rate">Taux de TVA (%)</label>
                                    <input type="number" step="0.1" class="form-control" id="tax_rate" name="tax_rate"
                                           value="{{ $settings['tax_rate'] ?? 18.0 }}" min="0" max="100">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="currency">Devise</label>
                                    <select class="form-control" id="currency" name="currency">
                                        <option value="FCFA" {{ ($settings['currency'] ?? 'FCFA') == 'FCFA' ? 'selected' : '' }}>FCFA</option>
                                        <option value="EUR" {{ ($settings['currency'] ?? 'FCFA') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="USD" {{ ($settings['currency'] ?? 'FCFA') == 'USD' ? 'selected' : '' }}>USD</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="working_hours">Heures de travail</label>
                                    <div class="input-group">
                                        <input type="time" class="form-control" id="working_hours_start" name="working_hours_start"
                                               value="{{ $settings['working_hours_start'] ?? '08:00' }}">
                                        <div class="input-group-append input-group-prepend">
                                            <span class="input-group-text">à</span>
                                        </div>
                                        <input type="time" class="form-control" id="working_hours_end" name="working_hours_end"
                                               value="{{ $settings['working_hours_end'] ?? '17:00' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Serveur Mail -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-envelope mr-2"></i>Configuration du Serveur Mail
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.mail') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_driver">Type de serveur</label>
                                    <select class="form-control" id="mail_driver" name="mail_driver">
                                        <option value="smtp" {{ ($mailSettings['driver'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="sendmail" {{ ($mailSettings['driver'] ?? 'smtp') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                        <option value="mailgun" {{ ($mailSettings['driver'] ?? 'smtp') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_host">Serveur SMTP</label>
                                    <input type="text" class="form-control" id="mail_host" name="mail_host"
                                           value="{{ $mailSettings['host'] ?? 'smtp.gmail.com' }}" placeholder="smtp.gmail.com">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mail_port">Port</label>
                                    <input type="number" class="form-control" id="mail_port" name="mail_port"
                                           value="{{ $mailSettings['port'] ?? 587 }}" placeholder="587">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mail_encryption">Chiffrement</label>
                                    <select class="form-control" id="mail_encryption" name="mail_encryption">
                                        <option value="tls" {{ ($mailSettings['encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ ($mailSettings['encryption'] ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="" {{ ($mailSettings['encryption'] ?? 'tls') == '' ? 'selected' : '' }}>Aucun</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mail_timeout">Timeout (secondes)</label>
                                    <input type="number" class="form-control" id="mail_timeout" name="mail_timeout"
                                           value="{{ $mailSettings['timeout'] ?? 30 }}" min="5" max="120">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_username">Nom d'utilisateur</label>
                                    <input type="text" class="form-control" id="mail_username" name="mail_username"
                                           value="{{ $mailSettings['username'] ?? '' }}" placeholder="votre-email@gmail.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_password">Mot de passe</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="mail_password" name="mail_password"
                                               value="{{ $mailSettings['password'] ?? '' }}" placeholder="••••••••">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                                <i class="fas fa-eye" id="toggleIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Pour Gmail, utilisez un mot de passe d'application</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_from_address">Adresse d'expédition</label>
                                    <input type="email" class="form-control" id="mail_from_address" name="mail_from_address"
                                           value="{{ $mailSettings['from_address'] ?? 'noreply@kenam.ci' }}" placeholder="noreply@kenam.ci">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_from_name">Nom d'expéditeur</label>
                                    <input type="text" class="form-control" id="mail_from_name" name="mail_from_name"
                                           value="{{ $mailSettings['from_name'] ?? 'KENAM Services' }}" placeholder="KENAM Services">
                                </div>
                            </div>
                        </div>

                        <div class="form-group d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Enregistrer la configuration mail
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="testMailConfig()">
                                <i class="fas fa-paper-plane mr-1"></i>Tester la connexion
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Services -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-sitemap mr-2"></i>Services
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Responsable</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $service)
                                <tr>
                                    <td>{{ $service->nom }}</td>
                                    <td>{{ $service->description ?? '-' }}</td>
                                    <td>{{ $service->responsable ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $service->actif ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $service->actif ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.services.edit', $service->id) }}"
                                           class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.services.destroy', $service->id) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Supprimer ce service ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('mail_password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

function testMailConfig() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Test en cours...';
    btn.disabled = true;

    fetch('{{ route("admin.settings.mail.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            mail_host: document.getElementById('mail_host').value,
            mail_port: document.getElementById('mail_port').value,
            mail_username: document.getElementById('mail_username').value,
            mail_password: document.getElementById('mail_password').value,
            mail_encryption: document.getElementById('mail_encryption').value,
            mail_from_address: document.getElementById('mail_from_address').value,
            mail_from_name: document.getElementById('mail_from_name').value
        })
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;

        if (data.success) {
            alert('✅ Connexion réussie ! Un email de test a été envoyé.');
        } else {
            alert('❌ Échec de la connexion : ' + data.message);
        }
    })
    .catch(error => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        alert('❌ Erreur : ' + error.message);
    });
}
</script>
@endsection
