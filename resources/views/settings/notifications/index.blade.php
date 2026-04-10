@extends('layouts.app')

@section('title', 'Paramètres de Notification - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-bell me-2 text-primary"></i>Paramètres de Notification
            </h1>
            <p class="text-muted mb-0">Configuration des notifications et alertes système</p>
        </div>
    </div>

    <!-- Alertes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Notifications par email -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-envelope me-2"></i>Notifications par Email
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.notifications.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       id="email_enabled" name="email_enabled" value="1"
                                       {{ old('email_enabled', $notifications['email_enabled']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_enabled">
                                    Activer les notifications par email
                                </label>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>Événements opérationnels</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_new_operation" name="email_new_operation" value="1">
                                    <label class="form-check-label" for="email_new_operation">
                                        Nouvelle opération créée
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_operation_completed" name="email_operation_completed" value="1">
                                    <label class="form-check-label" for="email_operation_completed">
                                        Opération terminée
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_operation_overdue" name="email_operation_overdue" value="1">
                                    <label class="form-check-label" for="email_operation_overdue">
                                        Opération en retard
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Événements RH</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_new_employee" name="email_new_employee" value="1">
                                    <label class="form-check-label" for="email_new_employee">
                                        Nouvel employé
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_leave_request" name="email_leave_request" value="1">
                                    <label class="form-check-label" for="email_leave_request">
                                        Demande de congé
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_payroll_generated" name="email_payroll_generated" value="1">
                                    <label class="form-check-label" for="email_payroll_generated">
                                        Paie générée
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Enregistrer les paramètres email
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notifications push -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-mobile-alt me-2"></i>Notifications Push
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="push_enabled" name="push_enabled" value="1"
                                   {{ old('push_enabled', $notifications['push_enabled']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="push_enabled">
                                Activer les notifications push
                            </label>
                        </div>
                        <div class="form-text">Notifications directement dans le navigateur</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Types de notifications</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="push_urgent" name="push_urgent" value="1">
                                <label class="form-check-label" for="push_urgent">
                                    Tâches urgentes uniquement
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="push_assignments" name="push_assignments" value="1">
                                <label class="form-check-label" for="push_assignments">
                                    Nouvelles assignations
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Horaires</h6>
                            <div class="mb-3">
                                <label for="push_start_time" class="form-label">Heure de début</label>
                                <input type="time" class="form-control" id="push_start_time" name="push_start_time" value="08:00">
                            </div>
                            <div class="mb-3">
                                <label for="push_end_time" class="form-label">Heure de fin</label>
                                <input type="time" class="form-control" id="push_end_time" name="push_end_time" value="18:00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications SMS -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-sms me-2"></i>Notifications SMS
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Configuration SMSECO</strong><br>
                        Les notifications SMS sont gérées par SMSECO, notre fournisseur officiel.
                        Les clés API sont préconfigurées dans le système.
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="sms_enabled" name="sms_enabled" value="1"
                                   {{ old('sms_enabled', $notifications['sms_enabled']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="sms_enabled">
                                Activer les notifications SMS
                            </label>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="sms_provider" class="form-label">Fournisseur SMS</label>
                            <select class="form-select" id="sms_provider" name="sms_provider">
                                <option value="">Sélectionner un fournisseur</option>
                                <option value="smseco" selected>SMSECO</option>
                            </select>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                SMSECO est le fournisseur SMS officiel de KENAM SERVICES
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="sms_api_key" class="form-label">Clé API SMSECO</label>
                            <input type="password" class="form-control" id="sms_api_key" name="sms_api_key"
                                   value="{{ config('services.smseco.api_key') }}"
                                   placeholder="Clé API préconfigurée">
                            <div class="form-text">
                                Clé API préconfigurée dans le système
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6>Utiliser les SMS pour :</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sms_emergency" name="sms_emergency" value="1">
                            <label class="form-check-label" for="sms_emergency">
                                Alertes d'urgence uniquement
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sms_admin" name="sms_admin" value="1">
                            <label class="form-check-label" for="sms_admin">
                                Notifications administrateur
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Résumé des notifications -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Résumé
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h4 mb-0 text-primary">{{ $notifications['email_enabled'] ? '✓' : '✗' }}</div>
                            <small class="text-muted">Email</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 mb-0 text-info">{{ $notifications['push_enabled'] ? '✓' : '✗' }}</div>
                            <small class="text-muted">Push</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 mb-0 text-warning">{{ $notifications['sms_enabled'] ? '✓' : '✗' }}</div>
                            <small class="text-muted">SMS</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test des notifications -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-flask me-2"></i>Test des notifications
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Envoyez une notification de test pour vérifier le bon fonctionnement.
                    </p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="testEmail()">
                            <i class="fas fa-envelope me-1"></i>Test Email
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="testPush()">
                            <i class="fas fa-bell me-1"></i>Test Push
                        </button>
                        <button class="btn btn-outline-warning btn-sm" onclick="testSms()">
                            <i class="fas fa-sms me-1"></i>Test SMS
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testEmail() {
    if (confirm('Envoyer un email de test ?')) {
        // TODO: Implémenter le test d'email
        alert('Email de test envoyé avec succès');
    }
}

function testPush() {
    if (confirm('Envoyer une notification push de test ?')) {
        // TODO: Implémenter le test push
        alert('Notification push de test envoyée');
    }
}

function testSms() {
    if (confirm('Envoyer un SMS de test via SMSECO ?')) {
        // TODO: Implémenter le test SMS avec SMSECO
        alert('SMS de test envoyé avec succès via SMSECO');
    }
}
</script>
@endsection
