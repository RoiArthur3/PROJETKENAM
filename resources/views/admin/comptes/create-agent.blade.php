@extends('layouts.app')

@section('title', 'Créer un Agent | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Créer un Agent</h5>
            <a href="{{ route('admin.comptes.index') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left me-1"></i>Retour</a>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Accès limité :</strong> L'agent pourra créer des requêtes d'opération et suivre leur validation.
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

            <form method="POST" action="{{ route('admin.comptes.agents.store') }}" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label small">Nom complet <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           name="name" value="{{ old('name') }}" required placeholder="Ex: Jean Dupont">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required placeholder="Ex: agent@kenamservices.net">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Téléphone <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                           name="phone" value="{{ old('phone') }}" required placeholder="Ex: +225 07 00 00 00 00">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Service <span class="text-danger">*</span></label>
                    <select class="form-select @error('service_id') is-invalid @enderror" name="service_id" required>
                        <option value="">Sélectionner un service</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Rôle</label>
                    <input type="text" class="form-control bg-light" value="Agent" disabled>
                    <small class="text-muted">Le rôle est automatiquement défini comme "agent".</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Accès</label>
                    <input type="text" class="form-control bg-light" value="Requêtes (opérations)" disabled>
                    <small class="text-muted">L'agent peut créer des requêtes et voir leur validation.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Mot de passe <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required placeholder="Minimum 8 caractères">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Confirmer le mot de passe <span class="text-danger">*</span></label>
                    <input type="password" class="form-control"
                           name="password_confirmation" required placeholder="Confirmez le mot de passe">
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('admin.comptes.index') }}" class="btn btn-light">
                        <i class="fas fa-times me-1"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Créer l'Agent
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
