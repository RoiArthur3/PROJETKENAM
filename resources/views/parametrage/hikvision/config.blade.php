@extends('layouts.app')

@section('title', 'Configuration Hikvision')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="fas fa-cogs me-2"></i>Configuration Hikvision</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @php
                        // Sécurise la génération de la route pour éviter l'erreur si elle n'est pas chargée
                        $routeHikvisionConfigSave = Route::has('hikvision.config.save') ? route('hikvision.config.save') : url('/hikvision/config');
                    @endphp
                    <form method="POST" action="{{ $routeHikvisionConfigSave }}">
                        @csrf
                        <div class="mb-3">
                            <label for="hikvision_ip" class="form-label">Adresse IP</label>
                            <input type="text" class="form-control" id="hikvision_ip" name="hikvision_ip" value="{{ old('hikvision_ip', env('HIKVISION_IP', '192.168.1.70')) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="hikvision_port" class="form-label">Port</label>
                            <input type="number" class="form-control" id="hikvision_port" name="hikvision_port" value="{{ old('hikvision_port', env('HIKVISION_PORT', 80)) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="hikvision_username" class="form-label">Nom d'utilisateur</label>
                            <input type="text" class="form-control" id="hikvision_username" name="hikvision_username" value="{{ old('hikvision_username', env('HIKVISION_USER', 'admin')) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="hikvision_password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="hikvision_password" name="hikvision_password" value="{{ old('hikvision_password', env('HIKVISION_PASS', '')) }}" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="hikvision_enabled" name="hikvision_enabled" value="1" {{ old('hikvision_enabled', env('HIKVISION_ENABLED', false)) ? 'checked' : '' }}>
                            <label class="form-check-label" for="hikvision_enabled">Activer l'intégration Hikvision</label>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
