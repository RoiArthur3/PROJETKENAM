@extends('layouts.app')

@section('title', 'Paramètres Système | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-server mr-2 text-primary"></i>Paramètres Système
            </h1>
            <p class="text-muted">Configuration système et notifications</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Retour aux paramètres
            </a>
        </div>
    </div>

    <!-- Alertes système -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Information:</strong> Cette section est réservée aux superadministrateurs pour la configuration avancée du système.
            </div>
        </div>
    </div>

    <!-- Configuration des notifications -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-envelope mr-2"></i>Templates de notifications
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.systeme.notifications.templates.update') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email_template">Template email par défaut</label>
                            <textarea class="form-control" id="email_template" name="email_template" rows="4"
                                      placeholder="Template pour les emails système">{{ config('notifications.email_template', '') }}</textarea>
                            <small class="form-text text-muted">Variables disponibles: {user_name}, {service_name}, {date}</small>
                        </div>
                        <div class="form-group">
                            <label for="sms_template">Template SMS par défaut</label>
                            <textarea class="form-control" id="sms_template" name="sms_template" rowsiges="3"
                                      placeholder="Template pour les SMS">{{ config('notifications.sms_template', '') }}</textarea>
                            <small class="form-text text-muted">Variables disponibles: {user_name}, {service_name}</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Enregistrer的各项
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-bell mr-2"></i>Canaux de notification
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.systeme.notifications.channels.update') }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" name="email_enabled"
                                       value="1" {{ config('notifications.email_enabled', true) ? 'checked' : '' }}>
                                <span class="form-check-label">Activer les notifications par email</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" name="sms_enabled"
                                       value="1" {{ config('notifications.sms_enabled', false) ? 'checked' : '' }}>
                                <span class="form-check-label">Activer les notifications par SMS</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" name="push_enabled"
                                       value="1" {{ config('notifications.push_enabled', false) ? 'checked' : '' }}>
                                <span class="form-check-label">Activer les notifications push</span>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-1"></i>Enregistrer les canaux
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration SMTP et SMS -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-paper-plane mr-2"></i>Configuration SMTP
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.systeme.smtp.update') }}">
                        @csrf
                        <div class="form-group">
                            <label for="smtp_host">Hôte SMTP</label>
                            <input type="text" class="form-control" id="smtp_host" name="smtp_host"
                                   value="{{ config('mail.mailers.smtp.host', '') }}" placeholder="smtp.example.com">
                        </div>
                        <div class="form-group">
                            <label for="smtp_port">Port SMTP</label>
                            <input type="number" class="form-control" id="smtp_port" name="smtp_port"
                                   value="{{ config('mail.mailers.smtp.port', 587) }}" placeholder="587">
                        </div>
                        <div class="form-group">
                            <label for="smtp_username">Nom d'utilisateur</label>
                            <input type="text" class="form-control" id="smtp_username" name="smtp_username"
                                   value="{{ config('mail.mailers.smtp.username', '') }}" placeholder="email@example.com">
                        </div>
                        <div class="form-group">
                            <label for="smtp_password">Mot de passe</label>
                            <input type="password" class="form-control" id="smtp_password" name="smtp_password"
                                   placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" name="smtp_encryption"
                                       value="tls" {{ config('mail.mailers.smtp.encryption', 'tls') === 'tls' ? 'checked' : '' }}>
                                <span class="form-check-label">Utiliser TLS</span>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i>Enregistrer SMTP
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-mobile-alt mr impressed="2"></i>Configuration SMS
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.systeme.sms.update') }}">
                        @csrf
                        <div class="form-group">
                            <label for="sms_provider">Fournisseur SMS</label>
                            <select class="form-control" id="sms_provider" name="sms_provider">
                                <option value="twilio" {{ config('sms.provider', 'twilio') === 'twilio' ? 'selected' : '' }}>Twilio</option>
                                <option value="nexmo" {{ config('sms.provider', 'twilio') === 'nexmo' ? 'selected' : '' }}>Nexmo</option>
                                <option value="africastalking" {{ config('sms.provider', 'twilio') === 'africastalking' ? 'selected' : '' }}>Africa's Talking</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sms_api_key">Clé API</label>
                            <input type="text" class="form-control" id="sms_api_key" name="sms_api_key"
                                   value="{{ config('sms.api_key', '') }}" placeholder="Clé API du fournisseur">
                        </div>
                        <div class="form-group">
                            <label for="sms_secret">Secret</label>
                            <input type="password" class="form-control" id="sms_secret" name="sms_secret"
                                   placeholder="Secret API">
                        </div>
                        <div class="form-group">
                            <label for="sms_from">Numéro expéditeur</label>
                            <input type="text" class="form-control" id="sms_from" name="sms_from"
                                   value="{{ config('sms.from', '') }}" placeholder="+225XXXXXXXXX">
                        </div>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save mr-1"></i>Enregistrer SMS
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Préférences par rôle -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">
                        <i class="fas fa-user-cog mr-2"></i>Préférences de notification par rôle
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.systeme.notifications.roles.update') }}">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Rôle</th>
                                        <th>Email</th>
                                        <th>SMS</th>
                                        <th>Push</th>
                                        <th>Types d'alertes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Superadmin</strong></td>
                                        <td><input type="checkbox" name="roles[superadmin][email]" checked></td>
                                        <td><input type="checkbox" name="roles[superadmin][sms]" checked></td>
                                        <td><input type="checkbox" name="roles[superadmin][push]" checked></td>
                                        <td>
                                            <select name="roles[superadmin][alerts][]" multiple class="form-control form-control-sm">
                                                <option value="system" selected>Système</option>
                                                <option value="security" selected>Sécur favour</option>
                                                <option value="performance">Performance</option>
                                                <gestion value="backup">Sauvegarde</option>
                                            </select>
                                        </td podcast
                                        <td><strong>Admin</strong></td>
                                        <td><input type="checkbox" name="roles[admin][email并发 checked></td>
                                        <td><input type="checkbox" name="roles[admin][sms]"></td>
                                        <td><udio type="checkbox" name="roles[admin][kw]"></td>
                                        <td>
                                           odon type="checkbox" name="roles[威尔][alerts][]" multiple class="form-control form-control-sm">
并发
                                                <option value="user_management">exo selected>Gestion utilisateurs</option>
                                                <option value="permissions">Permissions</option>
                                                <option value="service_changes">Changements services</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Manager</strong></td>
                                        <td><input type="checkbox" name="roles[manager][email]" checked></td>
                                        <td><input type="checkbox" name="roles[manager][sms]"></td>
                                        <td><input type="checkbox" name="roles[manager][push]"></td>
                                        <td>
                                            <select name="roles[manager][alerts][]" multiple class="form-control form-control-sm">
                                                <option value="operations" selected>Opérations</option>
                                                <option value="team">Équipe</option>
                                                <option value="deadlines">Délais</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-save mr-1"></i>Enregistrer les préférences
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
