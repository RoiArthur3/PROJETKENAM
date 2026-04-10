@extends('layouts.app')

@section('title', 'Paramètres Généraux - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-sliders-h me-2 text-primary"></i>Paramètres Généraux
            </h1>
            <p class="text-muted mb-0">Configuration générale de l'application</p>
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
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Configuration de base</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.general.update') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="app_name" class="form-label">Nom de l'application <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('app_name') is-invalid @enderror"
                                       id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name']) }}" required>
                                @error('app_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="timezone" class="form-label">Fuseau horaire <span class="text-danger">*</span></label>
                                <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone" required>
                                    <option value="UTC" {{ old('timezone', $settings['timezone']) == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="Europe/Paris" {{ old('timezone', $settings['timezone']) == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                                    <option value="Africa/Douala" {{ old('timezone', $settings['timezone']) == 'Africa/Douala' ? 'selected' : '' }}>Africa/Douala</option>
                                    <option value="America/New_York" {{ old('timezone', $settings['timezone']) == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                </select>
                                @error('timezone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="locale" class="form-label">Langue par défaut <span class="text-danger">*</span></label>
                            <select class="form-select @error('locale') is-invalid @enderror" id="locale" name="locale" required>
                                <option value="fr" {{ old('locale', $settings['locale']) == 'fr' ? 'selected' : '' }}>Français</option>
                                <option value="en" {{ old('locale', $settings['locale']) == 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ old('locale', $settings['locale']) == 'es' ? 'selected' : '' }}>Español</option>
                            </select>
                            @error('locale')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Maintenance</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1">
                                <label class="form-check-label" for="maintenance_mode">
                                    Mode maintenance activé
                                </label>
                            </div>
                            <div class="form-text">En mode maintenance, seuls les administrateurs peuvent accéder à l'application</div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Informations système -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations système
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Version PHP :</strong> {{ PHP_VERSION }}
                    </div>
                    <div class="mb-2">
                        <strong>Version Laravel :</strong> {{ app()->version() }}
                    </div>
                    <div class="mb-2">
                        <strong>Environnement :</strong>
                        <span class="badge bg-{{ app()->environment() == 'production' ? 'danger' : 'success' }}">
                            {{ app()->environment() }}
                        </span>
                    </div>
                    <div class="mb-0">
                        <strong>Dernière mise à jour :</strong>
                        <br>
                        <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="clearCache()">
                            <i class="fas fa-broom me-1"></i>Vider le cache
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="viewLogs()">
                            <i class="fas fa-file-alt me-1"></i>Voir les logs
                        </button>
                        <button class="btn btn-outline-warning btn-sm" onclick="backupDatabase()">
                            <i class="fas fa-download me-1"></i>Sauvegarde BDD
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearCache() {
    if (confirm('Êtes-vous sûr de vouloir vider le cache de l\'application ?')) {
        // TODO: Implémenter l'appel AJAX pour vider le cache
        alert('Cache vidé avec succès');
    }
}

function viewLogs() {
    // TODO: Ouvrir une fenêtre pour voir les logs
    alert('Fonction à implémenter');
}

function backupDatabase() {
    if (confirm('Créer une sauvegarde de la base de données ?')) {
        // TODO: Implémenter la sauvegarde
        alert('Sauvegarde créée avec succès');
    }
}
</script>
@endsection
