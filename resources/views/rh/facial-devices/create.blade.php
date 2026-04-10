@extends('layouts.app')

@section('title', 'Ajouter un Terminal - RH')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus me-2 text-primary"></i>Ajouter un terminal Hikvision
            </h1>
            <p class="text-muted mb-0">Configurez un nouveau terminal pour le pointage facial automatique</p>
        </div>
        <div>
            <a href="{{ route('rh.facial-devices.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('rh.facial-devices.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nom du terminal *</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="serial_number" class="form-label">Numéro de série *</label>
                        <input type="text" name="serial_number" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror" value="{{ old('serial_number') }}" required>
                        @error('serial_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="ip_address" class="form-label">Adresse IP *</label>
                        <input type="text" name="ip_address" id="ip_address" class="form-control @error('ip_address') is-invalid @enderror" value="{{ old('ip_address') }}" placeholder="192.168.1.100" required>
                        @error('ip_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="port" class="form-label">Port</label>
                        <input type="number" name="port" id="port" class="form-control @error('port') is-invalid @enderror" value="{{ old('port', 80) }}" min="1" max="65535">
                        @error('port')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="protocol" class="form-label">Protocole *</label>
                        <select name="protocol" id="protocol" class="form-select @error('protocol') is-invalid @enderror" required>
                            <option value="http" {{ old('protocol', 'http') === 'http' ? 'selected' : '' }}>HTTP</option>
                            <option value="https" {{ old('protocol') === 'https' ? 'selected' : '' }}>HTTPS</option>
                        </select>
                        @error('protocol')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="admin">
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Actif
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                    <a href="{{ route('rh.facial-devices.index') }}" class="btn btn-outline-secondary ms-2">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
