@extends('layouts.app')

@section('title', 'Configuration Système - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <a href="{{ route('parametrage.index') }}" class="text-gray-800 text-decoration-none">
                <i class="fas fa-arrow-left"></i> Paramétrage
            </a>
            <span class="mx-2">/</span>
            Configuration Système
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">Paramètres de l'Application</h6>
                </div>
                <div class="card-body">
                    <form id="systemConfigForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fuseau Horaire</label>
                                <select name="app_timezone" class="form-control">
                                    <option value="Africa/Porto-Novo" {{ ($config['app_timezone'] ?? 'Africa/Porto-Novo') == 'Africa/Porto-Novo' ? 'selected' : '' }}>Africa/Porto-Novo (Benin)</option>
                                    <option value="UTC" {{ ($config['app_timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="Europe/Paris" {{ ($config['app_timezone'] ?? '') == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Langue par défaut</label>
                                <select name="app_locale" class="form-control">
                                    <option value="fr" {{ ($config['app_locale'] ?? 'fr') == 'fr' ? 'selected' : '' }}>Français</option>
                                    <option value="en" {{ ($config['app_locale'] ?? '') == 'en' ? 'selected' : '' }}>English</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Durée Session (minutes)</label>
                                <input type="number" name="session_lifetime" class="form-control" value="{{ $config['session_lifetime'] ?? 120 }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Taille Max Upload (MB)</label>
                                <input type="number" name="max_file_size" class="form-control" value="{{ $config['max_file_size'] ?? 10 }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fréquence Sauvegarde</label>
                                <select name="backup_frequency" class="form-control">
                                    <option value="daily" {{ ($config['backup_frequency'] ?? 'daily') == 'daily' ? 'selected' : '' }}>Quotidienne</option>
                                    <option value="weekly" {{ ($config['backup_frequency'] ?? '') == 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                                    <option value="monthly" {{ ($config['backup_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>Mensuelle</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="debugMode" name="debug_mode" value="1" {{ ($config['debug_mode'] ?? false) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="debugMode">Mode Debug</label>
                                    <small class="form-text text-muted">Afficher les erreurs détaillées (Développement uniquement).</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="maintenanceMode" name="maintenance_mode" value="1" {{ ($config['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="maintenanceMode">Mode Maintenance</label>
                                    <small class="form-text text-muted">Rendre le site inaccessible aux utilisateurs standards.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="button" class="btn btn-secondary" onclick="saveSystemConfig()">
                                <i class="fas fa-save me-2"></i> Enregistrer les paramètres
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
             <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Information Importante</h6>
                </div>
                <div class="card-body">
                    <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Attention</p>
                    <p>Modifier ces paramètres peut affecter la stabilité de l'application.</p>
                    <p>Le mode maintenance déconnectera tous les utilisateurs non-administrateurs.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function saveSystemConfig() {
    const form = document.getElementById('systemConfigForm');
    const formData = new FormData(form);
    
    // Handle checkboxes manually if unchecked (default behavior of FormData doesn't include unchecked boxes)
    if(!document.getElementById('debugMode').checked) formData.append('debug_mode', 0);
    if(!document.getElementById('maintenanceMode').checked) formData.append('maintenance_mode', 0);

    // Convert booleans for Laravel validation 'boolean' rule
    // formData.set('debug_mode', formData.get('debug_mode') ? '1' : '0'); 
    // Actually checkbox value '1' is fine if checked, but needs handling if unchecked.
    // The previous lines handled existing check logic.

    fetch('{{ route("parametrage.systeme.save") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert('Erreur: ' + JSON.stringify(data.errors || data.message));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur technique lors de la sauvegarde.');
    });
}
</script>
@endpush
@endsection
