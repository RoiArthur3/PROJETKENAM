@extends('layouts.app')

@section('title', 'Créer un Modérateur | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-tie me-2 text-primary"></i>Créer un Modérateur
            </h1>
            <small class="text-muted">Créer un compte modérateur lié à un service (ex: compta@kenamservices.net, magasinier@kenamservices.net)</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.comptes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Retour à la liste
            </a>
        </div>
    </div>

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
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-user-plus me-2"></i>Informations du modérateur
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.comptes.moderateurs.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name') }}" required placeholder="Ex: Jean Dupont">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required placeholder="Ex: compta@kenamservices.net">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       name="phone" value="{{ old('phone') }}" required placeholder="Ex: +225 07 00 00 00 00">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Rôle</label>
                                <input type="text" class="form-control" value="Modérateur" disabled>
                                <small class="text-muted">Le rôle est automatiquement défini comme "moderator".</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Modules à gérer</label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" checked disabled id="mod_operations">
                                            <label class="form-check-label" for="mod_operations">
                                                Opérations (obligatoire)
                                            </label>
                                        </div>
                                    </div>

                                    @foreach($modules as $moduleKey => $moduleInfo)
                                        @if($moduleKey !== 'operations')
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="modules[]" value="{{ $moduleKey }}" id="mod_{{ $moduleKey }}"
                                                        {{ in_array($moduleKey, old('modules', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="mod_{{ $moduleKey }}">
                                                        {{ $moduleInfo['label'] ?? $moduleKey }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                @error('modules')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       name="password" required placeholder="Minimum 8 caractères">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                <input type="password" class="form-control"
                                       name="password_confirmation" required placeholder="Confirmez le mot de passe">
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.comptes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-success" style="background-color: #28a745; border-color: #28a745;">
                                <i class="fas fa-save me-2"></i>
                                Créer un utilisateur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
